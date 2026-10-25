<?php

/**
 * Contributor role and admin perimeter, ported from LCDS.
 *
 * A contributor only sees what they have to do: pages, media, menus, the
 * customizer, the site settings and their own profile. Everything else is
 * removed from the menu AND refused on access — hiding an entry protects
 * nothing, the URL stays typeable. See readme/roles.md.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

const OPTELYA_CONTRIBUTOR_ROLE = 'optelya_contributor';

/**
 * Opens Settings → Configuration. A DEDICATED capability, not
 * `manage_options`: that one opens the seven core Settings screens (Reading
 * picks the homepage, Permalinks…) and options.php, the raw editor of every
 * option — it would make a contributor an administrator.
 */
const OPTELYA_SETTINGS_CAP = 'optelya_manage_settings';

/**
 * Bump to replay the role definition on every environment after a change.
 */
const OPTELYA_ROLE_VERSION = 1;
const OPTELYA_ROLE_OPTION = 'optelya_role_version';

/**
 * Editor capabilities (no manage_options, activate_plugins nor switch_themes),
 * plus menus/customizer and the site settings. `edit_theme_options` also opens
 * the site editor and the font library: the screen guard refuses them.
 */
function optelya_register_contributor_role(): void
{
    if ((int) get_option(OPTELYA_ROLE_OPTION, 0) >= OPTELYA_ROLE_VERSION) {
        return;
    }

    $editor = get_role('editor');

    if (! $editor instanceof WP_Role) {
        return;
    }

    $capabilities = array_filter($editor->capabilities);
    $capabilities['edit_theme_options'] = true;
    $capabilities[OPTELYA_SETTINGS_CAP] = true;

    remove_role(OPTELYA_CONTRIBUTOR_ROLE);
    add_role(OPTELYA_CONTRIBUTOR_ROLE, __('Contributeur Optelya', 'optelya'), $capabilities);

    $administrator = get_role('administrator');

    if ($administrator instanceof WP_Role) {
        $administrator->add_cap(OPTELYA_SETTINGS_CAP);
    }

    update_option(OPTELYA_ROLE_OPTION, OPTELYA_ROLE_VERSION);
}
add_action('admin_init', 'optelya_register_contributor_role');

/**
 * The perimeter follows the ROLE, not a missing capability: an administrator
 * who lost a capability by accident must not be locked in.
 */
function optelya_is_restricted_user(): bool
{
    $user = wp_get_current_user();

    return $user->exists() && in_array(OPTELYA_CONTRIBUTOR_ROLE, (array) $user->roles, true);
}

function optelya_trim_admin_menu(): void
{
    if (! optelya_is_restricted_user()) {
        return;
    }

    foreach (OptelyaAdminScreen::forbiddenMenus() as $slug) {
        remove_menu_page($slug);
    }

    foreach (OptelyaAdminScreen::forbiddenSubmenus() as [$parent, $slug]) {
        remove_submenu_page($parent, $slug);
    }
}
add_action('admin_menu', 'optelya_trim_admin_menu', 999);

function optelya_guard_admin_screens(): void
{
    if (wp_doing_ajax() || ! optelya_is_restricted_user()) {
        return;
    }

    global $pagenow;

    if (OptelyaAdminScreen::isAllowed((string) $pagenow, $_GET)) {
        return;
    }

    wp_die(
        esc_html__('Vous n’avez pas accès à cette page.', 'optelya'),
        esc_html__('Accès refusé', 'optelya'),
        ['response' => 403, 'back_link' => true],
    );
}
add_action('admin_init', 'optelya_guard_admin_screens', 1);

/**
 * Dashboard reduced to "At a glance", for everyone: the other boxes talk about
 * WordPress, not about the site.
 */
function optelya_trim_dashboard(): void
{
    global $wp_meta_boxes;

    foreach (['normal', 'side', 'column3', 'column4'] as $context) {
        foreach (['high', 'core', 'default', 'low'] as $priority) {
            foreach (array_keys($wp_meta_boxes['dashboard'][$context][$priority] ?? []) as $boxId) {
                if ($boxId !== 'dashboard_right_now') {
                    unset($wp_meta_boxes['dashboard'][$context][$priority][$boxId]);
                }
            }
        }
    }
}
add_action('wp_dashboard_setup', 'optelya_trim_dashboard', 999);

/**
 * Two assignable roles: administrator and contributor. The others stay
 * DECLARED — removing them would break an account holding one.
 */
function optelya_assignable_roles(array $roles): array
{
    $kept = ['administrator' => true, OPTELYA_CONTRIBUTOR_ROLE => true];

    // The EDITED account's role stays listed: without its option, the browser
    // selects another one and saving changes the role nobody asked to change.
    foreach (optelya_edited_user_roles() as $role) {
        $kept[$role] = true;
    }

    return array_intersect_key($roles, $kept);
}
add_filter('editable_roles', 'optelya_assignable_roles');

/**
 * @return list<string>
 */
function optelya_edited_user_roles(): array
{
    $requested = $_REQUEST['user_id'] ?? null;
    $user = is_scalar($requested) ? get_userdata(absint($requested)) : false;

    return $user instanceof WP_User ? array_values((array) $user->roles) : [];
}

/**
 * "My account" reduced, on one's OWN profile only (load-profile.php, not
 * user-edit.php): an administrator editing a contributor keeps the full form.
 */
function optelya_trim_profile(): void
{
    if (! optelya_is_restricted_user()) {
        return;
    }

    add_filter('user_contactmethods', '__return_empty_array');
    add_filter('additional_capabilities_display', '__return_false');
    add_filter('wp_is_application_passwords_available_for_user', '__return_false');

    // Sections added by plugins on every profile.
    remove_all_actions('personal_options');
    remove_all_actions('profile_personal_options');
    remove_all_actions('show_user_profile');

    add_action('admin_head', 'optelya_profile_styles');
}
add_action('load-profile.php', 'optelya_trim_profile');

/**
 * Hides the rows out of scope. STYLING, not a barrier: these are the account's
 * own fields, which a contributor may change. What guards anything is the role
 * and the screen guard.
 */
function optelya_profile_styles(): void
{
    $rows = implode(",\n", array_map(
        static fn(string $class): string => '#your-profile .' . $class,
        OptelyaProfileField::hiddenRows(),
    ));

    // A separate rule: a browser ignoring `:has()` drops the WHOLE selector
    // list. "About yourself" has no container: it is targeted by its content.
    $sections = "#your-profile h2:has(+ table .user-description-wrap),\n"
        . '#your-profile table:has(.user-description-wrap)';

    printf(
        '<style>%s { display: none; } %s { display: none; } #your-profile .application-passwords { display: none; }</style>',
        $rows,
        $sections,
    );
}

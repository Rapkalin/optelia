<?php

/**
 * Front-end security hardening.
 *
 * Removes WordPress core output that widens the attack surface or forces a
 * looser Content-Security-Policy. The security HEADERS themselves (CSP,
 * nosniff, …) are set at the web-server level in website/.htaccess — see
 * readme/securite.md.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Disable the WordPress emoji feature (inline script + styles + related hooks).
 *
 * The emoji detection script is injected INLINE in <head> by core; dropping it
 * is what lets `script-src 'self'` stay strict, and it removes the runtime
 * request to s.w.org.
 */
function optelya_disable_emojis(): void
{
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

    add_filter('tiny_mce_plugins', 'optelya_disable_emojis_tinymce');
    add_filter('wp_resource_hints', 'optelya_disable_emojis_dns_prefetch', 10, 2);
}
add_action('init', 'optelya_disable_emojis');

/**
 * Remove the emoji plugin from the TinyMCE editor.
 */
function optelya_disable_emojis_tinymce(array $plugins): array
{
    return array_diff($plugins, ['wpemoji']);
}

/**
 * Drop the emoji CDN (s.w.org) DNS-prefetch hint.
 */
function optelya_disable_emojis_dns_prefetch(array $urls, string $relation_type): array
{
    if ($relation_type === 'dns-prefetch') {
        $emoji_svg_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/');
        $urls = array_filter($urls, static fn($url) => strpos((string) $url, $emoji_svg_url) === false);
    }

    return $urls;
}

/**
 * Stop advertising the exact WordPress version in <meta name="generator"> and
 * the feeds. The ?ver= of core assets (admin and login) is kept on purpose:
 * without it, a core update would leave month-old cached admin styles.
 */
function optelya_remove_version_disclosure(): void
{
    remove_action('wp_head', 'wp_generator');
    add_filter('the_generator', '__return_empty_string');
}
add_action('init', 'optelya_remove_version_disclosure');

/**
 * Remove the WLW / RSD discovery links: legacy publishing endpoints nobody uses,
 * which only advertise XML-RPC.
 */
function optelya_remove_legacy_head_links(): void
{
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
}
add_action('init', 'optelya_remove_legacy_head_links');

/**
 * Disable XML-RPC. It is the classic brute-force and pingback-amplification
 * entry point, and nothing in this site uses it (no Jetpack, no remote editor).
 */
add_filter('xmlrpc_enabled', '__return_false');

/**
 * Block author enumeration: ?author=N and /author/<login>/ both answer 404,
 * whether the account exists or not. The site has no author archive; a
 * redirect for known accounts and a 404 for unknown ones would still tell them
 * apart.
 */
function optelya_block_author_enumeration(): void
{
    if (get_query_var('author') === '' && get_query_var('author_name') === '') {
        return;
    }

    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    nocache_headers();
}
// Priority 1: core's redirect_canonical() runs at 10 and would first send
// ?author=1 to /author/<login>/, leaking the login.
add_action('template_redirect', 'optelya_block_author_enumeration', 1);

/**
 * Close the REST users endpoint to anonymous callers — the JSON equivalent of
 * the ?author=N leak above.
 *
 * @param mixed $result Short-circuit value; non-null replaces the response.
 */
function optelya_restrict_rest_users(mixed $result, \WP_REST_Server $server, \WP_REST_Request $request): mixed
{
    if ($result !== null) {
        return $result;
    }

    if (! is_user_logged_in() && str_starts_with((string) $request->get_route(), '/wp/v2/users')) {
        return new \WP_Error(
            'rest_user_cannot_view',
            __('Authentification requise.', 'optelya'),
            ['status' => rest_authorization_required_code()],
        );
    }

    return $result;
}
add_filter('rest_pre_dispatch', 'optelya_restrict_rest_users', 10, 3);

/**
 * Return the same message whether the user or the password was wrong, so the
 * login form stops confirming which accounts exist.
 */
function optelya_generic_login_error(): string
{
    return __('Identifiants incorrects.', 'optelya');
}
add_filter('login_errors', 'optelya_generic_login_error');

<?php

/**
 * Site header: logo (or the site name without one), navigation from the
 * "En-tête" menu, button from the site settings (Settings → Configuration).
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

$logoId = optelya_attachment_id(optelya_option(OptelyaSetting::Logo));
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main-content"><?php esc_html_e('Aller au contenu', 'optelya'); ?></a>

<header class="site-header" data-site-header>
    <div class="site-header__inner">
        <a class="brand site-header__brand" href="<?php echo esc_url(home_url('/')); ?>" rel="home">
            <?php if ($logoId > 0) : ?>
                <?php echo wp_get_attachment_image($logoId, 'medium', false, ['class' => 'site-header__logo', 'alt' => get_bloginfo('name'), 'loading' => 'eager', 'decoding' => 'async']); ?>
            <?php else : ?>
                <?php bloginfo('name'); ?>
            <?php endif; ?>
        </a>

        <?php /* Shown by assets/scripts/nav-toggle.js: without it, the menu stays unfolded. */ ?>
        <button class="site-header__toggle" type="button" aria-expanded="false" aria-controls="site-nav" data-nav-toggle>
            <span class="site-header__toggle-bars" aria-hidden="true"></span>
            <span class="visually-hidden"><?php esc_html_e('Menu', 'optelya'); ?></span>
        </button>

        <nav id="site-nav" class="site-header__nav" aria-label="<?php esc_attr_e('Navigation principale', 'optelya'); ?>">
            <?php get_template_part('components/menu-links', null, ['location' => OptelyaMenuLocation::Header]); ?>
        </nav>

        <?php get_template_part('components/link', null, ['link' => optelya_link(optelya_option(OptelyaSetting::HeaderCta)), 'class' => 'button button--small']); ?>
    </div>
</header>

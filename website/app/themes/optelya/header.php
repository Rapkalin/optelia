<?php

/**
 * Site header: navigation from the "En-tête" menu, phone and button from the
 * site settings (Settings → Configuration).
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}
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

<header class="site-header">
    <div class="site-header__inner">
        <a class="brand site-header__brand" href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a>

        <nav class="site-header__nav" aria-label="<?php esc_attr_e('Navigation principale', 'optelya'); ?>">
            <?php get_template_part('components/menu-links', null, ['location' => OptelyaMenuLocation::Header]); ?>
        </nav>

        <?php get_template_part('components/phone-link', null, ['class' => 'site-header__phone', 'size' => 16]); ?>

        <?php get_template_part('components/link', null, ['link' => optelya_link(optelya_option(OptelyaSetting::HeaderCta)), 'class' => 'button button--small']); ?>
    </div>
</header>

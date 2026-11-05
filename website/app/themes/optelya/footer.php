<?php

/**
 * Site footer: settings (tagline, contact column) and three menus. The site
 * version shows in the legal bar on every environment.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

$version = optelya_site_version();
$tagline = optelya_option_text(OptelyaSetting::Tagline);
$contactTitle = optelya_option_text(OptelyaSetting::ContactTitle);
$address = optelya_option_text(OptelyaSetting::Address);
?>

<footer class="site-footer">
    <div class="site-footer__columns">
        <div class="site-footer__identity">
            <span class="brand site-footer__brand"><?php bloginfo('name'); ?></span>
            <?php if ($tagline !== '') : ?>
                <span><?php echo esc_html($tagline); ?></span>
            <?php endif; ?>
        </div>

        <?php foreach (OptelyaMenuLocation::footerColumns() as $location) : ?>
            <?php $title = optelya_menu_name($location); ?>
            <?php if ($title !== '') : ?>
                <nav class="site-footer__column" aria-label="<?php echo esc_attr($title); ?>">
                    <h2 class="site-footer__title"><?php echo esc_html($title); ?></h2>
                    <?php get_template_part('components/menu-links', null, ['location' => $location, 'class' => 'site-footer__links']); ?>
                </nav>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if ($contactTitle !== '' || $address !== '') : ?>
            <div class="site-footer__column">
                <?php if ($contactTitle !== '') : ?>
                    <h2 class="site-footer__title"><?php echo esc_html($contactTitle); ?></h2>
                <?php endif; ?>
                <?php if ($address !== '') : ?>
                    <span><?php echo nl2br(esc_html($address)); ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="site-footer__legal">
        <?php get_template_part('components/menu-links', null, ['location' => OptelyaMenuLocation::Legal, 'class' => 'site-footer__legal-links']); ?>

        <?php if ($version !== '') : ?>
            <span class="site-footer__version">
                <?php printf(
                    /* translators: %s: site version number. */
                    esc_html__('Version %s', 'optelya'),
                    esc_html($version),
                ); ?>
            </span>
        <?php endif; ?>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>

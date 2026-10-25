<?php

/**
 * Homepage: the sections are the layouts of the ACF flexible content field
 * `sections`, each rendered by layouts/<name>.php. See readme/contribution.md.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

get_header();

$pageId = is_preview() ? (int) get_queried_object_id() : (int) get_the_ID();
$sections = 'sections';
?>

<main id="main-content">
    <?php if (optelya_has_acf() && have_rows($sections, $pageId)) : ?>
        <?php while (have_rows($sections, $pageId)) : ?>
            <?php the_row(); ?>
            <?php $layout = optelya_layout_template(); ?>
            <?php if ($layout !== '') : ?>
                <?php get_template_part('layouts/' . $layout); ?>
            <?php endif; ?>
        <?php endwhile; ?>
    <?php endif; ?>
</main>

<?php
get_footer();

<?php

/**
 * Generic template: single content, archives and search results.
 *
 * The homepage has its own template (front-page.php).
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

get_header();

$isSingle = is_singular();
?>

<main id="main-content" class="page-content">
    <?php if (! $isSingle) : ?>
        <h1 class="section-title"><?php echo esc_html(wp_strip_all_tags(is_search() ? get_search_query(false) : get_the_archive_title())); ?></h1>
    <?php endif; ?>

    <?php while (have_posts()) : ?>
        <?php the_post(); ?>
        <article <?php post_class(); ?>>
            <?php if ($isSingle) : ?>
                <h1 class="section-title"><?php the_title(); ?></h1>
                <?php the_content(); ?>
            <?php else : ?>
                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <?php the_excerpt(); ?>
            <?php endif; ?>
        </article>
    <?php endwhile; ?>
</main>

<?php
get_footer();

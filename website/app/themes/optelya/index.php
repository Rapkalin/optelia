<?php

/**
 * Generic template: single content and search results (archives answer 404,
 * see inc/seo.php).
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

<main id="<?php echo esc_attr(OPTELYA_MAIN_CONTENT_ID); ?>" class="page-content">
    <?php if (! $isSingle) : ?>
        <h1 class="section-title">
            <?php if (is_search()) : ?>
                <?php /* translators: %s: search terms */ ?>
                <?php echo esc_html(sprintf(__('Résultats pour « %s »', 'optelya'), get_search_query(false))); ?>
            <?php else : ?>
                <?php echo esc_html(wp_strip_all_tags(get_the_archive_title())); ?>
            <?php endif; ?>
        </h1>
        <?php if (! have_posts()) : ?>
            <p><?php esc_html_e('Aucun résultat.', 'optelya'); ?></p>
        <?php endif; ?>
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

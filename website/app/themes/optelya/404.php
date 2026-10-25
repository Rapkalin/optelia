<?php

/**
 * Page not found.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main id="main-content" class="page-content">
    <h1 class="section-title"><?php esc_html_e('Page introuvable', 'optelya'); ?></h1>
    <p><a class="text-link" href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e("Retour à l'accueil", 'optelya'); ?></a></p>
</main>

<?php
get_footer();

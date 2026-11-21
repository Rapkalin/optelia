<?php

/**
 * What Optelya brings, one panel per reader.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}
?>
<section id="<?php echo esc_attr(OptelyaSection::Benefits->value); ?>" class="section section--benefits">
    <div class="section__inner">
        <?php get_template_part('components/section-heading', null, ['title' => optelya_sub_text('title')]); ?>

        <?php get_template_part('components/reader-panels', null, ['panels' => optelya_reader_panels(optelya_sub_field('panels'))]); ?>
    </div>
</section>

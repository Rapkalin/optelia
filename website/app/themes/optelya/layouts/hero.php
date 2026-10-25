<?php

/**
 * Hero: promise, calls to action and the publisher's simulation loop.
 *
 * The media frame shows, in order of availability: the video (started by
 * assets/scripts/hero-video.js unless reduced motion is requested), the poster
 * image, or the mockup's placeholder.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

$videoId = optelya_attachment_id(optelya_sub_field('video'));
$posterId = optelya_attachment_id(optelya_sub_field('poster'));
$mime = $videoId > 0 ? (string) get_post_mime_type($videoId) : '';
$posterUrl = $posterId > 0 ? (string) wp_get_attachment_image_url($posterId, 'large') : '';
$caption = optelya_sub_text('caption');
$eyebrow = optelya_sub_text('eyebrow');
$lead = optelya_sub_text('lead');
$pauseLabel = __('Mettre la vidéo en pause', 'optelya');
?>
<section id="<?php echo esc_attr(OptelyaSection::Top->value); ?>" class="hero">
    <div class="hero__inner">
        <div class="hero__text">
            <?php if ($eyebrow !== '') : ?>
                <p class="eyebrow"><?php echo esc_html($eyebrow); ?></p>
            <?php endif; ?>
            <h1 class="hero__title"><?php echo esc_html(optelya_sub_text('title')); ?></h1>
            <?php if ($lead !== '') : ?>
                <p class="hero__lead"><?php echo esc_html($lead); ?></p>
            <?php endif; ?>
            <div class="hero__actions">
                <?php get_template_part('components/link', null, ['link' => optelya_link(optelya_sub_field('primary_link')), 'class' => 'button button--large']); ?>
                <?php get_template_part('components/link', null, ['link' => optelya_link(optelya_sub_field('secondary_link')), 'class' => 'hero__secondary']); ?>
            </div>
        </div>

        <figure class="hero__media">
            <div class="hero__frame">
                <?php if ($videoId > 0) : ?>
                    <video class="hero__video" muted loop playsinline preload="metadata" data-hero-video<?php echo $posterUrl !== '' ? ' poster="' . esc_url($posterUrl) . '"' : ''; ?>>
                        <source src="<?php echo esc_url((string) wp_get_attachment_url($videoId)); ?>"<?php echo $mime !== '' ? ' type="' . esc_attr($mime) . '"' : ''; ?>>
                    </video>
                <?php elseif ($posterId > 0) : ?>
                    <?php echo wp_get_attachment_image($posterId, 'large', false, ['class' => 'hero__video']); ?>
                <?php else : ?>
                    <div class="hero__placeholder">
                        <span class="hero__placeholder-label"><?php esc_html_e('Visuel éditeur', 'optelya'); ?></span>
                        <span><?php esc_html_e('Boucle vidéo de simulation, environ 10 s, sans son', 'optelya'); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($videoId > 0 || $posterId === 0) : ?>
                    <button class="hero__pause" type="button" aria-label="<?php echo esc_attr($pauseLabel); ?>" data-hero-pause data-label-pause="<?php echo esc_attr($pauseLabel); ?>" data-label-play="<?php esc_attr_e('Lire la vidéo', 'optelya'); ?>"<?php echo $videoId > 0 ? '' : ' disabled'; ?>>
                        <?php get_template_part('components/icon', null, ['icon' => OptelyaIcon::Pause, 'size' => 16]); ?>
                        <?php get_template_part('components/icon', null, ['icon' => OptelyaIcon::Play, 'size' => 16]); ?>
                    </button>
                <?php endif; ?>
            </div>
            <?php if ($caption !== '') : ?>
                <figcaption class="hero__caption"><?php echo esc_html($caption); ?></figcaption>
            <?php endif; ?>
        </figure>
    </div>
</section>

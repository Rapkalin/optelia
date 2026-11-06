<?php

/**
 * Promise, calls to action and visual of a page head. Without media (archive
 * pages), the text spans the whole width.
 *
 * The media frame shows, in order of availability: the video (started by
 * assets/scripts/hero-video.js unless reduced motion is requested), the poster
 * image, or a placeholder.
 *
 * @param string                 $args['id']      Anchor of the section ('' for none).
 * @param string                 $args['eyebrow']
 * @param string                 $args['title']
 * @param string                 $args['lead']
 * @param array                  $args['primary']   Link (see components/link.php).
 * @param array                  $args['secondary'] Link.
 * @param array{video_id?: int, poster_id?: int, caption?: string, placeholder_label?: string, placeholder_text?: string}|null $args['media']
 * @param list<array{label: string, url: string}> $args['breadcrumb'] Shown at the top of the hero (components/breadcrumb.php).
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

$anchor = (string) ($args['id'] ?? '');
$media = $args['media'] ?? null;
$media = is_array($media) ? $media : null;
$videoId = (int) ($media['video_id'] ?? 0);
$posterId = (int) ($media['poster_id'] ?? 0);
$mime = $videoId > 0 ? (string) get_post_mime_type($videoId) : '';
$imageSize = 'large';
$posterUrl = $posterId > 0 ? (string) wp_get_attachment_image_url($posterId, $imageSize) : '';
$caption = (string) ($media['caption'] ?? '');
$eyebrow = (string) ($args['eyebrow'] ?? '');
$lead = (string) ($args['lead'] ?? '');
$pauseLabel = __('Mettre la vidéo en pause', 'optelya');
$breadcrumb = (array) ($args['breadcrumb'] ?? []);
$classes = 'hero' . ($media === null ? ' hero--text-only' : '') . ($breadcrumb !== [] ? ' hero--with-breadcrumb' : '');
?>
<section<?php echo $anchor !== '' ? ' id="' . esc_attr($anchor) . '"' : ''; ?> class="<?php echo esc_attr($classes); ?>">
    <?php if ($breadcrumb !== []) : ?>
        <?php get_template_part('components/breadcrumb', null, ['items' => $breadcrumb, 'on_ink' => true]); ?>
    <?php endif; ?>
    <div class="hero__inner">
        <div class="hero__text">
            <?php if ($eyebrow !== '') : ?>
                <p class="eyebrow"><?php echo esc_html($eyebrow); ?></p>
            <?php endif; ?>
            <h1 class="hero__title"><?php echo esc_html((string) ($args['title'] ?? '')); ?></h1>
            <?php if ($lead !== '') : ?>
                <p class="hero__lead"><?php echo esc_html($lead); ?></p>
            <?php endif; ?>
            <div class="hero__actions">
                <?php get_template_part('components/link', null, ['link' => $args['primary'] ?? [], 'class' => 'button button--large']); ?>
                <?php get_template_part('components/link', null, ['link' => $args['secondary'] ?? [], 'class' => 'hero__secondary']); ?>
            </div>
        </div>

        <?php if ($media !== null) : ?>
        <figure class="hero__media">
            <div class="hero__frame">
                <?php if ($videoId > 0) : ?>
                    <video class="hero__visual" muted loop playsinline preload="metadata" data-hero-video<?php echo $posterUrl !== '' ? ' poster="' . esc_url($posterUrl) . '"' : ''; ?>>
                        <source src="<?php echo esc_url((string) wp_get_attachment_url($videoId)); ?>"<?php echo $mime !== '' ? ' type="' . esc_attr($mime) . '"' : ''; ?>>
                    </video>
                <?php elseif ($posterId > 0) : ?>
                    <?php echo wp_get_attachment_image($posterId, $imageSize, false, ['class' => 'hero__visual']); ?>
                <?php else : ?>
                    <div class="hero__placeholder">
                        <span class="hero__placeholder-label"><?php echo esc_html((string) ($media['placeholder_label'] ?? __('Visuel éditeur', 'optelya'))); ?></span>
                        <span><?php echo esc_html((string) ($media['placeholder_text'] ?? __('Boucle vidéo de simulation, environ 10 s, sans son', 'optelya'))); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($videoId > 0) : ?>
                    <button class="hero__pause" type="button" aria-label="<?php echo esc_attr($pauseLabel); ?>" data-hero-pause data-label-pause="<?php echo esc_attr($pauseLabel); ?>" data-label-play="<?php esc_attr_e('Lire la vidéo', 'optelya'); ?>">
                        <?php get_template_part('components/icon', null, ['icon' => OptelyaIcon::Pause, 'size' => 16]); ?>
                        <?php get_template_part('components/icon', null, ['icon' => OptelyaIcon::Play, 'size' => 16]); ?>
                    </button>
                <?php endif; ?>
            </div>
            <?php if ($caption !== '') : ?>
                <figcaption class="hero__caption"><?php echo esc_html($caption); ?></figcaption>
            <?php endif; ?>
        </figure>
        <?php endif; ?>
    </div>
</section>

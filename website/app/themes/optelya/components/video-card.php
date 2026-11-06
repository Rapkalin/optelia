<?php

/**
 * A video loaded only when the visitor asks for it (performance, no autoplay):
 * the button is replaced by the player (assets/scripts/video-card.js).
 * Without the script, the player is a plain link to the file.
 *
 * @param string $args['video_url']
 * @param string $args['poster_url']
 * @param string $args['captions_url'] WebVTT, French.
 * @param string $args['title']        Label of the play button.
 * @param string $args['player_label'] Name of the player once loaded.
 * @param string $args['meta']         Length, captions…
 * @param string $args['transcript']
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

$videoUrl = (string) ($args['video_url'] ?? '');

if ($videoUrl === '') {
    return;
}

$meta = (string) ($args['meta'] ?? '');
$transcript = (string) ($args['transcript'] ?? '');
?>
<figure class="video-card" data-video-card data-video="<?php echo esc_url($videoUrl); ?>" data-poster="<?php echo esc_url((string) ($args['poster_url'] ?? '')); ?>" data-captions="<?php echo esc_url((string) ($args['captions_url'] ?? '')); ?>" data-label="<?php echo esc_attr((string) ($args['player_label'] ?? '')); ?>">
    <a class="video-card__poster" href="<?php echo esc_url($videoUrl); ?>" data-video-play>
        <span class="video-card__play" aria-hidden="true"><?php get_template_part('components/icon', null, ['icon' => OptelyaIcon::Play, 'size' => 28]); ?></span>
        <span class="video-card__title"><?php echo esc_html((string) ($args['title'] ?? '')); ?></span>
        <?php if ($meta !== '') : ?>
            <span class="video-card__meta"><?php echo esc_html($meta); ?></span>
        <?php endif; ?>
    </a>
    <figcaption class="video-card__caption"><?php esc_html_e('La vidéo ne se charge qu’à votre demande.', 'optelya'); ?></figcaption>
    <?php if ($transcript !== '') : ?>
        <details class="video-card__transcript">
            <summary><?php esc_html_e('Lire la transcription', 'optelya'); ?></summary>
            <p><?php echo nl2br(esc_html($transcript)); ?></p>
        </details>
    <?php endif; ?>
</figure>

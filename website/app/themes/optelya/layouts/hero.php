<?php

/**
 * Homepage hero: the section's fields, rendered by components/hero.php.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

get_template_part('components/hero', null, [
    'id' => OptelyaSection::Top->value,
    'eyebrow' => optelya_sub_text('eyebrow'),
    'title' => optelya_sub_text('title'),
    'lead' => optelya_sub_text('lead'),
    'primary' => optelya_link(optelya_sub_field('primary_link')),
    'secondary' => optelya_link(optelya_sub_field('secondary_link')),
    'media' => [
        'video_id' => optelya_attachment_id(optelya_sub_field('video')),
        'poster_id' => optelya_attachment_id(optelya_sub_field('poster')),
        'caption' => optelya_sub_text('caption'),
    ],
]);

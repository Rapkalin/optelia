<?php

/**
 * Homepage contact section: the section's fields, rendered by
 * components/contact-block.php.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

get_template_part('components/contact-block', null, [
    'title' => optelya_sub_text('title'),
    'lead' => optelya_sub_text('lead'),
    'steps' => array_map(static fn(array $step): string => optelya_text($step['text'] ?? ''), optelya_rows(optelya_sub_field('steps'))),
    'notice' => optelya_sub_text('notice'),
]);

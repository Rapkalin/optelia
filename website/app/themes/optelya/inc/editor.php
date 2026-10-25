<?php

/**
 * The homepage is contributed through one ACF form under the title: its block
 * editor is turned off, and the content area hidden by the field group's
 * `hide_on_screen`. Two competing input surfaces on the same page guarantee a
 * contributor fills the wrong one.
 *
 * Turned off for the homepage only: free text (legal notices, a future post)
 * is better written in blocks.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

function optelya_disable_block_editor(bool $useBlockEditor, WP_Post $post): bool
{
    return (int) $post->ID === (int) get_option('page_on_front') ? false : $useBlockEditor;
}
add_filter('use_block_editor_for_post', 'optelya_disable_block_editor', 10, 2);

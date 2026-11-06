<?php

/**
 * Registration of the catalogue's content types and taxonomies.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

function optelya_register_content_types(): void
{
    foreach (OptelyaPostType::cases() as $type) {
        register_post_type($type->value, [
            'labels' => [
                'name' => $type->plural(),
                'singular_name' => $type->singular(),
                /* translators: %s: content type name */
                'add_new_item' => sprintf(__('Ajouter : %s', 'optelya'), $type->singular()),
                /* translators: %s: content type name */
                'edit_item' => sprintf(__('Modifier : %s', 'optelya'), $type->singular()),
            ],
            'public' => true,
            'has_archive' => $type->hasArchive() ? $type->slug() : false,
            'rewrite' => ['slug' => $type->slug(), 'with_front' => false],
            'menu_icon' => $type->menuIcon(),
            'menu_position' => 21,
            'capability_type' => 'page',
            'map_meta_cap' => true,
            // Edited through their ACF fields: no block editor.
            'supports' => ['title', 'revisions'],
            'show_in_rest' => false,
        ]);
    }

    foreach (OptelyaTaxonomy::cases() as $taxonomy) {
        register_taxonomy($taxonomy->value, array_map(static fn(OptelyaPostType $type): string => $type->value, $taxonomy->postTypes()), [
            'labels' => [
                'name' => __('Enjeux', 'optelya'),
                'singular_name' => __('Enjeu', 'optelya'),
            ],
            'public' => false,
            'show_ui' => true,
            'show_admin_column' => true,
            'hierarchical' => true,
            'rewrite' => false,
        ]);
    }
}
add_action('init', 'optelya_register_content_types');

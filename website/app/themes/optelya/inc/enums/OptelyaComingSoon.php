<?php

/**
 * A menu link to a section not built yet: it opens a short message with a
 * link to the contact form instead of leading nowhere. PURE, hence
 * tests/Unit/ComingSoonTest.php; the glue is inc/menus.php.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

final class OptelyaComingSoon
{
    /** Checkbox of a menu item (acf-json/group_optelya_menu_item.json). */
    public const string FIELD = 'coming_soon';

    /** `id` of the dialog, the target of every link that opens it. */
    public const string DIALOG_ID = 'coming-soon';

    /**
     * Texts of the message: those of Settings → Configuration, or the default
     * of each one left empty.
     *
     * @return array{title: string, text: string}
     */
    public static function texts(string $title, string $text): array
    {
        return [
            'title' => trim($title) !== '' ? trim($title) : __('Bientôt disponible', 'optelya'),
            'text' => trim($text) !== '' ? trim($text) : __('Cette section est en cours de construction. En attendant, nos experts répondent à vos questions.', 'optelya'),
        ];
    }
}

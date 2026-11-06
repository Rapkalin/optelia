<?php

/**
 * Taxonomies of the catalogue. The issues (« Optimiser et automatiser »…) order
 * the solutions archive and the menu; a solution belongs to one of them.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

enum OptelyaTaxonomy: string
{
    case Issue = 'optelya_issue';

    /**
     * @return list<OptelyaPostType>
     */
    public function postTypes(): array
    {
        return match ($this) {
            self::Issue => [OptelyaPostType::Solution, OptelyaPostType::Study],
        };
    }
}

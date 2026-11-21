<?php

/**
 * Sections of the pages, each one reachable by its anchor (the homepage's,
 * then those of the catalogue pages, which their head links to).
 *
 * The header, the footer and the calls to action all point at these anchors:
 * one enum keeps the `id` of a section and every link to it in step.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

enum OptelyaSection: string
{
    case Top = 'top';
    case Issues = 'enjeux';
    case Sectors = 'secteurs';
    case Results = 'resultats';
    case Benefits = 'lecteurs';
    case Contact = 'expert';
    case Solutions = 'solutions';
    case Vendors = 'editeurs';
    case Tools = 'outils';
    case Studies = 'etudes';

    /**
     * Link to the section from any page of the site, not only the homepage.
     */
    public function url(): string
    {
        return home_url('/#' . $this->value);
    }
}

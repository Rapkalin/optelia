<?php

/**
 * Content types of the catalogue — single source of their keys, addresses and
 * labels. See readme/contribution.md.
 *
 * Their rights are those of pages (`capability_type => page`): a contributor
 * manages their own, an editor all of them (inc/roles.php).
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

enum OptelyaPostType: string
{
    case Solution = 'optelya_solution';
    case Vendor = 'optelya_vendor';
    case Industry = 'optelya_industry';
    case Study = 'optelya_study';

    /**
     * Address of the content: /solutions/optimus/.
     */
    public function slug(): string
    {
        return match ($this) {
            self::Solution => 'solutions',
            self::Vendor => 'editeurs',
            self::Industry => 'industries',
            self::Study => 'etudes-de-cas',
        };
    }

    /**
     * The vendors are reached from the solutions: no list of their own.
     */
    public function hasArchive(): bool
    {
        return $this !== self::Vendor;
    }

    public function singular(): string
    {
        return match ($this) {
            self::Solution => __('Solution', 'optelya'),
            self::Vendor => __('Éditeur', 'optelya'),
            self::Industry => __('Industrie', 'optelya'),
            self::Study => __('Étude de cas', 'optelya'),
        };
    }

    public function plural(): string
    {
        return match ($this) {
            self::Solution => __('Solutions', 'optelya'),
            self::Vendor => __('Éditeurs', 'optelya'),
            self::Industry => __('Industries', 'optelya'),
            self::Study => __('Études de cas', 'optelya'),
        };
    }

    public function menuIcon(): string
    {
        return match ($this) {
            self::Solution => 'dashicons-admin-tools',
            self::Vendor => 'dashicons-building',
            self::Industry => 'dashicons-admin-site-alt3',
            self::Study => 'dashicons-chart-line',
        };
    }
}

<?php

/**
 * Inline SVG icons of the theme.
 *
 * Inline rather than files: they take the colour of their text through
 * `currentColor`, and add no request.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

enum OptelyaIcon: string
{
    case Phone = 'phone';
    case Pause = 'pause';
    case Play = 'play';
    case Optimize = 'optimize';
    case Tolerance = 'tolerance';
    case Mechanism = 'mechanism';

    public function viewBox(): string
    {
        return match ($this) {
            self::Phone, self::Pause, self::Play => '0 0 24 24',
            self::Optimize, self::Tolerance, self::Mechanism => '0 0 40 40',
        };
    }

    /**
     * Icons a contributor can pick for an issue card, with their admin label.
     *
     * @return array<string, string>
     */
    public static function issueChoices(): array
    {
        return [
            self::Optimize->value => __('Courbe d’optimisation', 'optelya'),
            self::Tolerance->value => __('Cotes et tolérances', 'optelya'),
            self::Mechanism->value => __('Mécanisme', 'optelya'),
        ];
    }

    /**
     * Paint attributes, copied from the mockup: only the phone has round caps.
     */
    public function paint(): string
    {
        return match ($this) {
            self::Pause, self::Play => 'fill="currentColor"',
            self::Phone => 'fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"',
            self::Optimize, self::Tolerance, self::Mechanism => 'fill="none" stroke="currentColor" stroke-width="2"',
        };
    }

    public function shapes(): string
    {
        return match ($this) {
            self::Phone => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"></path>',
            self::Pause => '<rect x="6" y="5" width="4" height="14" rx="1"></rect><rect x="14" y="5" width="4" height="14" rx="1"></rect>',
            self::Play => '<path d="M8 5v14l11-7z"></path>',
            self::Optimize => '<path d="M4 34 L14 20 L22 26 L36 6"></path><circle cx="36" cy="6" r="3"></circle><path d="M4 36 H36"></path>',
            self::Tolerance => '<rect x="6" y="12" width="28" height="16" rx="2"></rect><path d="M6 6 V10 M34 6 V10 M6 8 H34"></path><path d="M6 32 V36 M20 32 V36 M6 34 H20"></path>',
            self::Mechanism => '<circle cx="12" cy="28" r="7"></circle><circle cx="28" cy="12" r="5"></circle><path d="M17 23 L24 16"></path><circle cx="12" cy="28" r="2"></circle>',
        };
    }
}

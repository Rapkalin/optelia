<?php

/**
 * Background of the header, chosen in Settings → Configuration, and the text
 * colour that stays readable on it. PURE, hence tests/Unit/HeaderColorTest.php.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

final class OptelyaHeaderColor
{
    /**
     * Also the field's default in acf-json/group_optelya_settings.json, kept in
     * step by the test.
     */
    public const string DEFAULT = '#E8F0FF';

    /**
     * The header's two text colours: $color-ink and white
     * (assets/styles/basics/_variables.scss — the test keeps the ink in step).
     */
    public const string INK = '#0F1D2B';

    public const string WHITE = '#FFFFFF';

    /**
     * WCAG AA for the header's text (15 px, regular).
     */
    public const float MIN_CONTRAST = 4.5;

    /**
     * A `#RRGGBB` value, or the default: the stored value lands in a style
     * attribute.
     */
    public static function normalize(mixed $value): string
    {
        return is_string($value) && preg_match('/^#[0-9a-f]{6}$/i', $value) === 1 ? strtoupper($value) : self::DEFAULT;
    }

    /**
     * Ink text when it reads better than white on this background.
     */
    public static function isLight(string $background): bool
    {
        return self::contrast(self::INK, $background) >= self::contrast(self::WHITE, $background);
    }

    /**
     * Contrast of the better of the two text colours. Mid tones stay below
     * MIN_CONTRAST whatever the text: the field refuses them.
     */
    public static function bestContrast(string $background): float
    {
        return max(self::contrast(self::INK, $background), self::contrast(self::WHITE, $background));
    }

    public static function contrast(string $first, string $second): float
    {
        $lighter = max(self::luminance($first), self::luminance($second));
        $darker = min(self::luminance($first), self::luminance($second));

        return ($lighter + 0.05) / ($darker + 0.05);
    }

    private static function luminance(string $hex): float
    {
        $channels = array_map(
            static function (string $pair): float {
                $value = hexdec($pair) / 255;

                return $value <= 0.04045 ? $value / 12.92 : (($value + 0.055) / 1.055) ** 2.4;
            },
            str_split(substr(self::normalize($hex), 1), 2),
        );

        return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
    }
}

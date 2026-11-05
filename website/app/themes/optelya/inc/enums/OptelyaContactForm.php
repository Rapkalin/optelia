<?php

/**
 * Rules of the contact form that need no WordPress: validation, time token,
 * monthly counters. PURE, hence tests/Unit/ContactFormTest.php. The glue lives
 * in inc/contact.php — see readme/contact.md.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

final class OptelyaContactForm
{
    /**
     * Hidden field telling the handler a POST is this form's.
     */
    public const string ACTION_FIELD = 'optelya_action';

    public const string ACTION = 'optelya_contact';

    /**
     * Invisible field: a human leaves it empty, a robot fills every input.
     */
    public const string HONEYPOT = 'website';

    public const string TOKEN = 'contact_token';

    /**
     * Anchor the page comes back to after a submission: the outcome message.
     * Focusable, so the browser focuses it on arrival — keyboard and screen
     * reader users land on it (RGAA 11.10, 11.11).
     */
    public const string STATUS_ANCHOR = 'contact-status';

    /**
     * No human fills five fields in less. The site has no page cache, so a day
     * is a generous upper bound.
     */
    public const int MIN_SECONDS = 3;

    public const int MAX_SECONDS = 86400;

    public const int MAX_PER_HOUR = 5;

    public const int MONTHS_KEPT = 24;

    /**
     * Time the form was displayed, signed: the visitor cannot backdate it.
     */
    public static function token(int $now, string $secret): string
    {
        return $now . '.' . hash_hmac('sha256', (string) $now, $secret);
    }

    /**
     * Null when the token is genuine and its age plausible.
     */
    public static function tokenOutcome(string $token, int $now, string $secret): ?OptelyaContactOutcome
    {
        [$issued, $signature] = array_pad(explode('.', $token, 2), 2, '');

        if (! ctype_digit($issued) || ! hash_equals(hash_hmac('sha256', $issued, $secret), $signature)) {
            return OptelyaContactOutcome::Expired;
        }

        $age = $now - (int) $issued;

        if ($age < self::MIN_SECONDS) {
            return OptelyaContactOutcome::TooFast;
        }

        return $age > self::MAX_SECONDS ? OptelyaContactOutcome::Expired : null;
    }

    /**
     * @return array{values: array<string, string>, invalid: list<OptelyaContactField>}
     */
    public static function validate(array $input): array
    {
        $values = [];
        $invalid = [];

        foreach (OptelyaContactField::cases() as $field) {
            $value = $field->clean($input[$field->inputName()] ?? '');
            $values[$field->value] = $value;

            if (! $field->isValid($value)) {
                $invalid[] = $field;
            }
        }

        return ['values' => $values, 'invalid' => $invalid];
    }

    /**
     * Adds one outcome to the month's counters, newest month first, and drops
     * the months beyond MONTHS_KEPT.
     *
     * @param array<string, array<string, int>> $stats
     *
     * @return array<string, array<string, int>>
     */
    public static function record(array $stats, OptelyaContactOutcome $outcome, string $month): array
    {
        $stats[$month][$outcome->value] = ($stats[$month][$outcome->value] ?? 0) + 1;
        krsort($stats);

        return array_slice($stats, 0, self::MONTHS_KEPT, true);
    }
}

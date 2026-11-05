<?php

/**
 * Fields of the contact form — single source of their names, rules and labels,
 * shared by the template, the handler and the e-mail.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

enum OptelyaContactField: string
{
    case Name = 'name';
    case Email = 'email';
    case Company = 'company';
    case Phone = 'phone';
    case Project = 'project';

    public function label(): string
    {
        return match ($this) {
            self::Name => __('Nom et prénom', 'optelya'),
            self::Email => __('E-mail professionnel', 'optelya'),
            self::Company => __('Société', 'optelya'),
            self::Phone => __('Téléphone', 'optelya'),
            self::Project => __('Votre projet en une phrase', 'optelya'),
        };
    }

    /**
     * Prefixed: `name` is a public WordPress query var, and posting it turns
     * the page into a 404.
     */
    public function inputName(): string
    {
        return 'contact_' . $this->value;
    }

    public function inputType(): string
    {
        return match ($this) {
            self::Email => 'email',
            self::Phone => 'tel',
            default => 'text',
        };
    }

    public function autocomplete(): string
    {
        return match ($this) {
            self::Name => 'name',
            self::Email => 'email',
            self::Company => 'organization',
            self::Phone => 'tel',
            self::Project => 'off',
        };
    }

    /**
     * Three required, two optional (zoning v2, decision 5).
     */
    public function isRequired(): bool
    {
        return in_array($this, [self::Name, self::Email, self::Company], true);
    }

    public function maxLength(): int
    {
        return match ($this) {
            self::Email => 254,
            self::Phone => 30,
            self::Project => 500,
            default => 120,
        };
    }

    /**
     * One line of plain text: tags and control characters (line breaks
     * included) are removed, so a value can never inject an e-mail header.
     */
    public function clean(mixed $raw): string
    {
        if (! is_scalar($raw)) {
            return '';
        }

        $text = strip_tags((string) $raw);
        $text = (string) preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $text);

        return trim((string) preg_replace('/\s{2,}/u', ' ', $text));
    }

    /**
     * Why a value is refused, and how to fix it (RGAA 11.11).
     */
    public function errorMessage(string $value): string
    {
        if ($value === '') {
            return __('Ce champ est obligatoire.', 'optelya');
        }

        if (mb_strlen($value) > $this->maxLength()) {
            /* translators: %d: maximum number of characters */
            return sprintf(__('%d caractères au plus.', 'optelya'), $this->maxLength());
        }

        return match ($this) {
            self::Email => __('Adresse e-mail attendue, par exemple nom@entreprise.fr.', 'optelya'),
            self::Phone => __('Chiffres, espaces et + ( ) . - uniquement, par exemple 01 23 45 67 89.', 'optelya'),
            default => __('Valeur non valide.', 'optelya'),
        };
    }

    public function isValid(string $value): bool
    {
        if ($value === '') {
            return ! $this->isRequired();
        }

        if (mb_strlen($value) > $this->maxLength()) {
            return false;
        }

        return match ($this) {
            self::Email => filter_var($value, FILTER_VALIDATE_EMAIL) !== false,
            self::Phone => preg_match('/^\+?[\d\s().-]{6,}$/', $value) === 1,
            default => true,
        };
    }
}

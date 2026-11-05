<?php

/**
 * What became of a contact form submission. Counted per month on Tools →
 * Contact; the value is also the `contact` query parameter after a redirect.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

enum OptelyaContactOutcome: string
{
    case Sent = 'sent';
    case MailFailed = 'mail_failed';
    case Invalid = 'invalid';
    case Expired = 'expired';
    case TooFast = 'too_fast';
    case Honeypot = 'honeypot';
    case RateLimited = 'rate_limited';

    public function label(): string
    {
        return match ($this) {
            self::Sent => __('Envoyées', 'optelya'),
            self::MailFailed => __('E-mail en échec', 'optelya'),
            self::Invalid => __('Champs invalides', 'optelya'),
            self::Expired => __('Page expirée', 'optelya'),
            self::TooFast => __('Remplie trop vite', 'optelya'),
            self::Honeypot => __('Champ piège rempli', 'optelya'),
            self::RateLimited => __('Trop d’envois', 'optelya'),
        };
    }

    /**
     * What the visitor reads. The honeypot answers like a success: a robot told
     * it failed adapts.
     */
    public function message(): string
    {
        return match ($this) {
            self::Sent, self::Honeypot => __('Merci, votre demande est envoyée. Un expert vous répond rapidement.', 'optelya'),
            self::MailFailed => __('Votre demande n’a pas pu être envoyée. Merci de réessayer dans quelques minutes.', 'optelya'),
            self::Invalid => __('Merci de corriger les champs signalés.', 'optelya'),
            self::Expired, self::TooFast => __('La page a expiré : merci de renvoyer votre demande.', 'optelya'),
            self::RateLimited => __('Trop de demandes envoyées depuis votre connexion. Merci de réessayer dans une heure.', 'optelya'),
        };
    }

    public function isSuccess(): bool
    {
        return in_array($this, [self::Sent, self::Honeypot], true);
    }
}

<?php

/**
 * Contact form: handling, e-mail, anti-robot guards and Tools → Contact.
 *
 * Nothing personal is stored: the request leaves by e-mail, the site keeps
 * monthly counters only. See readme/contact.md.
 *
 * @package optelya
 */

use function Env\env;

if (! defined('ABSPATH')) {
    exit;
}

const OPTELYA_CONTACT_STATS_OPTION = 'optelya_contact_stats';
const OPTELYA_CONTACT_ADMIN_PAGE = 'optelya-contact';
const OPTELYA_CONTACT_QUERY = 'contact';

function optelya_contact_secret(): string
{
    return wp_salt('nonce');
}

function optelya_contact_token(): string
{
    return OptelyaContactForm::token(time(), optelya_contact_secret());
}

/**
 * Not displayed anywhere. Out of Settings → Configuration on purpose: a
 * contributor edits that page, and must not redirect the requests to
 * themselves. The admin e-mail is changed by an administrator only.
 */
function optelya_contact_recipient(): string
{
    $recipient = (string) env('CONTACT_RECIPIENT');

    return is_email($recipient) ? $recipient : (string) get_option('admin_email');
}

/**
 * Outcome of the current request (a POST, or the redirect after one), and
 * what the visitor typed when it must be shown again.
 *
 * @param array{outcome: OptelyaContactOutcome, values: array<string, string>, invalid: list<OptelyaContactField>}|null $set
 *
 * @return array{outcome: ?OptelyaContactOutcome, values: array<string, string>, invalid: list<OptelyaContactField>}
 */
function optelya_contact_state(?array $set = null): array
{
    static $state = null;

    if ($set !== null) {
        $state = $set;
    }

    if ($state !== null) {
        return $state;
    }

    $redirected = $_GET[OPTELYA_CONTACT_QUERY] ?? '';
    $outcome = is_string($redirected) ? OptelyaContactOutcome::tryFrom($redirected) : null;

    // Only a success travels in the URL: anything else is rendered on the POST.
    return ['outcome' => $outcome?->isSuccess() ? $outcome : null, 'values' => [], 'invalid' => []];
}

function optelya_contact_record(OptelyaContactOutcome $outcome): void
{
    $stats = get_option(OPTELYA_CONTACT_STATS_OPTION, []);
    $stats = OptelyaContactForm::record(is_array($stats) ? $stats : [], $outcome, wp_date('Y-m'));

    update_option(OPTELYA_CONTACT_STATS_OPTION, $stats, false);
}

/**
 * Attempts per visitor and hour. The address is hashed with the site's salt
 * and forgotten after the hour. REMOTE_ADDR only: X-Forwarded-For is typed by
 * the client.
 */
function optelya_contact_is_rate_limited(): bool
{
    $address = $_SERVER['REMOTE_ADDR'] ?? '';
    $key = 'optelya_contact_' . substr(wp_hash(is_string($address) ? $address : ''), 0, 20);
    $attempts = (int) get_transient($key);

    if ($attempts >= OptelyaContactForm::MAX_PER_HOUR) {
        return true;
    }

    set_transient($key, $attempts + 1, HOUR_IN_SECONDS);

    return false;
}

/**
 * @param array<string, string> $values
 */
function optelya_contact_send(array $values): bool
{
    $lines = [];

    foreach (OptelyaContactField::cases() as $field) {
        $lines[] = $field->label() . ' : ' . ($values[$field->value] !== '' ? $values[$field->value] : '—');
    }

    $subject = sprintf(
        /* translators: 1: site name, 2: company */
        __('[%1$s] Demande de contact — %2$s', 'optelya'),
        wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES),
        $values[OptelyaContactField::Company->value],
    );

    // The address is validated, and clean() strips line breaks: no header
    // can be injected through Reply-To.
    $headers = ['Reply-To: ' . $values[OptelyaContactField::Email->value]];

    return wp_mail(optelya_contact_recipient(), $subject, implode("\n", $lines), $headers);
}

/**
 * @param array{values: array<string, string>, invalid: list<OptelyaContactField>} $checked
 */
function optelya_contact_outcome(array $post, array $checked): OptelyaContactOutcome
{
    if (($post[OptelyaContactForm::HONEYPOT] ?? '') !== '') {
        return OptelyaContactOutcome::Honeypot;
    }

    if (optelya_contact_is_rate_limited()) {
        return OptelyaContactOutcome::RateLimited;
    }

    $token = $post[OptelyaContactForm::TOKEN] ?? '';
    $refused = OptelyaContactForm::tokenOutcome(is_string($token) ? $token : '', time(), optelya_contact_secret());

    if ($refused !== null) {
        return $refused;
    }

    if ($checked['invalid'] !== []) {
        return OptelyaContactOutcome::Invalid;
    }

    return optelya_contact_send($checked['values']) ? OptelyaContactOutcome::Sent : OptelyaContactOutcome::MailFailed;
}

/**
 * Posted to the page that shows the form. A success redirects (refreshing
 * does not send twice); a failure renders the page again with what was typed.
 */
function optelya_contact_handle(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || ($_POST[OptelyaContactForm::ACTION_FIELD] ?? '') !== OptelyaContactForm::ACTION) {
        return;
    }

    $post = wp_unslash($_POST);
    $checked = OptelyaContactForm::validate($post);
    $outcome = optelya_contact_outcome($post, $checked);
    optelya_contact_record($outcome);

    if (! $outcome->isSuccess()) {
        optelya_contact_state(['outcome' => $outcome] + $checked);

        return;
    }

    $page = get_permalink();
    $target = add_query_arg(OPTELYA_CONTACT_QUERY, OptelyaContactOutcome::Sent->value, $page !== false ? $page : home_url('/'));
    wp_safe_redirect($target . '#' . OptelyaContactForm::STATUS_ANCHOR, 303);
    exit;
}
add_action('template_redirect', 'optelya_contact_handle');

/**
 * The page title says how the submission went: it is the first thing a screen
 * reader announces when the page comes back.
 */
function optelya_contact_document_title(array $parts): array
{
    $outcome = optelya_contact_state()['outcome'];

    if ($outcome !== null) {
        $parts['title'] = sprintf(
            $outcome->isSuccess()
                /* translators: %s: page title */
                ? __('Demande envoyée : %s', 'optelya')
                /* translators: %s: page title */
                : __('Erreur : %s', 'optelya'),
            (string) ($parts['title'] ?? ''),
        );
    }

    return $parts;
}
add_filter('document_title_parts', 'optelya_contact_document_title');

function optelya_contact_register_admin_page(): void
{
    add_management_page(
        __('Formulaire de contact', 'optelya'),
        __('Contact', 'optelya'),
        'manage_options',
        OPTELYA_CONTACT_ADMIN_PAGE,
        'optelya_contact_render_admin_page',
    );
}
add_action('admin_menu', 'optelya_contact_register_admin_page');

function optelya_contact_render_admin_page(): void
{
    if (! current_user_can('manage_options')) {
        return;
    }

    $stats = get_option(OPTELYA_CONTACT_STATS_OPTION, []);
    get_template_part('components/contact-stats', null, [
        'stats' => is_array($stats) ? $stats : [],
        'recipient' => optelya_contact_recipient(),
    ]);
}

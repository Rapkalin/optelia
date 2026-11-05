<?php

/**
 * Contact form rules: what reaches the e-mail, what a robot runs into, and the
 * monthly counters of Tools → Contact.
 */

$enums = OPTELYA_THEME_DIR . 'inc/enums/';
require_once $enums . 'OptelyaContactField.php';
require_once $enums . 'OptelyaContactOutcome.php';
require_once $enums . 'OptelyaContactForm.php';

const CONTACT_TEST_SECRET = 'test-secret';

/**
 * Keyed by field value for readability, posted under the input names.
 */
function contact_valid_input(array $overrides = []): array
{
    $values = $overrides + [
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
        'company' => 'Analytical Engines',
        'phone' => '',
        'project' => '',
    ];
    $input = [];

    foreach ($values as $key => $value) {
        $input[OptelyaContactField::from($key)->inputName()] = $value;
    }

    return $input;
}

function contact_invalid_fields(array $input): array
{
    return array_map(
        static fn(OptelyaContactField $field): string => $field->value,
        OptelyaContactForm::validate($input)['invalid'],
    );
}

it('accepts the three required fields alone', function () {
    expect(contact_invalid_fields(contact_valid_input()))->toBe([]);
});

it('flags a missing or wrong field', function (array $overrides, string $field) {
    expect(contact_invalid_fields(contact_valid_input($overrides)))->toBe([$field]);
})->with([
    'no name' => [['name' => '   '], 'name'],
    'no company' => [['company' => ''], 'company'],
    'not an e-mail' => [['email' => 'ada@'], 'email'],
    'letters as phone' => [['phone' => 'call me'], 'phone'],
    'too long' => [['project' => str_repeat('a', 501)], 'project'],
    'array instead of text' => [['name' => ['Ada']], 'name'],
]);

it('refuses an e-mail carrying an extra header', function () {
    expect(contact_invalid_fields(contact_valid_input(['email' => "ada@example.com\r\nBcc: spam@example.com"])))
        ->toBe(['email']);
});

it('never posts a field under a WordPress query var', function () {
    foreach (OptelyaContactField::cases() as $field) {
        expect($field->inputName())->toStartWith('contact_');
    }
});

it('keeps every value on one line, without tags', function () {
    $values = OptelyaContactForm::validate(contact_valid_input([
        'company' => "Engines\r\nBcc: spam@example.com",
        'project' => '<script>x</script>A  model',
    ]))['values'];

    expect($values['company'])->toBe('Engines Bcc: spam@example.com');
    expect(OptelyaContactField::Company->clean("Engines\nBcc:\x07x"))->toBe('Engines Bcc: x');
    expect($values['project'])->toBe('xA model');
});

it('accepts usual phone formats', function (string $phone) {
    expect(contact_invalid_fields(contact_valid_input(['phone' => $phone])))->toBe([]);
})->with(['01 23 45 67 89', '+33 (0)1 23 45 67 89', '01.23.45.67.89']);

it('accepts a token of a plausible age', function () {
    $token = OptelyaContactForm::token(1000, CONTACT_TEST_SECRET);

    expect(OptelyaContactForm::tokenOutcome($token, 1000 + OptelyaContactForm::MIN_SECONDS, CONTACT_TEST_SECRET))->toBeNull();
});

it('refuses a form sent too fast or too late', function () {
    $token = OptelyaContactForm::token(1000, CONTACT_TEST_SECRET);

    expect(OptelyaContactForm::tokenOutcome($token, 1001, CONTACT_TEST_SECRET))->toBe(OptelyaContactOutcome::TooFast);
    expect(OptelyaContactForm::tokenOutcome($token, 1001 + OptelyaContactForm::MAX_SECONDS, CONTACT_TEST_SECRET))
        ->toBe(OptelyaContactOutcome::Expired);
});

it('refuses a forged or missing token', function (string $token) {
    expect(OptelyaContactForm::tokenOutcome($token, 5000, CONTACT_TEST_SECRET))->toBe(OptelyaContactOutcome::Expired);
})->with([
    'backdated' => ['1.' . hash_hmac('sha256', '2', CONTACT_TEST_SECRET)],
    'other secret' => [OptelyaContactForm::token(1000, 'other')],
    'empty' => [''],
    'garbage' => ['abc.def'],
]);

it('tells why a value is refused', function () {
    expect(OptelyaContactField::Name->errorMessage(''))->toBe('Ce champ est obligatoire.');
    expect(OptelyaContactField::Project->errorMessage(str_repeat('a', 501)))->toBe('500 caractères au plus.');
    expect(OptelyaContactField::Email->errorMessage('ada@'))->toContain('nom@entreprise.fr');
    expect(OptelyaContactField::Phone->errorMessage('call me'))->toContain('01 23 45 67 89');
});

it('does not tell a fast visitor that the page expired', function () {
    expect(OptelyaContactOutcome::TooFast->message())->not->toBe(OptelyaContactOutcome::Expired->message());
});

it('lets the honeypot pass for a success, nothing else', function () {
    $successes = array_filter(OptelyaContactOutcome::cases(), static fn(OptelyaContactOutcome $o): bool => $o->isSuccess());

    expect(array_values($successes))->toBe([OptelyaContactOutcome::Sent, OptelyaContactOutcome::Honeypot]);
});

it('counts outcomes per month, newest first', function () {
    $stats = OptelyaContactForm::record([], OptelyaContactOutcome::Sent, '2026-10');
    $stats = OptelyaContactForm::record($stats, OptelyaContactOutcome::Sent, '2026-11');
    $stats = OptelyaContactForm::record($stats, OptelyaContactOutcome::Sent, '2026-11');
    $stats = OptelyaContactForm::record($stats, OptelyaContactOutcome::Honeypot, '2026-11');

    expect($stats)->toBe([
        '2026-11' => ['sent' => 2, 'honeypot' => 1],
        '2026-10' => ['sent' => 1],
    ]);
});

it('forgets the months beyond the retention', function () {
    $stats = [];

    for ($month = 1; $month <= OptelyaContactForm::MONTHS_KEPT + 3; $month++) {
        $stats = OptelyaContactForm::record($stats, OptelyaContactOutcome::Sent, sprintf('%d-%02d', 2024 + intdiv($month - 1, 12), ($month - 1) % 12 + 1));
    }

    expect($stats)->toHaveCount(OptelyaContactForm::MONTHS_KEPT);
    expect(array_key_first($stats))->toBe('2026-03');
    expect(array_key_last($stats))->toBe('2024-04');
});

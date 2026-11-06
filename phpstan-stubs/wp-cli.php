<?php

/**
 * The part of WP-CLI the project calls, for PHPStan only (never loaded).
 * php-stubs/wp-cli-stubs requires WordPress stubs below 7.
 */

class WP_CLI
{
    public static function add_command(string $name, callable|object|string $callable, array $args = []): bool {}

    public static function success(string $message): void {}

    public static function warning(string $message): void {}

    public static function log(string $message): void {}
}

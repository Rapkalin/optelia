<?php

/**
 * Pest bootstrap.
 *
 * The suite runs OUTSIDE WordPress: no database, no wp-load. Files under test
 * guard themselves with `defined('ABSPATH')`, so the constant is stubbed here.
 */

if (! defined('ABSPATH')) {
    define('ABSPATH', dirname(__DIR__) . '/website/wordpress-core/');
}

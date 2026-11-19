<?php

/**
 * The vendors of the products an element names, as labels: first element of
 * a card, and of an entry of the choice list.
 *
 * @param list<string> $args['vendors']
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

$vendors = array_filter(array_map('strval', (array) ($args['vendors'] ?? [])), static fn(string $vendor): bool => $vendor !== '');

if ($vendors === []) {
    return;
}
?>
<span class="vendor-chips">
    <?php foreach ($vendors as $vendor) : ?>
        <span class="vendor-chip"><?php echo esc_html($vendor); ?></span>
    <?php endforeach; ?>
</span>

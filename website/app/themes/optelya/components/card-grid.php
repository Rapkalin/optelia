<?php

/**
 * Cards side by side (components/card.php for each). By default a short last
 * row stretches; `keep_width` keeps every card at the width of a full row (a
 * lone card does not span the page).
 *
 * @param list<array<string, mixed>> $args['cards']
 * @param bool                       $args['keep_width']
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

$cards = (array) ($args['cards'] ?? []);

if ($cards === []) {
    return;
}
?>
<div class="card-grid<?php echo ! empty($args['keep_width']) ? ' card-grid--keep-width' : ''; ?>">
    <?php foreach ($cards as $card) : ?>
        <?php get_template_part('components/card', null, ['card' => $card]); ?>
    <?php endforeach; ?>
</div>

<?php

/**
 * Contact → Tableau de bord: submissions per month and per outcome. Counters only, no
 * request content — see readme/contact.md.
 *
 * @param array<string, array<string, int>> $args['stats']
 * @param string                            $args['recipient']
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

$stats = (array) ($args['stats'] ?? []);
$outcomes = OptelyaContactOutcome::cases();
?>
<div class="wrap">
    <h1><?php esc_html_e('Formulaire de contact', 'optelya'); ?></h1>
    <p>
        <?php printf(
            /* translators: %s: recipient e-mail address */
            esc_html__('Les demandes sont envoyées à %s (modifiable dans Contact → Configuration). Le site ne garde que ces compteurs, aucune demande.', 'optelya'),
            '<strong>' . esc_html((string) ($args['recipient'] ?? '')) . '</strong>',
        ); ?>
    </p>

    <?php if ($stats === []) : ?>
        <p><?php esc_html_e('Aucun envoi pour l’instant.', 'optelya'); ?></p>
    <?php else : ?>
        <table class="widefat striped">
            <thead>
                <tr>
                    <th scope="col"><?php esc_html_e('Mois', 'optelya'); ?></th>
                    <?php foreach ($outcomes as $outcome) : ?>
                        <th scope="col"><?php echo esc_html($outcome->label()); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stats as $month => $counts) : ?>
                    <tr>
                        <th scope="row"><?php echo esc_html((string) $month); ?></th>
                        <?php foreach ($outcomes as $outcome) : ?>
                            <td><?php echo esc_html((string) (int) ($counts[$outcome->value] ?? 0)); ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="description">
            <?php esc_html_e('« Envoyées » sont les demandes reçues par e-mail. Les autres colonnes sont des envois refusés : robots (champ piège, remplie trop vite, trop d’envois) ou visiteurs à qui le formulaire a demandé de corriger ou de renvoyer.', 'optelya'); ?>
        </p>
    <?php endif; ?>
</div>

<?php
/**
 * Logica per i campi del single formazione
 * 
 * @package Bootscore Child
 * @version 1.0.0
 */

// Prevent direct access
defined('ABSPATH') || exit;

// Recupera tutti i valori dei campi
$corso_classe = rwmb_meta('corso_claase');
$corso_titolo = rwmb_meta('corso_titolo');
$corso_durata = rwmb_meta('corso_durata');
$corso_cfu = rwmb_meta('corso_cfu');


?>

<!-- Contenuto HTML dei campi condizionali -->
<?php if (!empty($corso_classe)) : ?>
<div class="col border-end border-white py-2">
    <strong>Classe</strong><br>
    <span><?php echo esc_html($corso_classe); ?></span>
</div>
<?php endif; ?>

<?php if (!empty($corso_titolo)) : ?>
<div class="col border-end border-white py-2">
    <strong>Titolo</strong><br>
    <span><?php echo esc_html($corso_titolo); ?></span>
</div>
<?php endif; ?>

<?php if (!empty($corso_durata)) : ?>
<div class="col border-end border-white py-2">
    <strong>Durata</strong><br>
    <span><?php echo esc_html($corso_durata); ?></span>
</div>
<?php endif; ?>

<?php if (!empty($corso_cfu)) : ?>
<div class="col py-2">
    <strong>CFU</strong><br>
    <span><?php echo esc_html($corso_cfu); ?></span>
</div>
<?php endif; ?>


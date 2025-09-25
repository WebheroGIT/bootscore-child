<?php
/**
 * Logica per i campi del single dottorato
 * 
 * @package Bootscore Child
 * @version 1.0.0
 */

// Prevent direct access
defined('ABSPATH') || exit;

// Recupera tutti i valori dei campi per dottorato
// Adattare i nomi dei campi in base ai meta fields del post type dottorato
$dottorato_ciclo = rwmb_meta('dottorato_ciclo');
$dottorato_durata = rwmb_meta('dottorato_durata');
$dottorato_settore = rwmb_meta('dottorato_settore');
$dottorato_coordinatore = rwmb_meta('dottorato_coordinatore');


?>

<!-- Contenuto HTML dei campi condizionali per dottorato -->
<?php if (!empty($dottorato_ciclo)) : ?>
<div class="col border-end border-white py-2">
    <strong>Ciclo</strong><br>
    <span><?php echo esc_html($dottorato_ciclo); ?></span>
</div>
<?php endif; ?>

<?php if (!empty($dottorato_durata)) : ?>
<div class="col border-end border-white py-2">
    <strong>Durata</strong><br>
    <span><?php echo esc_html($dottorato_durata); ?></span>
</div>
<?php endif; ?>

<?php if (!empty($dottorato_settore)) : ?>
<div class="col border-end border-white py-2">
    <strong>Settore</strong><br>
    <span><?php echo esc_html($dottorato_settore); ?></span>
</div>
<?php endif; ?>

<?php if (!empty($dottorato_coordinatore)) : ?>
<div class="col py-2">
    <strong>Coordinatore</strong><br>
    <span><?php echo esc_html($dottorato_coordinatore); ?></span>
</div>
<?php endif; ?>
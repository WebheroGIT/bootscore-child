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

<?php
// Conta quanti campi hanno un valore per determinare la griglia
// Crea lista di item esistenti con label e valore
$items = [];
if (!empty($corso_classe)) {
    $items[] = ['label' => 'Classe', 'value' => $corso_classe];
}
if (!empty($corso_titolo)) {
    $items[] = ['label' => 'Titolo', 'value' => $corso_titolo];
}
if (!empty($corso_durata)) {
    $items[] = ['label' => 'Durata', 'value' => $corso_durata];
}
if (!empty($corso_cfu)) {
    $items[] = ['label' => 'CFU', 'value' => $corso_cfu];
}

$count = count($items);

if ($count > 0) : ?>
<div class="grid-<?php echo esc_attr($count); ?> text-white text-center mt-4 g-0 border-top border-white">
  <?php foreach ($items as $index => $item) :
      $is_last = ($index === $count - 1);
      $col_classes = 'col py-2';
      if (!$is_last) {
          $col_classes .= ' border-end border-white';
      }
  ?>
    <div class="<?php echo esc_attr($col_classes); ?>">
        <strong><?php echo esc_html($item['label']); ?></strong><br>
        <span><?php echo esc_html($item['value']); ?></span>
    </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>


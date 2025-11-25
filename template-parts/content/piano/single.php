<?php
// Recupera i post formazione collegati al piano corrente
$formazioni = MB_Relationships_API::get_connected([
    'id'   => 'rel-pian-form', // Sostituisci con l’ID esatto che hai usato nella registrazione della relazione
    'from' => get_the_ID(),
    'type' => 'to',
]);

$formazione_id = null;
$cat_name = '';
$has_valid_cat = false;

if ($formazioni) {
    $formazione_id = $formazioni[0]->ID;

    // Controllo categorie della formazione
    $valid_cats = [4, 5, 9];
    $terms = wp_get_post_terms($formazione_id, 'cat-formazione');

    foreach ($terms as $term) {
        if (in_array($term->term_id, $valid_cats)) {
            $has_valid_cat = true;
            $cat_name = $term->name;
            break;
        }
    }
}
?>

<div class="custom-template-piano">

<?php if ($formazione_id): ?>
    <?php if ($has_valid_cat): ?>
    <div class="rounded-3 bg-primary text-white mb-5">
      <div class="row align-items-center">
        <div class="col-md-7 p-4">
          <small class="text-uppercase fw-semibold"><?php echo esc_html($cat_name); ?></small>
          <h1 class="mt-1">
            <a href="<?php echo get_permalink( $formazione_id ); ?>" class="text-white">
              <?php echo get_the_title( $formazione_id ); ?>
            </a>
          </h1>

          <div class="row text-white text-center mt-4 g-0 border-top border-bottom border-white">
            <div class="col border-end border-white py-2">
              <strong style="color:#fff;">Classe</strong><br>
              <span><?php echo rwmb_meta('corso_claase', '', $formazione_id); ?></span>
            </div>
            <div class="col border-end border-white py-2">
              <strong style="color:#fff;">Titolo</strong><br>
              <span><?php // echo get_the_title($formazione_id); ?><?php echo rwmb_meta('corso_titolo', '', $formazione_id); ?></span>
            </div>
            <div class="col border-end border-white py-2">
              <strong style="color:#fff;">Durata</strong><br>
              <span><?php echo rwmb_meta('corso_durata', '', $formazione_id); ?></span>
            </div>
            <div class="col py-2">
              <strong style="color:#fff;">CFU</strong><br>
              <span><?php echo rwmb_meta('corso_cfu', '', $formazione_id); ?></span>
            </div>
          </div>
        </div>
        <div class="col-md-5 text-center">
          <?php if (has_post_thumbnail($formazione_id)): ?>
            <?php echo get_the_post_thumbnail($formazione_id, 'medium_large', ['class' => 'img-fluid rounded']); ?>
          <?php else: ?>
            <?php echo wp_get_attachment_image(22, 'medium_large', false, ['class' => 'img-fluid rounded']); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>
<?php endif; ?>

<hr class="my-4">

<h2 class="mb-3"><?php the_title(); ?></h2>
<div><?php the_content(); ?></div>

</div>

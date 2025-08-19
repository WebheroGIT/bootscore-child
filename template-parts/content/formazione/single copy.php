<?php
$taxonomy = 'cat-formazione';
$valid_cats = [4, 5, 9];
$has_valid_cat = false;

// Recupera le categorie assegnate
$terms = wp_get_post_terms(get_the_ID(), $taxonomy);

// Verifica se almeno una categoria è tra quelle richieste
foreach ($terms as $term) {
  if (in_array($term->term_id, $valid_cats)) {
    $has_valid_cat = true;
    $cat_name = $term->name;
    break;
  }
}

if ($has_valid_cat) :
?>

<div class="rounded-3 bg-primary text-white mb-5">
  <div class="row align-items-center">
    <div class="col-md-7 p-4">
      <small class="text-uppercase fw-semibold"><?php echo esc_html($cat_name); ?></small>
      <h1 class="mt-1"><?php the_title(); ?></h1>

      <div class="row text-white text-center mt-4 g-0 border-top border-bottom border-white">
        <div class="col border-end border-white py-2">
          <strong>Dipartimento</strong><br>
          <span>-</span> <!-- Sostituisci con dato reale -->
        </div>
        <div class="col border-end border-white py-2">
          <strong>Titolo</strong><br>
          <span><?php the_title(); ?></span>
        </div>
        <div class="col border-end border-white py-2">
          <strong>Durata</strong><br>
          <span>-</span> <!-- Sostituisci con dato reale -->
        </div>
        <div class="col py-2">
          <strong>CFU</strong><br>
          <span>-</span> <!-- Sostituisci con dato reale -->
        </div>
      </div>
    </div>

    <div class="col-md-5 text-center">
      <div class="relative">
        <?php if (has_post_thumbnail()) : ?>
          <?php the_post_thumbnail('medium_large', ['class' => 'img-fluid rounded']); ?>
        <?php else : ?>
          <?php echo wp_get_attachment_image(22, 'medium_large', false, ['class' => 'img-fluid rounded']); ?>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>

<?php endif; ?>

<!-- CONTENUTO STANDARD -->
<div class="mt-4">
  <?php the_content(); ?>
</div>

<?php
$taxonomy = 'cat-formazione';
$valid_cats = [4, 5, 9, 27];
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

<div class="rounded-3 overflow-hidden bg-primary text-white mb-5">
  <div class="grid-7-5 grid-lg-1 row-iscrizioni-formazione align-items-stretch position-relative">
    <a href="#" class="btn btn-secondary btn-iscrizioni-formazione position-absolute bottom-0 end-0 px-4 py-2 w-auto" style="z-index:3;min-width:150px;">Iscriviti</a>
    <div class="">
      <div class="p-4 flex-grow-1">

      <!-- stampa relazione dipartimento -->
      <?php
      // Query per recuperare il Dipartimento associato tramite la relazione 'rel-dip-form'
      $dipartimento = get_posts([
          'post_type' => 'dipartimento', // Tipo di post 'dipartimento'
          'relationship' => [
              'id' => 'rel-dip-form', // La relazione che collega 'formazione' e 'dipartimento'
              'to' => get_the_ID(), // Il post di 'formazione' è il destinatario della relazione
          ],
          'posts_per_page' => 1
      ]);

      // Stampa il blocco solo se esiste un dipartimento valido
      if ($dipartimento) : ?>
          <!-- stampa relazione dipartimento -->
          <div class="py-2">
              <strong>Dipartimento</strong>
              <span>
                  <?php echo get_the_title($dipartimento[0]->ID); ?>
              </span>
          </div>

      <?php endif; ?>
      <!-- END relazione dipartimento -->

        <small class="text-uppercase fw-semibold"><?php echo esc_html($cat_name); ?></small>
        <h1 class="mt-1"><?php the_title(); ?></h1>

        <div class="grid-4 text-white text-center mt-4 g-0 border-top border-bottom border-white">
          <div class="col border-end border-white py-2">
              <strong>Classe</strong><br>
              <span><?php echo rwmb_meta( 'corso_claase' ); ?></span> <!-- Sostituito con il dato reale dal campo Dipartimento -->
          </div>
          <div class="col border-end border-white py-2">
              <strong>Titolo</strong><br>
              <span><?php // the_title(); ?><?php echo rwmb_meta( 'corso_titolo' ); ?> </span>
          </div>
          <div class="col border-end border-white py-2">
              <strong>Durata</strong><br>
              <span><?php echo rwmb_meta( 'corso_durata' ); ?></span> <!-- Sostituito con il dato reale dal campo Durata -->
          </div>
          <div class="col py-2">
              <strong>CFU</strong><br>
              <span><?php echo rwmb_meta( 'corso_cfu' ); ?></span> <!-- Sostituito con il dato reale dal campo CFU -->
          </div>
        </div>
      </div>
    </div>

    <div class="col-featured position-relative h-100">
      <div class="img-wrapper position-absolute top-0 start-0 end-0 bottom-0">
        <?php if (has_post_thumbnail()) : ?>
          <?php the_post_thumbnail('large', ['class' => 'img-fluid h-100 w-100 img-formazione']); ?>
        <?php else : ?>
          <?php echo wp_get_attachment_image(22, 'large', false, ['class' => 'img-fluid h-100 w-100 img-formazione']); ?>
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

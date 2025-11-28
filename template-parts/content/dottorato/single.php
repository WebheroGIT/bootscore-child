<?php
$taxonomy = 'cat-dottorato';
$valid_cats = []; // Da definire le categorie valide per dottorato
$has_valid_cat = false;

// Recupera le categorie assegnate
$terms = wp_get_post_terms(get_the_ID(), $taxonomy);

// Verifica se almeno una categoria è tra quelle richieste
if (!is_wp_error($terms) && is_array($terms)) {
  foreach ($terms as $term) {
    if (in_array($term->term_id, $valid_cats)) {
      $has_valid_cat = true;
      $cat_name = $term->name;
      
      // Trasforma il nome della categoria per visualizzazione singolare
      // Adattare in base alle categorie di dottorato
      
      break;
    }
  }
}

// Per ora mostriamo sempre il template per dottorato
$has_valid_cat = true;
$cat_name = 'Dottorato di Ricerca';

if ($has_valid_cat) :
?>

<div class="rounded-3 overflow-hidden bg-primary text-white mb-5">
  <div class="grid-7-5 grid-xl-1 row-iscrizioni-formazione align-items-stretch position-relative">
    <a href="https://platform.unimarconi.it/shibd" target="_blank" class="btn btn-secondary btn-iscrizioni-formazione position-absolute bottom-0 end-0 px-4 py-2 w-auto" style="z-index:3;min-width:150px;">Iscriviti</a>
    <div class="">
      <div class="p-4 flex-grow-1">

      <!-- stampa relazione dipartimento -->
      <?php
      // Query per recuperare il Dipartimento associato tramite la relazione
      // Temporaneamente disabilitato fino a quando la relazione 'rel-dip-dott' non sarà configurata
      $dipartimento = false; // get_posts([
      //     'post_type' => 'dipartimento',
      //     'relationship' => [
      //         'id' => 'rel-dip-dott',
      //         'to' => get_the_ID(),
      //     ],
      //     'posts_per_page' => 1
      // ]);

      // Stampa il blocco solo se esiste un dipartimento valido
      if ($dipartimento) : ?>
          <!-- stampa relazione dipartimento -->
          <div class="py-2">
              <!-- TODO <strong>Dipartimento</strong> -->
              <span>
                  <?php echo get_the_title($dipartimento[0]->ID); ?>
              </span>
          </div>

      <?php endif; ?>
      <!-- END relazione dipartimento -->

        <small class="text-uppercase fw-semibold"><?php echo esc_html($cat_name); ?></small>
        <h1 class="mt-1"><?php the_title(); ?></h1>

        <div class="grid-4 text-white text-center mt-4 g-0 border-top border-bottom border-white">
          
        <?php
          // Include con controllo esistenza file per dottorato
          if (locate_template('template-parts/content/dottorato/logic-single-dottorato.php')) {
              get_template_part('template-parts/content/dottorato/logic-single-dottorato');
          }
        ?>

        </div>
      </div>
    </div>

    <div class="col-featured position-relative h-100 overflow-hidden">
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
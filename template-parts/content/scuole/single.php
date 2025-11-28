<?php
$taxonomy = 'cat-formazione';
$excluded_cats = []; // Categorie da escludere dall'header
$show_header = true; // Di default mostriamo l'header

// Recupera le categorie assegnate
$terms = wp_get_post_terms(get_the_ID(), $taxonomy);

// Verifica se il post ha categorie da escludere
foreach ($terms as $term) {
  if (in_array($term->term_id, $excluded_cats)) {
    $show_header = false; // Se trova una categoria esclusa, non mostrare l'header
    break;
  }
}

// Se dobbiamo mostrare l'header, prendi il nome della prima categoria
$cat_name = '';
if ($show_header && !empty($terms)) {
  $term = $terms[0];
  $cat_name = $term->name;
  
  // Trasforma il nome della categoria per visualizzazione singolare
  if ($term->slug === 'lauree-triennali') {
    $cat_name = 'Laurea Triennale';
  }
}

if ($show_header) :
?>

<div class="rounded-3 overflow-hidden bg-primary text-white mb-5">
  <div class="grid-7-5 grid-xl-1 row-iscrizioni-formazione align-items-stretch position-relative">
    
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
              <!-- TODO <strong>Dipartimento</strong> -->
              <span>
                  <?php echo get_the_title($dipartimento[0]->ID); ?>
              </span>
          </div>

      <?php endif; ?>
      <!-- END relazione dipartimento -->

        <small class="text-uppercase fw-semibold"><?php echo esc_html($cat_name); ?></small>
        <h1 class="mt-1"><?php the_title(); ?></h1>

        <?php
          // Include con controllo esistenza file
          if (locate_template('template-parts/content/formazione/logic-single-formazione.php')) {
              get_template_part('template-parts/content/formazione/logic-single-formazione');
          }
        ?>
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

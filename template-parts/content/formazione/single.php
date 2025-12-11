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

// Logica a cascata per determinare la categoria da mostrare
$cat_name = '';
if ($show_header) {
  // 1. PRIORITÀ MASSIMA: Controlla il campo personalizzato 'corso_categoria_fittizia'
  $categoria_fittizia = '';
  
  // Prova diversi modi per recuperare il campo personalizzato
  if (function_exists('get_field')) {
    $categoria_fittizia = get_field('corso_categoria_fittizia');
  }
  // Se ACF non funziona, prova con get_post_meta
  if (empty($categoria_fittizia)) {
    $categoria_fittizia = get_post_meta(get_the_ID(), 'corso_categoria_fittizia', true);
  }
  
  if (!empty($categoria_fittizia)) {
    // Se il campo contiene solo il simbolo "-", non stampare nulla
    if (trim($categoria_fittizia) === '-') {
      $cat_name = '';
    } else {
      $cat_name = $categoria_fittizia;
    }
  }
  // 2. SECONDA PRIORITÀ: Controlla se c'è una primary term impostata con RankMath
  elseif (!empty($terms)) {
    $primary_term = null;
    
    // Prova diversi modi per recuperare la primary term di RankMath
    $primary_term_id = null;
    
    // Metodo 1: Funzione RankMath standard
    if (function_exists('rank_math_get_primary_term')) {
      $primary_term_id = rank_math_get_primary_term($taxonomy);
    }
    
    // Metodo 2: Se il primo non funziona, prova con get_post_meta
    if (!$primary_term_id) {
      $primary_term_id = get_post_meta(get_the_ID(), 'rank_math_primary_' . $taxonomy, true);
    }
    
    // Metodo 3: Prova con il meta key alternativo
    if (!$primary_term_id) {
      $primary_term_id = get_post_meta(get_the_ID(), '_rank_math_primary_term_' . $taxonomy, true);
    }
    
    // Se abbiamo trovato una primary term, cerchiamola tra i terms
    if ($primary_term_id) {
      foreach ($terms as $term) {
        if ($term->term_id == $primary_term_id) {
          $primary_term = $term;
          break;
        }
      }
    }
    
    // Se abbiamo una primary term, usala, altrimenti usa la prima categoria
    $selected_term = $primary_term ? $primary_term : $terms[0];
    $cat_name = $selected_term->name;
    
    // Trasforma il nome della categoria per visualizzazione singolare
    if ($selected_term->slug === 'lauree-triennali') {
      $cat_name = 'Laurea Triennale';
    }
  }
}

if ($show_header) :
?>

<div class="rounded-3 overflow-hidden bg-primary text-white mb-5">
  <div class="grid-7-5 grid-xl-1 row-iscrizioni-formazione align-items-stretch position-relative">
    <a href="https://platform.unimarconi.it/shibd" target="_blank" class="btn btn-secondary btn-iscrizioni-formazione position-absolute bottom-0 end-0 px-4 py-2 w-auto" style="z-index:3;min-width:150px;">Iscriviti</a>
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
      <h1 class="d-flex flex-column">
        <?php if (!empty($cat_name)) : ?>
          <small class="text-uppercase f-small fw-semibold mb-1"><?php echo esc_html($cat_name); ?></small>
        <?php endif; ?>
        <span class=""><?php the_title(); ?></span>
      </h1>

        <?php
          // Include con controllo esistenza file
          if (locate_template('template-parts/content/formazione/logic-single-formazione.php')) {
              get_template_part('template-parts/content/formazione/logic-single-formazione');
          }
        ?>
      </div>
    </div>

    <div class="col-featured position-relative h-100 overflow-hidden">
      <div class="img-wrapper position-absolute top-0 start-0 end-0 bottom-0" style="aspect-ratio: 14 / 9;height: 100%;width: 100%;">
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

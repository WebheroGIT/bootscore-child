<?php
/**
 * Template part for displaying formazione posts in grid layout
 * Used by archive.php for post type 'formazione' and taxonomy 'cat-formazione'
 *
 * @package Bootscore
 */

// Exit if accessed directly
defined('ABSPATH') || exit;
?>

<!-- Filtri prima della griglia -->
<?php if (function_exists('wpgb_render_facet')) : ?>
<div class="formazione-filters mb-2 grid-3 gap-3 grid-md-1">
    <?php wpgb_render_facet(['id' => 1, 'grid' => 'wpgb-content']); ?>
    <?php wpgb_render_facet(['id' => 2, 'grid' => 'wpgb-content']); ?>
    
    <?php 
    // Facet 3 solo in post-type-archive-formazione o in tax-cat-formazione term-master term-6
    $show_facet_3 = false;
    
    if (is_post_type_archive('formazione')) {
        $show_facet_3 = true;
    } elseif (is_tax('cat-formazione')) {
        $queried_object = get_queried_object();
        if ($queried_object && $queried_object->term_id == 6) { // term-6 (master)
            $show_facet_3 = true;
        }
    }
    
    if ($show_facet_3) : ?>
        <?php wpgb_render_facet(['id' => 3, 'grid' => 'wpgb-content']); ?>
    <?php endif; ?>
    
    <?php wpgb_render_facet(['id' => 4, 'grid' => 'wpgb-content']); ?>
</div>
<?php endif; ?>

<?php
// Crea una query personalizzata per WPGB (stesso pattern del tuo sito funzionante)
$args = array(
    'post_type'      => 'formazione',
    'wp_grid_builder' => 'wpgb-content', // Stesso nome del tuo esempio funzionante
    'posts_per_page' => get_option('posts_per_page'),
    'paged'          => get_query_var('paged') ? get_query_var('paged') : 1
);

// Se siamo su una taxonomy, mantieni il filtro
if (is_tax('cat-formazione')) {
    $current_term = get_queried_object();
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'cat-formazione',
            'field'    => 'term_id',
            'terms'    => $current_term->term_id,
        ),
    );
}

$formazione_query = new WP_Query($args);
?>

<!-- Grid container con stessa classe del tuo esempio funzionante -->
<div class="grid-3 grid-lg-2 grid-md-1 gap-4 grid-box-formazione mb-5 wpgb-content">
     
  <?php if ($formazione_query->have_posts()) : ?>
    <?php while ($formazione_query->have_posts()) : $formazione_query->the_post(); ?>
  
    <?php do_action('bootscore_before_loop_item', 'formazione-grid'); ?>

      <article id="post-<?php the_ID(); ?>" <?php post_class('card formazione-card position-relative'); ?>>
        
        <?php if (has_post_thumbnail()) : ?>
          <div class="card-img-wrapper">
            <a href="<?php the_permalink(); ?>">
              <?php the_post_thumbnail('medium', ['class' => 'card-img-top', 'alt' => get_the_title()]); ?>
            </a>
          </div>
        <?php endif; ?>
        
        <?php 
        // Badge corso_modalita in alto a sinistra
        $corso_modalita = rwmb_meta('corso_modalita', '', get_the_ID());
        if (!empty($corso_modalita)) : ?>
          <div class="position-absolute top-0 start-0 m-2">
            <span class="badge bg-warning text-dark fw-bold"><?php rwmb_the_value('corso_modalita', '', get_the_ID()); ?></span>
          </div>
        <?php endif; ?>
        
        <div class="card-body d-flex flex-column">
          
          <!-- Category and attributes badges -->
          <div class="mb-2">
            <?php 
            // Get cat-formazione taxonomy terms
            $terms = get_the_terms(get_the_ID(), 'cat-formazione');
            if ($terms && !is_wp_error($terms)) :
              foreach ($terms as $term) : 
                // Trasforma il nome della categoria per visualizzazione singolare
                $cat_name = $term->name;
                if ($term->slug === 'lauree-triennali') {
                  $cat_name = 'Laurea Triennale';
                }
                ?>
                <span class=""><?php echo esc_html($cat_name); ?> in</span>
              <?php endforeach;
            endif;
            
            // Get custom attributes if they exist
            $attributes = get_post_meta(get_the_ID(), 'attributo', true);
            if ($attributes) : ?>
              <span class="badge bg-info me-1"><?php echo esc_html($attributes); ?></span>
            <?php endif; ?>
          </div>
          
          <h5 class="card-title">
            <a href="<?php the_permalink(); ?>" class="text-decoration-none text-dark">
              <?php the_title(); ?>
            </a>
          </h5>
          
          <?php if (has_excerpt()) : ?>
            <p class="card-text flex-grow-1 excerpt-4-lines small"><?php echo wp_strip_all_tags(get_the_excerpt()); ?></p>
          <?php endif; ?>
        </div>

        <div class="card-footer bg-primary text-light">
          <!-- Green band with "Scopri di più" -->
          <div class="d-flex gap-2 justify-content-between">
    
          <?php
          // Recupera i valori dei campi CFU e durata
          $corso_cfu = rwmb_meta('corso_cfu', '', get_the_ID());
          $corso_durata = rwmb_meta('corso_durata', '', get_the_ID());

          // Mostra CFU e durata se presenti (indipendentemente da corso_modalita)
          if (!empty($corso_cfu) || !empty($corso_durata)) {
            echo '<div class="d-flex gap-1">'; // Apri il div contenitore
            
            if (!empty($corso_cfu)) {
              echo '<p class="m-0 small fw-bold">' . esc_html($corso_cfu) . ' CFU</p>';
              if (!empty($corso_durata)) {
                echo '<p class="m-0 small">|</p>';
              }
            }
            
            if (!empty($corso_durata)) {
              echo '<p class="m-0 small fw-bold">' . esc_html($corso_durata) . '</p>';
            }
            
            echo '</div>'; // Chiudi il div contenitore
          }
          ?>
          
            <a href="<?php the_permalink(); ?>" class="m-0 small text-light">
              Scopri di più
            </a>
          </div>
          
        </div>
        
      </article>

    <?php do_action('bootscore_after_loop_item', 'formazione-grid'); ?>

    <?php endwhile; ?>
  <?php endif; ?>
  
</div>

<?php wp_reset_postdata(); ?>
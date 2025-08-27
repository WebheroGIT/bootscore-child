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

<!-- Grid container for formazione posts -->
<div class="grid-3 grid-lg-2 grid-md-1 gap-4 grid-box-formazione mb-5">
  
  <?php while (have_posts()) : the_post(); ?>
  
  <?php do_action('bootscore_before_loop_item', 'formazione-grid'); ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class('card formazione-card'); ?>>
      
      <?php if (has_post_thumbnail()) : ?>
        <div class="card-img-wrapper">
          <a href="<?php the_permalink(); ?>">
            <?php the_post_thumbnail('medium', ['class' => 'card-img-top', 'alt' => get_the_title()]); ?>
          </a>
        </div>
      <?php endif; ?>
      
      <div class="card-body d-flex flex-column">
        
        <!-- Category and attributes badges -->
        <div class="mb-2">
          <?php 
          // Get cat-formazione taxonomy terms
          $terms = get_the_terms(get_the_ID(), 'cat-formazione');
          if ($terms && !is_wp_error($terms)) :
            foreach ($terms as $term) : ?>
              <span class=""><?php echo esc_html($term->name); ?> in</span>
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
        // Recupera i valori dei campi
        $corso_modalita = rwmb_meta('corso_modalita', '', get_the_ID());
        $corso_cfu = rwmb_meta('corso_cfu', '', get_the_ID());
        $corso_durata = rwmb_meta('corso_durata', '', get_the_ID());

        // Controlla se almeno uno dei tre campi è presente
        if (!empty($corso_modalita) || !empty($corso_cfu) || !empty($corso_durata)) {
          echo '<div class="d-flex gap-1">'; // Apri il div contenitore
          
          if (!empty($corso_modalita)) {
            // Display the label instead of the value
            echo '<p class="m-0 small fw-bold">';
            rwmb_the_value('corso_modalita', '', get_the_ID());
            echo '</p>';
          } else {
            // If corso_modalita is not present, check for corso_cfu and corso_durata
            if (!empty($corso_cfu)) {
              echo '<p class="m-0 small fw-bold">' . esc_html($corso_cfu) . ' CFU</p><p class="m-0 small">|</p>';
            }
            
            if (!empty($corso_durata)) {
              echo '<p class="m-0 small fw-bold">' . esc_html($corso_durata) . '</p>';
            }
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
  
</div>
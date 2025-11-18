<?php
/**
 * Template part for displaying category posts in grid layout
 * Uses the same visualization as the [articoli] shortcode
 * Used by archive.php for category archives
 *
 * @package Bootscore Child
 */

// Exit if accessed directly
defined('ABSPATH') || exit;
?>

<!-- CSS responsive per l'altezza minima del card-body -->
<style>
    @media (max-width: 767px) {
        .news-card-body { min-height: 190px !important; }
    }
    @media (min-width: 768px) {
        .news-card-body { min-height: 240px !important; }
    }
</style>

<div class="row row-cols-1 row-cols-md-3 g-4 wpgb-content mb-5">

  <?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>

      <?php do_action('bootscore_before_loop_item', 'category-grid'); ?>

      <div class="col h-100">
        
        <article id="post-<?php the_ID(); ?>" <?php post_class('card mb-4 d-flex flex-column'); ?> style="border-radius: 10px;overflow:hidden;height:100%">

          <!-- Featured image in alto (stampa sempre come nello shortcode articoli) -->
          <div class="card-img-top" style="height: 200px; overflow: hidden; position: relative;">
            <a href="<?php the_permalink(); ?>" style="display: block; width: 100%; height: 100%;">
              <?php 
              $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
              if ($thumbnail_url) {
                echo '<div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: url(' . esc_url($thumbnail_url) . '); background-size: cover; background-position: center;"></div>';
              } else {
                // Fallback se non c'è immagine
                echo '<div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: #e9ecef;"></div>';
              }
              ?>
            </a>
          </div>

          <!-- Box grigio con titolo, excerpt e data -->
          <div class="card-body bg-light p-4 flex-grow-1 news-card-body">
            <a href="<?php the_permalink(); ?>" class="text-dark clickable-parent">
              <h3 class="fs-5"><?php the_title(); ?></h3>
            </a>
            
            <?php if (has_excerpt()) : ?>
              <p class="card-text flex-grow-1 excerpt-4-lines small mt-2"><?php echo wp_strip_all_tags(get_the_excerpt()); ?></p>
            <?php endif; ?>
            
            <p class="text-muted mb-0"><?php echo get_the_date('d M Y'); ?></p>
          </div>

          <!-- Footer con link "Continua a leggere" -->
          <div class="card-footer bg-primary text-white py-3 mt-auto">
            <div class="text-center">
              <a href="<?php the_permalink(); ?>" class="text-white"><?php echo esc_html__('Continua a leggere', 'bootscore'); ?></a>
            </div>
          </div>

        </article>

      </div>

      <?php do_action('bootscore_after_loop_item', 'category-grid'); ?>

    <?php endwhile; ?>
  <?php endif; ?>

</div>

<?php
// La paginazione viene gestita da archive.php dopo questo template part
// Non serve wp_reset_postdata() perché usiamo la query globale
?>



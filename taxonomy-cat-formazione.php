<?php

/**
 * The template for displaying cat-formazione taxonomy archives
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Bootscore
 * @version 6.2.0
 */

// Exit if accessed directly
defined('ABSPATH') || exit;

get_header();
?>

  <div id="content" class="site-content <?= apply_filters('bootscore/class/container', 'container', 'taxonomy-cat-formazione'); ?> <?= apply_filters('bootscore/class/content/spacer', 'pt-4 pb-5', 'taxonomy-cat-formazione'); ?>">
    <div id="primary" class="content-area">
      
      <?php do_action('bootscore_after_primary_open', 'taxonomy-cat-formazione'); ?>

      <div class="row">
        <div class="col-12">

          <main id="main" class="site-main">

            <div class="entry-header mb-4">
              <?php do_action( 'bootscore_before_title', 'taxonomy-cat-formazione' ); ?>
              <?php the_archive_title('<h1 class="entry-title text-center ' . apply_filters('bootscore/class/entry/title', '', 'taxonomy-cat-formazione') . '">', '</h1>'); ?>
              <?php do_action( 'bootscore_after_title', 'taxonomy-cat-formazione' ); ?>
              <?php the_archive_description( '<div class="archive-description text-center ' . apply_filters('bootscore/class/entry/archive-description', '') . '">', '</div>' ); ?>
            </div>
            
            <?php do_action( 'bootscore_before_loop', 'taxonomy-cat-formazione' ); ?>

            <?php if (have_posts()) : ?>
              
              <!-- Grid container for cat-formazione taxonomy posts -->
              <div class="grid-3 grid-m-1 gap-4">
                
                <?php while (have_posts()) : the_post(); ?>
              
                <?php do_action('bootscore_before_loop_item', 'taxonomy-cat-formazione'); ?>

                  <article id="post-<?php the_ID(); ?>" <?php post_class( apply_filters('bootscore/class/loop/card', 'card formazione-card', 'taxonomy-cat-formazione') ); ?>>
                    
                    <?php if (has_post_thumbnail()) : ?>
                      <div class="card-img-wrapper">
                        <a href="<?php the_permalink(); ?>">
                          <?php the_post_thumbnail('medium', ['class' => 'card-img-top', 'alt' => get_the_title()]); ?>
                        </a>
                      </div>
                    <?php endif; ?>
                    
                    <div class="card-body">
                      
                      <!-- Post type and categories badges -->
                      <div class="mb-2">
                        <span class="badge bg-primary me-1"><?php echo get_post_type_object(get_post_type())->labels->singular_name; ?></span>
                        <?php 
                        $terms = get_the_terms(get_the_ID(), 'cat-formazione');
                        if ($terms && !is_wp_error($terms)) :
                          foreach ($terms as $term) : ?>
                            <span class="badge bg-secondary me-1"><?php echo esc_html($term->name); ?></span>
                          <?php endforeach;
                        endif;
                        ?>
                      </div>
                      
                      <h5 class="card-title">
                        <a href="<?php the_permalink(); ?>" class="text-decoration-none">
                          <?php the_title(); ?>
                        </a>
                      </h5>
                      
                      <?php if (has_excerpt()) : ?>
                        <p class="card-text"><?php the_excerpt(); ?></p>
                      <?php endif; ?>
                      
                      <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted"><?php echo get_the_date(); ?></small>
                        <a href="<?php the_permalink(); ?>" class="btn btn-outline-primary btn-sm">
                          <?php _e('Leggi di più', 'bootscore'); ?>
                        </a>
                      </div>
                      
                    </div>
                    
                  </article>

                <?php do_action('bootscore_after_loop_item', 'taxonomy-cat-formazione'); ?>

                <?php endwhile; ?>
                
              </div>
              
              <?php do_action( 'bootscore_after_loop', 'taxonomy-cat-formazione' ); ?>

              <?php bootscore_pagination(); ?>

            <?php else : ?>

              <?php get_template_part('template-parts/content', 'none'); ?>

            <?php endif; ?>

          </main>

        </div>
      </div>
      
      <?php do_action('bootscore_before_primary_close', 'taxonomy-cat-formazione'); ?>

    </div>
  </div>

<?php
get_footer();
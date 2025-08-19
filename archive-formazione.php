<?php

/**
 * The template for displaying formazione post type archives
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

  <div id="content" class="site-content <?= apply_filters('bootscore/class/container', 'container', 'archive-formazione'); ?> <?= apply_filters('bootscore/class/content/spacer', 'pt-4 pb-5', 'archive-formazione'); ?>">
    <div id="primary" class="content-area">
      
      <?php do_action('bootscore_after_primary_open', 'archive-formazione'); ?>

      <div class="row">
        <div class="col-12">

          <main id="main" class="site-main">

            <div class="entry-header mb-4">
              <?php do_action( 'bootscore_before_title', 'archive-formazione' ); ?>
              <?php the_archive_title('<h1 class="entry-title text-center ' . apply_filters('bootscore/class/entry/title', '', 'archive-formazione') . '">', '</h1>'); ?>
              <?php do_action( 'bootscore_after_title', 'archive-formazione' ); ?>
              <?php the_archive_description( '<div class="archive-description text-center ' . apply_filters('bootscore/class/entry/archive-description', '') . '">', '</div>' ); ?>
            </div>
            
            <?php do_action( 'bootscore_before_loop', 'archive-formazione' ); ?>

            <?php if (have_posts()) : ?>
              
              <!-- Grid container for formazione posts -->
              <div class="grid-3 grid-m-1 gap-4">
                
                <?php while (have_posts()) : the_post(); ?>
              
                <?php do_action('bootscore_before_loop_item', 'archive-formazione'); ?>

                  <article id="post-<?php the_ID(); ?>" <?php post_class( apply_filters('bootscore/class/loop/card', 'card formazione-card', 'archive-formazione') ); ?>>
                    
                    <?php if (has_post_thumbnail()) : ?>
                      <div class="card-img-wrapper">
                        <a href="<?php the_permalink(); ?>">
                          <?php the_post_thumbnail('medium', array('class' => apply_filters('bootscore/class/loop/card/image', 'card-img-top', 'archive-formazione'))); ?>
                        </a>
                      </div>
                    <?php endif; ?>

                    <div class="<?= apply_filters('bootscore/class/loop/card/body', 'card-body', 'archive-formazione'); ?>">

                      <!-- Post Type and Category Info -->
                      <div class="post-meta-info mb-2">
                        <span class="badge bg-primary me-2">Formazione</span>
                        <?php 
                        // Get formazione categories
                        $terms = get_the_terms(get_the_ID(), 'cat-formazione');
                        if ($terms && !is_wp_error($terms)) :
                          foreach ($terms as $term) : ?>
                            <span class="badge bg-secondary me-1"><?= esc_html($term->name); ?></span>
                          <?php endforeach;
                        endif;
                        ?>
                      </div>

                      <?php do_action('bootscore_before_loop_title', 'archive-formazione'); ?>
                      
                      <a class="text-body text-decoration-none" href="<?php the_permalink(); ?>">
                        <?php the_title('<h3 class="' . apply_filters('bootscore/class/loop/card/title', 'card-title h5', 'archive-formazione') . '">', '</h3>'); ?>
                      </a>

                      <?php if (apply_filters('bootscore/loop/meta', true, 'archive-formazione')) : ?>
                        <?php if ('formazione' === get_post_type()) : ?>
                          <p class="meta small mb-2 text-body-secondary">
                            <?php
                            bootscore_date();
                            bootscore_author();
                            bootscore_comments();
                            bootscore_edit();
                            ?>
                          </p>
                        <?php endif; ?>
                      <?php endif; ?>

                      <?php if (apply_filters('bootscore/loop/excerpt', true, 'archive-formazione')) : ?>
                        <p class="<?= apply_filters('bootscore/class/loop/card-text/excerpt', 'card-text', 'archive-formazione'); ?>">
                          <a class="text-body text-decoration-none" href="<?php the_permalink(); ?>">
                            <?= strip_tags(get_the_excerpt()); ?>
                          </a>
                        </p>
                      <?php endif; ?>

                      <?php if (apply_filters('bootscore/loop/read-more', true, 'archive-formazione')) : ?>
                        <div class="card-footer bg-transparent border-0 p-0">
                          <a class="<?= apply_filters('bootscore/class/loop/read-more', 'btn btn-outline-primary btn-sm', 'archive-formazione'); ?>" href="<?php the_permalink(); ?>">
                            <?= apply_filters('bootscore/loop/read-more/text', __('Scopri di più »', 'bootscore'), 'archive-formazione'); ?>
                          </a>
                        </div>
                      <?php endif; ?>

                    </div>
                    
                  </article>
              
                  <?php do_action('bootscore_after_loop_item', 'archive-formazione'); ?>

                <?php endwhile; ?>
                
              </div>
              <!-- End grid container -->
              
            <?php endif; ?>
            
            <?php do_action('bootscore_after_loop', 'archive-formazione'); ?>

            <div class="entry-footer mt-5">
              <?php bootscore_pagination(); ?>
            </div>

          </main>

        </div>
      </div>

    </div>
  </div>

<?php
get_footer();
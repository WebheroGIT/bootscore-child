<?php

/**
 * The template for displaying archive pages
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

  <div id="content" class="site-content <?= apply_filters('bootscore/class/container', 'container', 'archive'); ?> <?= apply_filters('bootscore/class/content/spacer', 'pt-4 pb-5', 'archive'); ?>">
    <div id="primary" class="content-area">
      
      <?php do_action('bootscore_after_primary_open', 'archive'); ?>

      <div class="row">
        <div class="<?= apply_filters('bootscore/class/main/col', 'col') ?>">

          <main id="main" class="site-main">

            <?php the_breadcrumb(); ?>

            <div class="entry-header">
              <?php do_action( 'bootscore_before_title', 'archive' ); ?>
              <?php the_archive_title('<h1 class="entry-title ' . apply_filters('bootscore/class/entry/title', '', 'archive') . '">', '</h1>'); ?>
              <?php do_action( 'bootscore_after_title', 'archive' ); ?>
              <?php the_archive_description( '<div class="archive-description ' . apply_filters('bootscore/class/entry/archive-description', '') . '">', '</div>' ); ?>
            </div>
            
            <?php do_action( 'bootscore_before_loop', 'archive' ); ?>

            <?php if (have_posts()) : ?>
              
              <?php 
              // Check if this is formazione post type or cat-formazione taxonomy
              $is_formazione = (is_post_type_archive('formazione') || is_tax('cat-formazione'));
              ?>
              
              <?php if ($is_formazione) : ?>
                <?php get_template_part('template-parts/archivi/formazione-grid'); ?>
              <?php else : ?>
                <?php while (have_posts()) : the_post(); ?>
            
              <?php do_action('bootscore_before_loop_item', 'archive'); ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class( apply_filters('bootscore/class/loop/card', 'card horizontal mb-4', 'archive') ); ?>>
                  
                  <div class="<?= apply_filters('bootscore/class/loop/card/row', 'row g-0', 'archive'); ?>">

                    <?php if (has_post_thumbnail()) : ?>
                      <div class="<?= apply_filters('bootscore/class/loop/card/image/col', 'col-lg-6 col-xl-5 col-xxl-4', 'archive'); ?>">
                        <a href="<?php the_permalink(); ?>">
                          <?php the_post_thumbnail('medium', array('class' => apply_filters('bootscore/class/loop/card/image', 'card-img-lg-start', 'archive'))); ?>
                        </a>
                      </div>
                    <?php endif; ?>

                    <div class="<?= apply_filters('bootscore/class/loop/card/content/col', 'col', 'archive'); ?>">
                      <div class="<?= apply_filters('bootscore/class/loop/card/body', 'card-body', 'archive'); ?>">

                        <?php if (apply_filters('bootscore/loop/category', true, 'archive')) : ?>
                          <?php bootscore_category_badge(); ?>
                        <?php endif; ?>

                        <?php do_action('bootscore_before_loop_title', 'archive'); ?>
                        
                        <a class="text-body text-decoration-none" href="<?php the_permalink(); ?>">
                          <?php the_title('<h2 class="' . apply_filters('bootscore/class/loop/card/title', 'blog-post-title h5', 'archive') . '">', '</h2>'); ?>
                        </a>

                        <?php if (apply_filters('bootscore/loop/meta', true, 'archive')) : ?>
                          <?php if ('post' === get_post_type()) : ?>
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

                        <?php if (apply_filters('bootscore/loop/excerpt', true, 'archive')) : ?>
                          <p class="<?= apply_filters('bootscore/class/loop/card-text/excerpt', 'card-text', 'archive'); ?>">
                            <a class="text-body text-decoration-none" href="<?php the_permalink(); ?>">
                              <?= strip_tags(get_the_excerpt()); ?>
                            </a>
                          </p>
                        <?php endif; ?>

                        <?php if (apply_filters('bootscore/loop/read-more', true, 'archive')) : ?>
                          <p class="<?= apply_filters('bootscore/class/loop/card-text/read-more', 'card-text', 'archive'); ?>">
                            <a class="<?= apply_filters('bootscore/class/loop/read-more', 'read-more', 'archive'); ?>" href="<?php the_permalink(); ?>">
                              <?= apply_filters('bootscore/loop/read-more/text', __('Read more »', 'bootscore', 'archive')); ?>
                            </a>
                          </p>
                        <?php endif; ?>

                        <?php if (apply_filters('bootscore/loop/tags', true, 'archive')) : ?>
                          <?php bootscore_tags(); ?>
                        <?php endif; ?>

                      </div>
                      
                      <?php do_action('bootscore_loop_item_after_card_body', 'archive'); ?>
                      
                    </div>
                    
                  </div>
                  
                </article>
            
                <?php do_action('bootscore_after_loop_item', 'archive'); ?>

                <?php endwhile; ?>
              <?php endif; // end else ?>
            <?php endif; // end have_posts ?>
            
            <?php do_action('bootscore_after_loop', 'archive'); ?>

            <div class="entry-footer">
              <?php 
              // Verifica se siamo nell'archivio formazione o nelle sue taxonomy
              $is_formazione_context = is_post_type_archive('formazione') || is_tax('cat-formazione') || is_tax('area-formazione') || is_tax('modalita-formazione');
              $has_filter_param = false;
              
              // Controlla se ci sono parametri che iniziano con _filter
              foreach ($_GET as $key => $value) {
                  if (strpos($key, '_filter') === 0) {
                      $has_filter_param = true;
                      break;
                  }
              }
              
              if ($is_formazione_context) :
                // Mostra sempre la paginazione normale per formazione
                bootscore_pagination();
                
                // Aggiungi sempre il facet ID 5 subito dopo la paginazione
                if (function_exists('wpgb_render_facet')) : ?>
                <div class="formazione-pagination-facet mt-3">
                  <?php wpgb_render_facet(['id' => 5, 'grid' => 'wpgb-content']); ?>
                </div>
                <?php endif;
              else :
                // Mostra la paginazione normale per altri post types
                bootscore_pagination();
              endif; ?>
            </div>

          </main>

        </div>
        <?php get_sidebar(); ?>
      </div>

    </div>
  </div>


  <style>
    :root {
    --bs-pagination-padding-x: 0.75rem;
    --bs-pagination-padding-y: 0.375rem;
    --bs-pagination-font-size: 1rem;
    --bs-pagination-color: var(--bs-link-color);
    --bs-pagination-bg: var(--bs-body-bg);
    --bs-pagination-border-width: var(--bs-border-width);
    --bs-pagination-border-color: var(--bs-border-color);
    --bs-pagination-border-radius: var(--bs-border-radius);
    --bs-pagination-hover-color: var(--bs-link-hover-color);
    --bs-pagination-hover-bg: var(--bs-tertiary-bg);
    --bs-pagination-hover-border-color: var(--bs-border-color);
    --bs-pagination-focus-color: var(--bs-link-hover-color);
    --bs-pagination-focus-bg: var(--bs-secondary-bg);
    --bs-pagination-focus-box-shadow: 0 0 0 0.25rem rgba(55, 153, 117, 0.25);
    --bs-pagination-active-color: #fff;
    --bs-pagination-active-bg: #379975;
    --bs-pagination-active-border-color: #379975;
    --bs-pagination-disabled-color: var(--bs-secondary-color);
    --bs-pagination-disabled-bg: var(--bs-secondary-bg);
    --bs-pagination-disabled-border-color: var(--bs-border-color);
    }
    
  </style>

<?php
get_footer();

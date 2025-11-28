<?php
/**
 * Template Name: Unimarconi Hero Full
 * Template Post Type: page, post, ateneo, dipartimento, formazione, piano, tirocinio, eventi, progetto-ricerca, avviso, dottorato, territorio-societa, internazionale, ricerca, iscriviti, servizio, press, rassegna-stampa, piani-studio, offerta-formativa, dirigenza, territorio
 *
 * @package Bootscore Child
 * @version 1.0.0
 */

// Exit if accessed directly
defined('ABSPATH') || exit;

get_header();
?>

  <div id="content" class="site-content">
    <div id="primary" class="content-area">
      
      <?php do_action( 'bootscore_after_primary_open', 'unimarconi-hero-full' ); ?>

      <main id="main" class="site-main">

        <?php the_post(); ?>

        <?php
        // Determina il post type corrente
        $post_type = get_post_type();

        // Prova a caricare un contenuto personalizzato per il post type
        if ( locate_template("template-parts/content/{$post_type}/unimarconi-hero-full.php") ) {
          get_template_part("template-parts/content/{$post_type}/unimarconi-hero-full");
        } else {
        ?>

        <div class="<?= apply_filters('bootscore/class/container', 'container', 'unimarconi-hero-full'); ?> <?= apply_filters('bootscore/class/content/spacer', 'pt-3 pb-5', 'unimarconi-hero-full'); ?>">
          
          <?php the_breadcrumb(); ?>

          <!-- Header dell'evento -->
          <div class="rounded-3 overflow-hidden bg-primary text-white mb-5">
              <div class="grid-7-5 grid-xl-1 row-iscrizioni-formazione align-items-stretch position-relative">
                  <div class="p-4 flex-grow-1 align-items-center d-flex">
                      <h1 class="mb-2 mt-2"><?php the_title(); ?></h1>
                      
                     
                  </div>
                  
                  <!-- Featured Image -->
                  <div class="col-featured position-relative h-100 overflow-hidden">
                      <?php if (has_post_thumbnail()): ?>
                          <?php the_post_thumbnail('large', array('class' => 'featured img-fluid h-100 w-100 img-formazione wp-post-image')); ?>
                      <?php endif; ?>
                  </div>
              </div>
          </div>

          <div class="row">
            <div class="<?= apply_filters('bootscore/class/main/col', 'col'); ?>">

              <div class="entry-content">
                <?php bootscore_category_badge(); ?>
                
                <?php the_content(); ?>
              </div>
              
              <?php do_action( 'bootscore_before_entry_footer', 'unimarconi-hero-full' ); ?>

              <div class="entry-footer clear-both">
                <div class="mb-4">
                  <?php bootscore_tags(); ?>
                </div>
                <?php // if (function_exists('bootscore_related_posts')) bootscore_related_posts(); ?>
                <nav aria-label="bs page navigation">
                  <ul class="pagination justify-content-center">
                    <li class="page-item">
                      <?php previous_post_link('%link'); ?>
                    </li>
                    <li class="page-item">
                      <?php next_post_link('%link'); ?>
                    </li>
                  </ul>
                </nav>
                <?php comments_template(); ?>
              </div>

            </div>
          </div>

        </div>

        <?php } ?>

      </main>

    </div>
  </div>

  <style>
    main#main > .container:first-of-type .col-xl-9:first-of-type {
      width: 100%;
    }
  </style>

<?php
get_footer();
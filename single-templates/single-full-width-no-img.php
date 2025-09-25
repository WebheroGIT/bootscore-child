<?php
/**
 * Template Name: Full width no image
 * Template Post Type: post, ateneo, dipartimento, formazione, piano, tirocinio, eventi, progetto-ricerca, avviso, dottorato, territorio-societa, internazionale, ricerca, iscriviti, servizio, press-room, rassegna-stampa, piani-studio, offerta-formativa
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
      
      <?php do_action( 'bootscore_after_primary_open', 'single-full-width-no-img' ); ?>

      <main id="main" class="site-main">

        <?php the_post(); ?>

        <?php
        // Determina il post type corrente
        $post_type = get_post_type();

        // Prova a caricare un contenuto personalizzato per il post type
        if ( locate_template("template-parts/content/{$post_type}/single-full-width-no-img.php") ) {
          get_template_part("template-parts/content/{$post_type}/single-full-width-no-img");
        } else {
        ?>

        <div class="<?= apply_filters('bootscore/class/container', 'container', 'single-full-width-no-img'); ?> <?= apply_filters('bootscore/class/content/spacer', 'pt-3 pb-5', 'single-full-width-no-img'); ?>">
          
          <?php the_breadcrumb(); ?>

          <div class="row">
            <div class="<?= apply_filters('bootscore/class/main/col', 'col'); ?>">

              <div class="entry-header mb-4">
                <?php do_action( 'bootscore_before_title', 'single-full-width-no-img' ); ?>
                <?php the_title('<h1 class="entry-title ' . apply_filters('bootscore/class/entry/title', '', 'single-full-width-no-img') . '">', '</h1>'); ?>
                <?php do_action( 'bootscore_after_title', 'single-full-width-no-img' ); ?>
              </div>

              <div class="entry-content">
                <?php bootscore_category_badge(); ?>
                <p class="entry-meta">
                  <small class="text-body-secondary">
                    <?php
                    bootscore_date();
                    bootscore_author();
                    bootscore_comment_count();
                    ?>
                  </small>
                </p>
                <?php the_content(); ?>
              </div>
              
              <?php do_action( 'bootscore_before_entry_footer', 'single-full-width-no-img' ); ?>

              <div class="entry-footer clear-both">
                <div class="mb-4">
                  <?php bootscore_tags(); ?>
                </div>
                <?php if (function_exists('bootscore_related_posts')) bootscore_related_posts(); ?>
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
            <?php get_sidebar(); ?>
          </div>

        </div>

        <?php } ?>

      </main>

    </div>
  </div>

<?php
get_footer();
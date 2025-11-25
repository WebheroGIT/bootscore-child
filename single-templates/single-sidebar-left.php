<?php
/**
 * Template Name: Left Sidebar
 * Template Post Type: page, post, ateneo, dipartimento, formazione, piano, tirocinio, eventi, progetto-ricerca, avviso, dottorato, territorio-societa, internazionale, ricerca, iscriviti, servizio, press, rassegna-stampa, piani-studio, offerta-formativa, dirigenza, territorio
 *
 * @package Bootscore Child
 * @version 1.0.0
 */

// Exit if accessed directly
defined('ABSPATH') || exit;

get_header();
?>

  <div id="content" class="site-content <?= apply_filters('bootscore/class/container', 'container', 'single-sidebar-left'); ?> <?= apply_filters('bootscore/class/content/spacer', 'pt-3 pb-5', 'single-sidebar-left'); ?>">
    <div id="primary" class="content-area">
      
      <?php do_action( 'bootscore_after_primary_open', 'single-sidebar-left' ); ?>

      <?php the_breadcrumb(); ?>

      <div class="row">
        <?php custom_sidebar_rules(); ?>
        <div class="<?= apply_filters('bootscore/class/main/col', 'col'); ?>">

          <main id="main" class="site-main">

            <?php the_post(); ?>

            <?php
            // Determina il post type corrente
            $post_type = get_post_type();

            // Prova a caricare un contenuto personalizzato per il post type
            if ( locate_template("template-parts/content/{$post_type}/single-sidebar-left.php") ) {
              get_template_part("template-parts/content/{$post_type}/single-sidebar-left");
            } else {
            ?>

            <div class="entry-header">
              <?php bootscore_category_badge(); ?>
              <?php do_action( 'bootscore_before_title', 'single-sidebar-left' ); ?>
              <?php the_title('<h1 class="entry-title ' . apply_filters('bootscore/class/entry/title', '', 'single-sidebar-left') . '">', '</h1>'); ?>
              <?php do_action( 'bootscore_after_title', 'single-sidebar-left' ); ?>
             
              <?php bootscore_post_thumbnail(); ?>
            </div>
            
            <?php do_action( 'bootscore_after_featured_image', 'single-sidebar-left' ); ?>

            <div class="entry-content">
              <?php the_content(); ?>
            </div>
            
            <?php do_action( 'bootscore_before_entry_footer', 'single-sidebar-left' ); ?>

            <div class="entry-footer clear-both">
              <div class="mb-4">
                <?php bootscore_tags(); ?>
              </div>
              <?php // if (function_exists('bootscore_related_posts')) bootscore_related_posts(); ?>
            <!-- <nav aria-label="bs page navigation">
              <ul class="pagination justify-content-center">
                <li class="page-item"><?php // previous_post_link('%link'); ?></li>
                <li class="page-item"><?php // next_post_link('%link'); ?></li>
              </ul>
            </nav> -->
        
              <?php comments_template(); ?>
            </div>

            <?php } ?>

          </main>

        </div>
      </div>

    </div>
  </div>

<?php
get_footer();
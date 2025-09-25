<?php
/**
 * Template for single "ateneo" posts.
 *
 * @package Bootscore Child
 */

// Exit if accessed directly
defined('ABSPATH') || exit;

get_header(); ?>

<!-- Breadcrumbs in container -->
<div class="container mt-4">
    <div class="row">
        <?php the_breadcrumb(); ?>
    </div>
  
</div>

<!-- Full-width content section -->
<div id="content" class="site-content">

  <div id="primary" class="content-area">
    <?php do_action('bootscore_after_primary_open', 'single'); ?>


        <main id="main" class="site-main">

          <?php the_post(); ?>

          <?php
          // Determina il post type corrente
          $post_type = get_post_type();

          // Carica contenuto personalizzato per il post type
          if ( locate_template("template-parts/content/{$post_type}/single.php") ) {
            get_template_part("template-parts/content/{$post_type}/single");
          } else {
          ?>

          <div class="entry-header">
            <?php bootscore_category_badge(); ?>
            <?php do_action('bootscore_before_title', 'single'); ?>
            <?php the_title('<h1 class="entry-title ' . apply_filters('bootscore/class/entry/title', '', 'single') . '">', '</h1>'); ?>
            <?php do_action('bootscore_after_title', 'single'); ?>
            <?php bootscore_post_thumbnail(); ?>
          </div>

          <?php do_action('bootscore_after_featured_image', 'single'); ?>

          <div class="entry-content">
            <?php
              // Fallback minimale se il template personalizzato non esiste
              the_content();
            ?>
          </div>

          <?php } ?>

          <?php do_action('bootscore_before_entry_footer', 'single'); ?>

          <div class="entry-footer clear-both">
            <div class="mb-4">
              <?php bootscore_tags(); ?>
            </div>
            <?php 
            // Post correlati e navigazione disabilitati per il post type 'ateneo'
            // <?php if (function_exists('bootscore_related_posts')) bootscore_related_posts(); ?>
            <!-- <nav aria-label="bs page navigation">
            //   <ul class="pagination justify-content-center">
            //     <li class="page-item"><?php previous_post_link('%link'); ?></li>
            //     <li class="page-item"><?php next_post_link('%link'); ?></li>
            //   </ul>
            // </nav>
            // ?>  -->
            <?php comments_template(); ?>
          </div>

        </main>

      </div>
    </div>


<?php get_footer(); ?>

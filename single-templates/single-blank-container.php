<?php
/**
 * Template Name: Blank with container
 * Template Post Type: post, ateneo, dipartimento, formazione, piano, tirocinio, eventi, progetto-ricerca, avviso, dottorato, territorio-societa, internazionale, ricerca, iscriviti, servizio, press, rassegna-stampa, piani-studio, offerta-formativa, dirigenza
 *
 * @package Bootscore Child
 * @version 1.0.0
 */

// Exit if accessed directly
defined('ABSPATH') || exit;

get_header();
?>

  <div id="content" class="site-content <?= apply_filters('bootscore/class/container', 'container', 'single-blank-container'); ?> <?= apply_filters('bootscore/class/content/spacer', 'pt-3 pb-5', 'single-blank-container'); ?>">
    <div id="primary" class="content-area">

      <main id="main" class="site-main">

        <?php the_post(); ?>

        <?php
        // Determina il post type corrente
        $post_type = get_post_type();

        // Prova a caricare un contenuto personalizzato per il post type
        if ( locate_template("template-parts/content/{$post_type}/single-blank-container.php") ) {
          get_template_part("template-parts/content/{$post_type}/single-blank-container");
        } else {
        ?>

        <div class="entry-content">
          <?php the_content(); ?>
        </div>

        <?php } ?>

      </main>

    </div>
  </div>

<?php
get_footer();
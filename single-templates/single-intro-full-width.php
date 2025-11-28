<?php
/**
 * Template Name: Intro Full Width
 * Template Post Type: page, post, ateneo, dipartimento, formazione, piano, tirocinio, eventi, progetto-ricerca, avviso, dottorato, territorio-societa, internazionale, ricerca, iscriviti, servizio, press, rassegna-stampa, piani-studio, offerta-formativa, dirigenza, territorio
 *
 * Full-width intro section with left content and right edge-to-edge featured image.
 * Below the intro, render the normal content in a standard container.
 *
 * @package Bootscore Child
 * @version 1.0.0
 */

// Exit if accessed directly
defined('ABSPATH') || exit;

get_header();
?>

<?php the_post(); ?>

<div id="content" class="site-content">
  <!-- Full-width intro section -->
  <section class="bg-background">
    <!-- Use standard container so text aligns with site gutters -->
    <div class="<?= apply_filters('bootscore/class/container', 'container'); ?>">
      <div class="row g-0 align-items-stretch">
        <!-- Left column: title, excerpt, button -->
        <div class="col-12 col-xl-7 d-flex">
          <!-- Add responsive horizontal padding so text aligns with site gutters -->
          <div class="w-100 <?= apply_filters('bootscore/class/content/spacer', 'py-5'); ?>">
            <div class="row">
              <div class="col-12">
                <header class="entry-header mb-3">
                  <h1 class="entry-title h2 m-0"><?php echo esc_html(get_the_title()); ?></h1>
                </header>

                <?php if (has_excerpt()) : ?>
                  <div class="entry-summary mb-4 me-0 me-lg-4"><?php echo wp_kses_post(get_the_excerpt()); ?></div>
                <?php endif; ?>

                <?php
                // Button text and URL from Meta Box if available
                $button_text = function_exists('rwmb_meta') ? rwmb_meta('intro_button_text', '', get_the_ID()) : '';
                $button_url  = function_exists('rwmb_meta') ? rwmb_meta('intro_button_url', '', get_the_ID()) : '';

                if (empty($button_text)) {
                  $button_text = __('Scopri di più', 'bootscore');
                }
                if (empty($button_url)) {
                  $button_url = get_permalink();
                }
                ?>

                <div class="mt-2">
                  <a class="btn btn-secondary" href="<?php echo esc_url($button_url); ?>"><?php echo esc_html($button_text); ?></a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right column: featured image full-bleed -->
        <div class="col-12 col-xl-5">
          <?php if (has_post_thumbnail()) : ?>
            <!-- On lg+, bleed the image to the right viewport edge; on smaller screens, keep normal -->
            <div class="d-none d-xl-block ms-auto" style="width:auto; margin-right: calc(50% - 50vw); min-height: 260px; height: 100%;">
              <?php
              $thumb_id  = get_post_thumbnail_id();
              $thumb_src = wp_get_attachment_image_src($thumb_id, 'large');
              if ($thumb_src) :
              ?>
                <div class="w-100" style="height:100%; min-height: 260px; background-image:url('<?php echo esc_url($thumb_src[0]); ?>'); background-size:cover; background-position:center; background-repeat:no-repeat;"></div>
              <?php else : ?>
                <?php the_post_thumbnail('large', ['class' => 'w-100', 'style' => 'height:100%; min-height:260px; object-fit:cover; object-position:center; display:block;']); ?>
              <?php endif; ?>
            </div>
            <div class="d-xl-none">
              <?php the_post_thumbnail('large', ['class' => 'w-100 h-100', 'style' => 'object-fit:cover; object-position:center; display:block;']); ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- Spacing between intro and main content -->
  <div class="py-4"></div>

  <!-- Normal content area -->
  <div class="<?= apply_filters('bootscore/class/container', 'container', 'single-intro-full-width'); ?> <?= apply_filters('bootscore/class/content/spacer', 'pb-5', 'single-intro-full-width'); ?>">
    <div id="primary" class="content-area">
      <main id="main" class="site-main">
        <div class="entry-content">
          <?php the_content(); ?>
        </div>
      </main>
    </div>
  </div>
</div>

<?php
get_footer();



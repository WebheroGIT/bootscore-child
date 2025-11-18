<?php
/**
 * Template part for displaying progetto posts in grid layout
 * Used by archive.php for post type 'progetto'
 *
 * @package Bootscore Child
 */

// Exit if accessed directly
defined('ABSPATH') || exit;
?>

<?php if (function_exists('wpgb_render_facet')) : ?>
<div class="progetto-filters grid-3 gap-5 mb-2">
  <?php wpgb_render_facet(['id' => 7, 'grid' => 'wpgb-content']); ?>
  <?php wpgb_render_facet(['id' => 8, 'grid' => 'wpgb-content']); ?>
  <?php wpgb_render_facet(['id' => 4, 'grid' => 'wpgb-content']); ?>
</div>
<?php endif; ?>

<?php
// Query base per Progetti (simile a formazione, ma senza campi corso_)
$args = array(
    'post_type'       => 'progetto',
    'wp_grid_builder' => 'wpgb-content',
    'posts_per_page'  => get_option('posts_per_page'),
    'paged'           => get_query_var('paged') ? get_query_var('paged') : 1,
    'orderby'         => array(
        'date' => 'DESC',
    ),
);

// Mantieni eventuale filtro taxonomy corrente, se in archivio tassonomia
if (is_tax()) {
    $queried = get_queried_object();
    if ($queried && !empty($queried->taxonomy) && !empty($queried->term_id)) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => $queried->taxonomy,
                'field'    => 'term_id',
                'terms'    => $queried->term_id,
            ),
        );
    }
}

$progetto_query = new WP_Query($args);
?>

<div class="grid-3 grid-lg-2 grid-md-1 gap-4 grid-box-progetto mb-5 wpgb-content">
 
  <?php if ($progetto_query->have_posts()) : ?>
    <?php while ($progetto_query->have_posts()) : $progetto_query->the_post(); ?>

      <?php do_action('bootscore_before_loop_item', 'progetto-grid'); ?>

      <article id="post-<?php the_ID(); ?>" <?php post_class('card progetto-card position-relative'); ?>>

        <?php if (has_post_thumbnail()) : ?>
          <div class="card-img-wrapper">
            <a href="<?php the_permalink(); ?>">
              <?php the_post_thumbnail('large', ['class' => 'card-img-top', 'alt' => get_the_title()]); ?>
            </a>
          </div>
        <?php endif; ?>

        <?php 
        // Badge stato progetto in alto a sinistra (pricerca_stato)
        $pricerca_stato = function_exists('rwmb_meta') ? rwmb_meta('pricerca_stato', '', get_the_ID()) : get_post_meta(get_the_ID(), 'pricerca_stato', true);
        if (!empty($pricerca_stato)) : 
          // Se il termine è "attivo", usa bg-primary e text-white, altrimenti bg-warning e text-dark
          $badge_classes = (strtolower(trim($pricerca_stato)) === 'attivo') ? 'bg-primary text-white' : 'bg-warning text-dark';
        ?>
          <div class="position-absolute top-0 start-0 m-2">
            <span class="badge <?php echo esc_attr($badge_classes); ?> fw-bold"><?php echo esc_html($pricerca_stato); ?></span>
          </div>
        <?php endif; ?>

        <div class="card-body d-flex flex-column">

          <h5 class="card-title">
            <a href="<?php the_permalink(); ?>" class="text-decoration-none text-dark">
              <?php the_title(); ?>
            </a>
          </h5>

          <?php if (has_excerpt()) : ?>
            <p class="card-text flex-grow-1 excerpt-4-lines small"><?php echo wp_strip_all_tags(get_the_excerpt()); ?></p>
          <?php else : ?>
            <div class="flex-grow-1"></div>
          <?php endif; ?>
        </div>

        <div class="card-footer bg-primary text-light">
          <div class="d-flex gap-2 justify-content-between align-items-center">
            <?php
            // Footer: mostra solo il periodo del progetto (pricerca_periodo)
            $pricerca_periodo = function_exists('rwmb_meta') ? rwmb_meta('pricerca_periodo', '', get_the_ID()) : get_post_meta(get_the_ID(), 'pricerca_periodo', true);
            if (!empty($pricerca_periodo)) {
              echo '<p class="m-0 small fw-bold">' . esc_html($pricerca_periodo) . '</p>';
            } else {
              echo '<span class="m-0 small">&nbsp;</span>';
            }
            ?>

            <a href="<?php the_permalink(); ?>" class="m-0 small text-light">
              <?php echo esc_html__('Scopri di più', 'bootscore'); ?>
            </a>
          </div>
        </div>

      </article>

      <?php do_action('bootscore_after_loop_item', 'progetto-grid'); ?>

    <?php endwhile; ?>
  <?php endif; ?>

</div>

<?php wp_reset_postdata(); ?>



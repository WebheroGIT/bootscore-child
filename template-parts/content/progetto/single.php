<?php
/**
 * Template per il singolo progetto
 *
 * @package Bootscore Child
 */

// Exit if accessed directly
defined('ABSPATH') || exit;

// Recupera i custom fields del progetto
$ricerca_location = rwmb_meta('pricerca_location');
$ricerca_periodo = rwmb_meta('pricerca_periodo');
$ricerca_tipologia = rwmb_meta('pricerca_tipologia');
$ricerca_ente = rwmb_meta('pricerca_ente');
$ricerca_gestione = rwmb_meta('pricerca_gestione');
$ricerca_budget = rwmb_meta('pricerca_budget');
$ricerca_referenti = rwmb_meta('pricerca_referenti');
$ricerca_sito_web = rwmb_meta('pricerca_sito_web');
$ricerca_email = rwmb_meta('pricerca_email');
$ricerca_stato = rwmb_meta('pricerca_stato');

?>

<div class="rounded-3 overflow-hidden bg-primary text-white mb-5">
  <div class="grid-7-5 grid-lg-1 row-iscrizioni-formazione align-items-stretch position-relative">
    <div class="">
      <div class="p-4 flex-grow-1">

        <!-- Location se presente -->
        <?php if (!empty($ricerca_location)) : ?>
        <div class="py-2">
            <strong>Ambito</strong>
            <span><?php echo esc_html($ricerca_location); ?></span>
        </div>
        <?php endif; ?>

        <h1 class="mt-1"><?php the_title(); ?></h1>

        <div class="grid-3 text-white text-center mt-4 g-0 border-top border-bottom border-white">
          
          <!-- Periodo -->
          <?php if (!empty($ricerca_periodo)) : ?>
          <div class="col border-end border-white py-2">
              <strong>Periodo</strong><br>
              <span><?php echo esc_html($ricerca_periodo); ?></span>
          </div>
          <?php endif; ?>

          <!-- Budget -->
          <?php if (!empty($ricerca_budget)) : ?>
          <div class="col border-end border-white py-2">
              <strong>Budget</strong><br>
              <span><?php echo esc_html($ricerca_budget); ?></span>
          </div>
          <?php endif; ?>

          <!-- Stato -->
          <?php if (!empty($ricerca_stato)) : ?>
          <div class="col py-2">
              <strong>Stato</strong><br>
              <span><?php echo esc_html($ricerca_stato); ?></span>
          </div>
          <?php endif; ?>

        </div>
      </div>
    </div>

    <!-- Featured image -->
    <div class="col-featured position-relative h-100">
      <div class="img-wrapper position-absolute top-0 start-0 end-0 bottom-0">
        <?php if (has_post_thumbnail()) : ?>
          <?php the_post_thumbnail('large', ['class' => 'img-fluid h-100 w-100 img-formazione']); ?>
        <?php else : ?>
          <?php echo wp_get_attachment_image(22, 'large', false, ['class' => 'img-fluid h-100 w-100 img-formazione']); ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Sezione informazioni progetto -->
<?php if (!empty($ricerca_tipologia) || !empty($ricerca_gestione) || !empty($ricerca_ente)) : ?>
<div class="mb-4">
  <div class="d-flex flex-wrap gap-3 align-items-start">
    <?php if (!empty($ricerca_tipologia)) : ?>
    <div class="project-info-item">
      <h3 class="h6 text-primary mb-1">Tipologia</h3>
      <p class="mb-0"><?php echo esc_html($ricerca_tipologia); ?></p>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($ricerca_gestione)) : ?>
    <div class="project-info-item">
      <h3 class="h6 text-primary mb-1">Struttura Gestione</h3>
      <p class="mb-0"><?php echo esc_html($ricerca_gestione); ?></p>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($ricerca_ente)) : ?>
    <div class="project-info-item">
      <h3 class="h6 text-primary mb-1">Ente Finanziatore</h3>
      <p class="mb-0"><?php echo esc_html($ricerca_ente); ?></p>
    </div>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>

<!-- CONTENUTO STANDARD -->
<div class="mt-4 mb-5">
  <?php the_content(); ?>
</div>

<!-- Sezione referenti e contatti -->
<div class="row">
  <div class="col-md-6">
    <?php if (!empty($ricerca_referenti)) : ?>
    <div class="mb-4">
      <h2 class="h4 text-primary">Referenti</h2>
      <p><?php echo wp_kses_post($ricerca_referenti); ?></p>
    </div>
    <?php endif; ?>
  </div>
  <div class="col-md-6">
    <div class="mb-4">
      <h2 class="h4 text-primary">Contatti</h2>
      <?php if (!empty($ricerca_sito_web)) : ?>
      <p><strong>Sito web:</strong> <a href="<?php echo esc_url($ricerca_sito_web); ?>" target="_blank" rel="noopener"><?php echo esc_html($ricerca_sito_web); ?></a></p>
      <?php endif; ?>
      
      <?php if (!empty($ricerca_email)) : ?>
      <p><strong>Email:</strong> <a href="mailto:<?php echo esc_attr($ricerca_email); ?>"><?php echo esc_html($ricerca_email); ?></a></p>
      <?php endif; ?>
    </div>
  </div>
</div>
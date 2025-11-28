<?php
/**
 * Template per il singolo evento
 *
 * @package Bootscore Child
 */

// Exit if accessed directly
defined('ABSPATH') || exit;

// Otteniamo i metadati per l'evento
$evento_data_inizio = rwmb_meta('evento_data_inizio');
$evento_data_fine = rwmb_meta('evento_data_fine');
$evento_descrizione = rwmb_meta('evento_descrizione');
$evento_luogo = rwmb_meta('evento_luogo');
$evento_location = rwmb_meta('evento_location');
$evento_organizzatore = rwmb_meta('evento_organizzatore');

// Funzione helper per tradurre i mesi in italiano (se non già definita)
if (!function_exists('format_date_italian')) {
    function translate_month_to_italian($english_month) {
        $months = array(
            'Jan' => 'Gen',
            'Feb' => 'Feb', 
            'Mar' => 'Mar',
            'Apr' => 'Apr',
            'May' => 'Mag',
            'Jun' => 'Giu',
            'Jul' => 'Lug',
            'Aug' => 'Ago',
            'Sep' => 'Set',
            'Oct' => 'Ott',
            'Nov' => 'Nov',
            'Dec' => 'Dic'
        );
        return isset($months[$english_month]) ? $months[$english_month] : $english_month;
    }
    
    function format_date_italian($timestamp, $format) {
        $date_parts = explode(' ', date($format, $timestamp));
        foreach ($date_parts as &$part) {
            if (strlen($part) == 3 && ctype_alpha($part)) {
                $part = translate_month_to_italian($part);
            }
        }
        return implode(' ', $date_parts);
    }
}

// Convertiamo le date in timestamp
$start_timestamp = strtotime($evento_data_inizio);
$end_timestamp = strtotime($evento_data_fine);
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    
    <!-- Header dell'evento -->
    <div class="rounded-3 overflow-hidden bg-primary text-white mb-5">
        <div class="grid-7-5 grid-xl-1 row-iscrizioni-formazione align-items-stretch position-relative">
            <div class="p-4 flex-grow-1 align-items-center d-flex">
                <h1 class="mb-2 mt-2"><?php the_title(); ?></h1>
                
                <?php if ($evento_organizzatore): ?>
                <div class="d-flex align-items-center">
                    <i class="fas fa-user me-2"></i>
                    <span><?php echo esc_html($evento_organizzatore); ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Featured Image -->
            <div class="col-featured position-relative h-100 overflow-hidden">
                <?php if (has_post_thumbnail()): ?>
                    <?php the_post_thumbnail('large', array('class' => 'featured img-fluid h-100 w-100 img-formazione wp-post-image')); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Informazioni evento -->
    <div class="mb-4">
        <?php if ($start_timestamp && $end_timestamp): ?>
        <div class="d-flex align-items-center mb-3">
            <i class="fas fa-calendar-alt me-2 text-primary"></i>
            <span class="fs-5">
                <?php 
                if (date('Y-m-d', $start_timestamp) === date('Y-m-d', $end_timestamp)) {
                    // Stesso giorno
                    echo format_date_italian($start_timestamp, 'j M Y');
                } else {
                    // Giorni diversi
                    echo format_date_italian($start_timestamp, 'j M Y') . ' - ' . format_date_italian($end_timestamp, 'j M Y');
                }
                ?>
            </span>
        </div>
        <?php endif; ?>
        
        <?php if ($start_timestamp && $end_timestamp): ?>
        <div class="d-flex align-items-center mb-3">
            <i class="fas fa-clock me-2 text-primary"></i>
            <span>
                <?php 
                $start_time = date('H:i', $start_timestamp);
                $end_time = date('H:i', $end_timestamp);
                echo $start_time . ' - ' . $end_time;
                ?>
            </span>
        </div>
        <?php endif; ?>

        <?php if ($evento_location): ?>
        <div class="d-flex align-items-center mb-3">
            <i class="fas fa-map-marker-alt me-2 text-primary"></i>
            <span><?php echo esc_html($evento_location); ?></span>
        </div>
        <?php endif; ?>

    </div>
    
    <!-- Contenuto dell'evento -->
    <div class="entry-content">
        <?php if ($evento_descrizione): ?>
            <div class="mb-4">
                <h3>Descrizione</h3>
                <div class="content-text">
                    <?php echo wpautop($evento_descrizione); ?>
                </div>
            </div>
        <?php endif; ?>
        
        <?php 
        // Mostra il contenuto principale del post se presente
        the_content();
        ?>
    </div>
    
    <!-- Navigation tra eventi -->
    <div class="row mt-5">
        <div class="col-6">
            <?php 
            $prev_post = get_previous_post(false, '', 'evento_data_inizio');
            if ($prev_post): 
            ?>
                <a href="<?php echo get_permalink($prev_post->ID); ?>" class="btn btn-outline-primary">
                    <i class="fas fa-chevron-left me-2"></i>Evento precedente
                </a>
            <?php endif; ?>
        </div>
        <div class="col-6 text-end">
            <?php 
            $next_post = get_next_post(false, '', 'evento_data_inizio');
            if ($next_post): 
            ?>
                <a href="<?php echo get_permalink($next_post->ID); ?>" class="btn btn-outline-primary">
                    Evento successivo<i class="fas fa-chevron-right ms-2"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
    
</article>
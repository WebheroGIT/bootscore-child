<?php
/**
 * Logic per Dipartimenti - Shortcodes e funzioni
 */

// Shortcode per stampare gli avvisi dei dipartimenti
function avvisi_dipartimenti_shortcode($atts) {
    // Parametri di default
    $atts = shortcode_atts(array(
        'taxonomy' => 'cat-dipartimenti', // Tassonomia di default
        'cat' => '', // Categoria specifica
        'posts_per_page' => -1 // Tutti i post
    ), $atts, 'avvisi');

    // Se non è specificata una categoria, non mostrare nulla
    if (empty($atts['cat'])) {
        return '<div class="alert alert-warning">Specificare una categoria per visualizzare gli avvisi.</div>';
    }

    // Argomenti per la query
    $args = array(
        'post_type' => 'avviso',
        'post_status' => 'publish',
        'posts_per_page' => $atts['posts_per_page'],
        'tax_query' => array(
            array(
                'taxonomy' => $atts['taxonomy'],
                'field' => 'slug',
                'terms' => $atts['cat']
            )
        )
    );

    // Esegui la query
    $query = new WP_Query($args);

    // Se non ci sono post, restituisci messaggio
    if (!$query->have_posts()) {
        wp_reset_postdata();
        return '<div class="alert alert-info">Al momento non risultano avvisi attivi per questo Dipartimento.</div>';
    }

    // Inizia l'output
    $output = '<div class="avvisi-dipartimenti grid-3 grid-lg-2 grid-md-1 gap-4">';

    // Loop attraverso i post
    while ($query->have_posts()) {
        $query->the_post();
        
        $output .= '<div class="avviso-item bg-light p-4 rounded d-flex align-items-start">';
        $output .= '<div class="avviso-content flex-grow-1">';
        $output .= '<h5 class="avviso-title mb-2">' . get_the_title() . '</h5>';
        $output .= '<div class="avviso-text">' . get_the_content() . '</div>';
        $output .= '</div>';
        $output .= '<div class="avviso-icon ms-3 d-flex align-items-center">';
        $output .= '<div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">';
        $output .= '<i class="fas fa-chevron-right text-white"></i>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
    }

    $output .= '</div>';

    // Reset post data
    wp_reset_postdata();

    return $output;
}

// Registra lo shortcode SHORTCODE

// [avvisi cat="scienze-economico-aziendali"]
// [avvisi cat="scienze-umane" taxonomy="cat-dipartimenti"]
// [avvisi cat="scienze-umane" taxonomy="cat-dipartimenti" posts_per_page="5"]
//
add_shortcode('avvisi', 'avvisi_dipartimenti_shortcode');

?>
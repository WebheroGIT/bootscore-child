<?php
// Assicurati che il file non sia accessibile direttamente
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


function rassegna_shortcode() {
    // Query per ottenere i post di tipo "rassegna" ordinati per data (dal più recente)
    $args = array(
        'post_type' => 'rassegna',
        'posts_per_page' => -1, // Puoi cambiare il numero se vuoi limitare i post visualizzati
        'orderby' => 'date',
        'order' => 'DESC'
    );

    $query = new WP_Query($args);
    
    // Iniziamo il contenitore
    $output = '<div class="accordion mt-5" id="accordionExample">';

    // Verifica se ci sono post
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            // Otteniamo il valore del campo personalizzato 'rassegna_estata'
            $rassegna_estata = rwmb_meta('rassegna_estata');

            // Data del post
            $post_date = get_the_date('d M Y');

            // Generiamo un ID unico per ogni post per l'accordion
            $accordion_id = 'accordion-' . get_the_ID();
            $collapse_id = 'collapse-' . get_the_ID();

            // Costruzione del layout
            $output .= '<div class="accordion-item mb-4 rounded-4 overflow-hidden">';  // Stacco tra gli accordion

                // Prima parte: intestazione grigia con campo custom e data
                $output .= '<div class="accordion-header bg-grey-medium text-white p-3" id="' . $accordion_id . 'Heading">';
                    $output .= '<div class="d-flex justify-content-between w-100">';
                        if ($rassegna_estata) {
                            $output .= '<strong style="color:#fff;">' . esc_html($rassegna_estata) . '</strong>'; // Visualizza il campo "rassegna_estata" se presente
                        }
                        $output .= '<span>' . esc_html($post_date) . '</span>'; // Visualizza la data
                    $output .= '</div>';
                $output .= '</div>';

                // Seconda parte: titolo del post e freccetta per aprire l'accordion
                $output .= '<div class="accordion-header p-3" style="background-color: white;">';
                    $output .= '<button class="accordion-button collapsed d-flex justify-content-between" type="button" data-bs-toggle="collapse" data-bs-target="#' . $collapse_id . '" aria-expanded="false" aria-controls="' . $collapse_id . '">';
                        $output .= '<span>' . get_the_title() . '</span>';
                        // Freccetta per aprire/chiudere l'accordion
                        $output .= '<i class="bi bi-chevron-down"></i>';
                    $output .= '</button>';
                $output .= '</div>';

                // Corpo dell'accordion: parte nascosta con contenuto del post
                $output .= '<div id="' . $collapse_id . '" class="accordion-collapse collapse" aria-labelledby="' . $accordion_id . 'Heading" data-bs-parent="#accordionExample">';
                    $output .= '<div class="accordion-body" style="background-color: white;">'; // Sfondo bianco per il corpo dell'accordion
                        $output .= '<div class="card-text">' . get_the_content() . '</div>'; // Contenuto del post
                    $output .= '</div>';
                $output .= '</div>';

            $output .= '</div>';
        }
    } else {
        $output .= '<p>No posts found.</p>';
    }

    // Ripristiniamo la query originale
    wp_reset_postdata();

    // Chiudiamo il contenitore dell'accordion
    $output .= '</div>';

    return $output;
}
add_shortcode('rassegna', 'rassegna_shortcode');

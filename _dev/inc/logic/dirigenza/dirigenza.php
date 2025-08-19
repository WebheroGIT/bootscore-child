<?php

// Assicurati che il file non sia accessibile direttamente
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function stampa_dirigenza_shortcode() {
    // Query per ottenere i post del custom post type 'dirigenza' in ordine crescente (dal più vecchio al più nuovo)
    $args = array(
        'post_type' => 'dirigenza',
        'posts_per_page' => -1, // Per ottenere tutti i post
        'orderby' => 'date',
        'order' => 'ASC',
    );

    $query = new WP_Query($args);

    // Verifica se ci sono post
    if ($query->have_posts()) {
        $output = '<div class="row">'; // Avvio della griglia di Bootstrap con 3 colonne

        while ($query->have_posts()) {
            $query->the_post();

            // Ottieni il valore del campo personalizzato
            $ruolo = rwmb_meta('dirigenza_ruolo');
            $foto = get_the_post_thumbnail_url(get_the_ID(), 'medium'); // Ottieni l'immagine in miniatura
            $content = get_the_content(); // Ottieni il contenuto del post
            $link = get_permalink(); // Ottieni il link al post

            // Aggiungi il codice HTML per il box
            $output .= '<div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="row g-0 h-100">
                                    <div class="col-5">
                                        <img src="' . esc_url($foto) . '" class="img-fluid rounded-start h-100" alt="' . get_the_title() . '">
                                    </div>
                                    <div class="col-7 bg-light">
                                        <div class="card-body">
                                            <p class="text-primary fw-bold">' . esc_html($ruolo) . '</p>
                                            <h3 class="card-title">' . get_the_title() . '</h3>
                                            <p class="card-text">' . wp_trim_words($content, 20) . '</p>
                                            <a href="' . esc_url($link) . '" class="text-primary text-decoration-none float-end">Scopri di più ></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>';
        }

        $output .= '</div>'; // Chiusura della griglia
        wp_reset_postdata(); // Ripristina i dati del post
    } else {
        $output = '<p>No post found.</p>';
    }

    return $output;
}
add_shortcode('stampa_dirigenza', 'stampa_dirigenza_shortcode');



/// [stampa_dirigenza]
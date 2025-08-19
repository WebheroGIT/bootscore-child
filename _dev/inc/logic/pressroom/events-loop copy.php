<?php

// Assicurati che il file non sia accessibile direttamente
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function eventi_shortcode() {
    // Impostiamo la query per ottenere gli eventi
    $args = array(
        'post_type' => 'eventi', // Tipo di post 'eventi'
        'posts_per_page' => -1, // Mostra tutti gli eventi
        'orderby' => 'meta_value', // Ordina per data personalizzata
        'order' => 'ASC', // Ordine crescente (dal più recente al più lontano)
        'meta_key' => 'evento_data_inizio', // La chiave del campo personalizzato per la data di inizio
        'meta_type' => 'NUMERIC', // La data è un timestamp numerico
    );

    $query = new WP_Query($args);

    // Iniziamo il contenitore
    $output = '<div class="container mt-5">';

    // Inizializzare le variabili per gli eventi futuri e passati
    $future_events = [];
    $past_events = [];

    // Aggiungi var_dump per i metadati prima del loop
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            // Otteniamo i metadati per l'evento
            $evento_data_inizio = rwmb_meta('evento_data_inizio');
            $evento_data_fine = rwmb_meta('evento_data_fine');
            $current_date = current_time('timestamp'); // Otteniamo la data di oggi


            // Convertiamo la data inizio e fine in timestamp
            $start_timestamp = strtotime($evento_data_inizio);
            if ($start_timestamp === false || $start_timestamp < 0) {
                $start_timestamp = 0; // Imposta un timestamp valido se strtotime restituisce false
            }

            $end_timestamp = strtotime($evento_data_fine);
            if ($end_timestamp === false || $end_timestamp < 0) {
                $end_timestamp = 0; // Imposta un timestamp valido se strtotime restituisce false
            }

            // Separiamo gli eventi in base alla data
            if ($start_timestamp >= $current_date) {
                // Evento futuro
                $future_events[] = [
                    'title' => get_the_title(),
                    'start_date' => $start_timestamp,
                    'end_date' => $end_timestamp,
                    'link' => get_permalink()
                ];
            } else {
                // Evento passato
                $past_events[] = [
                    'title' => get_the_title(),
                    'start_date' => $start_timestamp,
                    'end_date' => $end_timestamp,
                    'link' => get_permalink()
                ];
            }
        }
    }

    // Ordina gli eventi futuri (più recente in alto)
    usort($future_events, function($a, $b) {
        return $a['start_date'] <=> $b['start_date'];
    });

    // Ordina gli eventi passati (più recente in alto)
    usort($past_events, function($a, $b) {
        return $a['start_date'] <=> $b['start_date'];
    });

    // Mostra i prossimi eventi
    if (count($future_events) > 0) {
        $output .= '<h3>Prossimi eventi</h3>';
        $output .= '<div class="row">'; // Inizio della griglia

        foreach ($future_events as $event) {
            $output .= '<div class="col-12 col-md-4 mb-4 eventi-loop">'; // Griglia a 3 colonne
                // Box con evento futuro
                $output .= '<div class="card rounded overflow-hidden">';
                    $output .= '<div class="card-header bg-primary text-white d-flex justify-content-end align-items-center">';
                        $output .= '<span>' . date('Y', $event['start_date']) . '</span>'; // Anno
                        $output .= '<span class="badge bg-white text-dark fs-4 fw-normal">' . date('M j', $event['start_date']) . '</span>'; // Mese e giorno
                    $output .= '</div>';
                    $output .= '<div class="card-body bg-light p-4" style="border-bottom: 1px dotted #333;">';
                        $output .= '<span>' . date('M j, Y', $event['start_date']) . ' - ' . date('M j, Y', $event['end_date']) . '</span>';
                    $output .= '</div>';
                    $output .= '<div class="card-body bg-light p-4">';
                        $output .= '<h5><a href="' . $event['link'] . '" class="text-dark clickable-parent">' . $event['title'] . '</a></h5>';
                        $output .= '<a href="' . $event['link'] . '" class="text-primary text-decoration-underline mt-3">Vedi evento ></a>';
                    $output .= '</div>';
                $output .= '</div>';
            $output .= '</div>';
        }

        $output .= '</div>'; // Fine della riga
    }

    // Mostra gli eventi passati
    if (count($past_events) > 0) {
        $output .= '<h3>Eventi passati</h3>';
        $output .= '<div class="row">'; // Inizio della griglia

        foreach ($past_events as $event) {
            $output .= '<div class="col-12 col-md-4 mb-4 eventi-loop">'; // Griglia a 3 colonne
                // Box con evento passato
                $output .= '<div class="card rounded overflow-hidden">';
                    $output .= '<div class="card-header bg-secondary text-white d-flex justify-content-end align-items-center">';
                        $output .= '<span>' . date('Y', $event['start_date']) . '</span>'; // Anno
                        $output .= '<span class="badge bg-white text-dark fs-4 fw-normal">' . date('M j', $event['start_date']) . '</span>'; // Mese e giorno
                    $output .= '</div>';
                    $output .= '<div class="card-body bg-light p-4" style="border-bottom: 1px dotted #333;">';
                        $output .= '<span>' . date('M j, Y', $event['start_date']) . ' - ' . date('M j, Y', $event['end_date']) . '</span>';
                    $output .= '</div>';
                    $output .= '<div class="card-body bg-light p-4">';
                        $output .= '<h5><a href="' . $event['link'] . '" class="text-dark clickable-parent">' . $event['title'] . '</a></h5>';
        
                        $output .= '<a href="' . $event['link'] . '" class="text-primary text-decoration-underline mt-3">Vedi evento ></a>';
                    $output .= '</div>';
                $output .= '</div>';
            $output .= '</div>';
        }

        $output .= '</div>'; // Fine della riga
    }

    // Ripristiniamo la query originale
    wp_reset_postdata();

    // Chiudiamo il contenitore
    $output .= '</div>';

    return $output;
}
add_shortcode('eventi', 'eventi_shortcode');

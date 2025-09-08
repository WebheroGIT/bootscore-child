<?php

// Assicurati che il file non sia accessibile direttamente
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Funzione helper per tradurre i mesi in italiano
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

// Funzione helper per formattare la data in italiano
function format_date_italian($timestamp, $format) {
    $date_parts = explode(' ', date($format, $timestamp));
    foreach ($date_parts as &$part) {
        if (strlen($part) == 3 && ctype_alpha($part)) {
            $part = translate_month_to_italian($part);
        }
    }
    return implode(' ', $date_parts);
}

function eventi_shortcode($atts) {
    // Parametri di default
    $atts = shortcode_atts(
        array(
            'type' => '', // tipo di eventi: 'futuri' per visualizzare solo gli eventi futuri
        ), 
        $atts, 
        'eventi'
    );

    // Impostiamo la query per ottenere gli eventi
    $args = array(
        'post_type' => 'eventi', // Tipo di post 'eventi'
        'posts_per_page' => -1, // Mostra tutti gli eventi
        'orderby' => 'meta_value', // Ordina per data personalizzata
        'order' => 'ASC', // Ordine crescente (dal più recente al più lontano)
        'meta_key' => 'evento_data_inizio', // La chiave del campo personalizzato per la data di inizio
        'meta_type' => 'NUMERIC', // La data è un timestamp numerico
    );

    // Esegui la query
    $query = new WP_Query($args);

    // Iniziamo il contenitore
    $output = '<div class="container mt-5">';

    // Inizializzare le variabili per gli eventi futuri, in corso e passati
    $future_events = [];
    $current_events = [];
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
            if ($start_timestamp > $current_date) {
                // Evento futuro (inizia dopo oggi)
                $future_events[] = [
                    'title' => get_the_title(),
                    'start_date' => $start_timestamp,
                    'end_date' => $end_timestamp,
                    'link' => get_permalink()
                ];
            } elseif ($start_timestamp <= $current_date && $end_timestamp >= $current_date) {
                // Evento in corso (iniziato oggi o prima, ma finisce oggi o dopo)
                $current_events[] = [
                    'title' => get_the_title(),
                    'start_date' => $start_timestamp,
                    'end_date' => $end_timestamp,
                    'link' => get_permalink()
                ];
            } else {
                // Evento passato (finito prima di oggi)
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

    // Ordina gli eventi in corso (più recente in alto)
    usort($current_events, function($a, $b) {
        return $a['start_date'] <=> $b['start_date'];
    });

    // Ordina gli eventi passati (più recente in alto)
    usort($past_events, function($a, $b) {
        return $a['start_date'] <=> $b['start_date'];
    });

    // Se l'utente ha passato il parametro 'type' con valore 'futuri', mostriamo solo i futuri (3 eventi)
    if ($atts['type'] === 'futuri') {
        $future_events = array_slice($future_events, 0, 3); // Prendiamo solo i primi 3 eventi futuri
        $output .= '<div class="row">';
        // Mostriamo solo gli eventi futuri
        foreach ($future_events as $event) {
            $output .= '<div class="col-12 col-md-4 mb-4 eventi-loop">'; // Griglia a 3 colonne
                $output .= '<div class="card rounded overflow-hidden">';
                    $output .= '<div class="card-header bg-primary text-white d-flex justify-content-end align-items-center">';
                        $output .= '<span>' . date('Y', $event['start_date']) . '</span>'; // Anno
                        $output .= '<span class="badge bg-white text-dark fs-4 fw-normal">' . format_date_italian($event['start_date'], 'M j') . '</span>'; // Mese e giorno
                    $output .= '</div>';
                    $output .= '<div class="card-body bg-light p-4" style="border-bottom: 1px dotted #333;">';
                        // Mostra solo una data se inizio e fine sono uguali
                        if (date('Y-m-d', $event['start_date']) === date('Y-m-d', $event['end_date'])) {
                            $output .= '<span>' . format_date_italian($event['start_date'], 'M j, Y') . '</span>';
                        } else {
                            $output .= '<span>' . format_date_italian($event['start_date'], 'M j, Y') . ' - ' . format_date_italian($event['end_date'], 'M j, Y') . '</span>';
                        }
                    $output .= '</div>';
                    $output .= '<div class="card-body bg-light p-4">';
                        $output .= '<h5><a href="' . $event['link'] . '" class="text-dark clickable-parent">' . $event['title'] . '</a></h5>';
                        $output .= '<a href="' . $event['link'] . '" class="text-primary text-decoration-underline mt-3">Vedi evento ></a>';
                    $output .= '</div>';
                $output .= '</div>';
            $output .= '</div>';
        }
        $output .= '</div>';
    } else {
        // Se non è passato il parametro 'type', mostriamo sia gli eventi futuri che passati
        if (count($future_events) > 0) {
            $output .= '<h3>Prossimi eventi</h3>';
            $output .= '<div class="row">'; // Inizio della griglia

            foreach ($future_events as $event) {
                $output .= '<div class="col-12 col-md-4 mb-4 eventi-loop">'; // Griglia a 3 colonne
                    $output .= '<div class="card rounded overflow-hidden">';
                        $output .= '<div class="card-header bg-primary text-white d-flex justify-content-end align-items-center">';
                            $output .= '<span>' . date('Y', $event['start_date']) . '</span>'; // Anno
                            $output .= '<span class="badge bg-white text-dark fs-4 fw-normal">' . format_date_italian($event['start_date'], 'M j') . '</span>'; // Mese e giorno
                        $output .= '</div>';
                        $output .= '<div class="card-body bg-light p-4" style="border-bottom: 1px dotted #333;">';
                            // Mostra solo una data se inizio e fine sono uguali
                            if (date('Y-m-d', $event['start_date']) === date('Y-m-d', $event['end_date'])) {
                                $output .= '<span>' . format_date_italian($event['start_date'], 'M j, Y') . '</span>';
                            } else {
                                $output .= '<span>' . format_date_italian($event['start_date'], 'M j, Y') . ' - ' . format_date_italian($event['end_date'], 'M j, Y') . '</span>';
                            }
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

        // Mostra gli eventi in corso
        if (count($current_events) > 0) {
            $output .= '<h3>Eventi in corso</h3>';
            $output .= '<div class="row">'; // Inizio della griglia

            foreach ($current_events as $event) {
                $output .= '<div class="col-12 col-md-4 mb-4 eventi-loop">'; // Griglia a 3 colonne
                    $output .= '<div class="card rounded overflow-hidden">';
                        $output .= '<div class="card-header bg-success text-white d-flex justify-content-end align-items-center">';
                            $output .= '<span>' . date('Y', $event['start_date']) . '</span>'; // Anno
                            $output .= '<span class="badge bg-white text-dark fs-4 fw-normal">' . format_date_italian($event['start_date'], 'M j') . '</span>'; // Mese e giorno
                        $output .= '</div>';
                        $output .= '<div class="card-body bg-light p-4" style="border-bottom: 1px dotted #333;">';
                            // Mostra solo una data se inizio e fine sono uguali
                            if (date('Y-m-d', $event['start_date']) === date('Y-m-d', $event['end_date'])) {
                                $output .= '<span>' . format_date_italian($event['start_date'], 'M j, Y') . '</span>';
                            } else {
                                $output .= '<span>' . format_date_italian($event['start_date'], 'M j, Y') . ' - ' . format_date_italian($event['end_date'], 'M j, Y') . '</span>';
                            }
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
                    $output .= '<div class="card rounded overflow-hidden">';
                        $output .= '<div class="card-header bg-secondary text-white d-flex justify-content-end align-items-center">';
                            $output .= '<span>' . date('Y', $event['start_date']) . '</span>'; // Anno
                            $output .= '<span class="badge bg-white text-dark fs-4 fw-normal">' . format_date_italian($event['start_date'], 'M j') . '</span>'; // Mese e giorno
                        $output .= '</div>';
                        $output .= '<div class="card-body bg-light p-4" style="border-bottom: 1px dotted #333;">';
                            // Mostra solo una data se inizio e fine sono uguali
                            if (date('Y-m-d', $event['start_date']) === date('Y-m-d', $event['end_date'])) {
                                $output .= '<span>' . format_date_italian($event['start_date'], 'M j, Y') . '</span>';
                            } else {
                                $output .= '<span>' . format_date_italian($event['start_date'], 'M j, Y') . ' - ' . format_date_italian($event['end_date'], 'M j, Y') . '</span>';
                            }
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
    }

    // Ripristiniamo la query originale
    wp_reset_postdata();

    // Chiudiamo il contenitore
    $output .= '</div>';

    return $output;
}
add_shortcode('eventi', 'eventi_shortcode');

// [eventi type="futuri"] per visualizzare solo gli eventi futuri

// [eventi]

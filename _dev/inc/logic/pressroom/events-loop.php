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

// Funzione helper per ottenere l'immagine dell'evento (featured o fallback)
function get_evento_image($post_id) {
    // Prova a ottenere la featured image
    $thumbnail_url = get_the_post_thumbnail_url($post_id, 'full');
    
    // Se non c'è featured image, usa l'immagine di fallback con ID 4280
    if (!$thumbnail_url) {
        $fallback_image_id = 4280;
        $fallback_url = wp_get_attachment_image_url($fallback_image_id, 'full');
        if ($fallback_url) {
            $thumbnail_url = $fallback_url;
        }
    }
    
    if ($thumbnail_url) {
        return '<div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: url(' . esc_url($thumbnail_url) . '); background-size: cover; background-position: center;"></div>';
    } else {
        // Fallback finale se nemmeno l'immagine 4280 è disponibile
        return '<div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: #e9ecef;"></div>';
    }
}

function eventi_shortcode($atts) {
    // Parametri di default
    $atts = shortcode_atts(
        array(
            'type' => '', // tipo di eventi: 'futuri' per visualizzare solo gli eventi futuri
            'per_page' => 9, // numero di eventi per pagina per la sezione "passati"
            'cat' => '', // filtro per meta evento_category
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

    // Applica filtro categoria se presente
    $cat_value = trim((string) $atts['cat']);
    if ($cat_value !== '') {
        $args['meta_query'] = isset($args['meta_query']) ? $args['meta_query'] : array();
        $args['meta_query'][] = array(
            'key' => 'evento_category',
            'value' => $cat_value,
            'compare' => '=',
        );
    }

    // Esegui la query
    $query = new WP_Query($args);

    // Iniziamo il contenitore
    $output = '<div class="container mt-3">';

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
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'start_date' => $start_timestamp,
                    'end_date' => $end_timestamp,
                    'link' => get_permalink()
                ];
            } elseif ($start_timestamp <= $current_date && $end_timestamp >= $current_date) {
                // Evento in corso (iniziato oggi o prima, ma finisce oggi o dopo)
                $current_events[] = [
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'start_date' => $start_timestamp,
                    'end_date' => $end_timestamp,
                    'link' => get_permalink()
                ];
            } else {
                // Evento passato (finito prima di oggi)
                $past_events[] = [
                    'id' => get_the_ID(),
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

    // Ordina gli eventi passati (più recente in alto - ordine inverso)
    usort($past_events, function($a, $b) {
        return $b['start_date'] <=> $a['start_date'];
    });

	// Se l'utente ha passato il parametro 'type' con valore 'futuri', mostriamo eventi in corso e futuri (max 3)
	if ($atts['type'] === 'futuri') {
		$events_to_show = array_slice(array_merge($current_events, $future_events), 0, 3);
		$output .= '<div class="row">';
		// Mostriamo eventi in corso e futuri
		foreach ($events_to_show as $event) {
            $output .= '<div class="col-12 col-md-4 mb-4 eventi-loop">'; // Griglia a 3 colonne
                $output .= '<div class="card rounded overflow-hidden h-100">';
                    $output .= '<div class="card-header bg-primary text-white d-flex justify-content-end align-items-center">';
                        $output .= '<span>' . date('Y', $event['start_date']) . '</span>'; // Anno
                        $output .= '<span class="badge bg-white text-dark fs-4 fw-normal">' . format_date_italian($event['start_date'], 'M j') . '</span>'; // Mese e giorno
                    $output .= '</div>';
                    // Featured image subito dopo l'header
                    $image_aria_label = esc_attr(sprintf(__('Vai all\'evento: %s', 'bootscore'), $event['title']));
                    $output .= '<div class="card-img-top" style="height: 200px; overflow: hidden; position: relative;">';
                        $output .= '<a href="' . esc_url($event['link']) . '" aria-label="' . $image_aria_label . '" title="' . $image_aria_label . '" style="display: block; width: 100%; height: 100%;">';
                            $output .= get_evento_image($event['id']);
                            $output .= '<span class="visually-hidden-focusable">' . esc_html($event['title']) . '</span>';
                        $output .= '</a>';
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
                        $title_aria_label = esc_attr(sprintf(__('Vai all\'evento: %s', 'bootscore'), $event['title']));
                        $output .= '<a href="' . esc_url($event['link']) . '" class="text-dark clickable-parent" aria-label="' . $title_aria_label . '" title="' . $title_aria_label . '"><h6>' . esc_html($event['title']) . '</h6></a>';
                        $output .= '<span class="text-primary text-decoration-underline mt-3">Vedi evento ></span>';
                    $output .= '</div>';
                $output .= '</div>';
            $output .= '</div>';
        }
        $output .= '</div>';
    } else {
        // Se non è passato il parametro 'type', mostriamo prima gli eventi in corso, poi i futuri e infine i passati
        
        // Mostra gli eventi in corso per primi
        if (count($current_events) > 0) {
            $output .= '<div class="mb-5 rounded">';
                $output .= '<div class="mb-4">';
                    $output .= '<h3 class="d-inline-block mb-3">';
                        $output .= '<span class="badge bg-success text-white px-4 py-2 fs-5 fw-normal rounded-pill" style="border: 2px solid #379975;">Eventi in corso</span>';
                    $output .= '</h3>';
                $output .= '</div>';
                $output .= '<div class="row">'; // Inizio della griglia

            foreach ($current_events as $event) {
                $output .= '<div class="col-12 col-md-4 mb-4 eventi-loop">'; // Griglia a 3 colonne
                    $output .= '<div class="card rounded overflow-hidden h-100">';
                        $output .= '<div class="card-header bg-success text-white d-flex justify-content-end align-items-center">';
                            $output .= '<span>' . date('Y', $event['start_date']) . '</span>'; // Anno
                            $output .= '<span class="badge bg-white text-dark fs-4 fw-normal">' . format_date_italian($event['start_date'], 'M j') . '</span>'; // Mese e giorno
                        $output .= '</div>';
                        // Featured image subito dopo l'header
                        $image_aria_label = esc_attr(sprintf(__('Vai all\'evento: %s', 'bootscore'), $event['title']));
                        $output .= '<div class="card-img-top" style="height: 200px; overflow: hidden; position: relative;">';
                            $output .= '<a href="' . esc_url($event['link']) . '" aria-label="' . $image_aria_label . '" title="' . $image_aria_label . '" style="display: block; width: 100%; height: 100%;">';
                                $output .= get_evento_image($event['id']);
                                $output .= '<span class="visually-hidden-focusable">' . esc_html($event['title']) . '</span>';
                            $output .= '</a>';
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
                            $title_aria_label = esc_attr(sprintf(__('Vai all\'evento: %s', 'bootscore'), $event['title']));
                            $output .= '<a href="' . esc_url($event['link']) . '" class="text-dark clickable-parent" aria-label="' . $title_aria_label . '" title="' . $title_aria_label . '"><h6>' . esc_html($event['title']) . '</h6></a>';
                            $output .= '<span class="text-primary text-decoration-underline mt-3">Vedi evento ></span>';
                        $output .= '</div>';
                    $output .= '</div>';
                $output .= '</div>';
            }

            $output .= '</div>'; // Fine della riga
            $output .= '</div>'; // Fine del contenitore eventi in corso
        }
        
        // Poi mostra gli eventi futuri
        if (count($future_events) > 0) {
            $output .= '<div class="mb-5 rounded">';
                $output .= '<div class="mb-4">';
                    $output .= '<h3 class="d-inline-block mb-3">';
                        $output .= '<span class="badge bg-primary text-white px-4 py-2 fs-5 fw-normal rounded-pill">Prossimi eventi</span>';
                    $output .= '</h3>';
                $output .= '</div>';
                $output .= '<div class="row">'; // Inizio della griglia

            foreach ($future_events as $event) {
                $output .= '<div class="col-12 col-md-4 mb-4 eventi-loop">'; // Griglia a 3 colonne
                    $output .= '<div class="card rounded overflow-hidden h-100">';
                        $output .= '<div class="card-header bg-primary text-white d-flex justify-content-end align-items-center">';
                            $output .= '<span>' . date('Y', $event['start_date']) . '</span>'; // Anno
                            $output .= '<span class="badge bg-white text-dark fs-4 fw-normal">' . format_date_italian($event['start_date'], 'M j') . '</span>'; // Mese e giorno
                        $output .= '</div>';
                        // Featured image subito dopo l'header
                        $image_aria_label = esc_attr(sprintf(__('Vai all\'evento: %s', 'bootscore'), $event['title']));
                        $output .= '<div class="card-img-top" style="height: 200px; overflow: hidden; position: relative;">';
                            $output .= '<a href="' . esc_url($event['link']) . '" aria-label="' . $image_aria_label . '" title="' . $image_aria_label . '" style="display: block; width: 100%; height: 100%;">';
                                $output .= get_evento_image($event['id']);
                                $output .= '<span class="visually-hidden-focusable">' . esc_html($event['title']) . '</span>';
                            $output .= '</a>';
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
                            $title_aria_label = esc_attr(sprintf(__('Vai all\'evento: %s', 'bootscore'), $event['title']));
                            $output .= '<a href="' . esc_url($event['link']) . '" class="text-dark clickable-parent" aria-label="' . $title_aria_label . '" title="' . $title_aria_label . '"><h6>' . esc_html($event['title']) . '</h6></a>';
                            $output .= '<span class="text-primary text-decoration-underline mt-3">Vedi evento ></span>';
                        $output .= '</div>';
                    $output .= '</div>';
                $output .= '</div>';
            }

            $output .= '</div>'; // Fine della riga
            $output .= '</div>'; // Fine del contenitore eventi futuri
        }

		// Mostra gli eventi passati (con paginazione)
		if (count($past_events) > 0) {
			$per_page = max(1, intval($atts['per_page']));
			$current_page = isset($_GET['eventi_page']) ? max(1, intval($_GET['eventi_page'])) : 1;
			$total_past = count($past_events);
			$total_pages = (int) ceil($total_past / $per_page);
			$offset = ($current_page - 1) * $per_page;
			$paged_past_events = array_slice($past_events, $offset, $per_page);
            $output .= '<div class="mb-5 rounded">';
                $output .= '<div class="mb-4">';
                    $output .= '<h3 class="d-inline-block mb-3">';
                        $output .= '<span class="badge bg-secondary text-white px-4 py-2 fs-5 fw-normal rounded-pill" style="border: 2px solid #FF914D;">Eventi passati</span>';
                    $output .= '</h3>';
                $output .= '</div>';
				$output .= '<div class="row" id="past-events-grid">'; // Inizio della griglia

			foreach ($paged_past_events as $event) {
                $output .= '<div class="col-12 col-md-4 mb-4 eventi-loop">'; // Griglia a 3 colonne
                    $output .= '<div class="card rounded overflow-hidden h-100">';
                        $output .= '<div class="card-header bg-secondary text-white d-flex justify-content-end align-items-center">';
                            $output .= '<span>' . date('Y', $event['start_date']) . '</span>'; // Anno
                            $output .= '<span class="badge bg-white text-dark fs-4 fw-normal">' . format_date_italian($event['start_date'], 'M j') . '</span>'; // Mese e giorno
                        $output .= '</div>';
                        // Featured image subito dopo l'header
                        $image_aria_label = esc_attr(sprintf(__('Vai all\'evento: %s', 'bootscore'), $event['title']));
                        $output .= '<div class="card-img-top" style="height: 200px; overflow: hidden; position: relative;">';
                            $output .= '<a href="' . esc_url($event['link']) . '" aria-label="' . $image_aria_label . '" title="' . $image_aria_label . '" style="display: block; width: 100%; height: 100%;">';
                                $output .= get_evento_image($event['id']);
                                $output .= '<span class="visually-hidden-focusable">' . esc_html($event['title']) . '</span>';
                            $output .= '</a>';
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
                            $title_aria_label = esc_attr(sprintf(__('Vai all\'evento: %s', 'bootscore'), $event['title']));
                            $output .= '<a href="' . esc_url($event['link']) . '" class="text-dark clickable-parent" aria-label="' . $title_aria_label . '" title="' . $title_aria_label . '"><h6>' . esc_html($event['title']) . '</h6></a>';
                            $output .= '<span class="text-primary text-decoration-underline mt-3">Vedi evento ></span>';
                        $output .= '</div>';
                    $output .= '</div>';
                $output .= '</div>';
            }

			$output .= '</div>'; // Fine della riga
			
			// Bottone Load More (AJAX)
            if ($total_pages > 1 && $current_page < $total_pages) {
				$nonce = wp_create_nonce('load_past_events');
				$ajax_url = admin_url('admin-ajax.php');
				$output .= '<div class="text-center mt-3">';
                    $output .= '<button id="load-more-past-events" class="btn btn-outline-secondary" data-current-page="' . esc_attr($current_page) . '" data-total-pages="' . esc_attr($total_pages) . '" data-per-page="' . esc_attr($per_page) . '" data-nonce="' . esc_attr($nonce) . '" data-ajax-url="' . esc_url($ajax_url) . '" data-cat="' . esc_attr($cat_value) . '">Carica altri</button>';
				$output .= '</div>';
				// Inline script minimale
                $output .= '<script>(function(){var btn=document.getElementById("load-more-past-events");if(!btn)return;var loading=false;btn.addEventListener("click",function(){if(loading)return;loading=true;btn.disabled=true;var current=parseInt(btn.getAttribute("data-current-page"),10)||1;var total=parseInt(btn.getAttribute("data-total-pages"),10)||1;var perPage=parseInt(btn.getAttribute("data-per-page"),10)||9;var nonce=btn.getAttribute("data-nonce");var ajaxUrl=btn.getAttribute("data-ajax-url");var cat=btn.getAttribute("data-cat")||"";var next=current+1;var fd=new FormData();fd.append("action","load_past_events");fd.append("page",next);fd.append("per_page",perPage);fd.append("cat",cat);fd.append("nonce",nonce);fetch(ajaxUrl,{method:"POST",body:fd}).then(function(r){return r.text()}).then(function(html){var grid=document.getElementById("past-events-grid");if(grid&&html){grid.insertAdjacentHTML("beforeend",html);}current=next;btn.setAttribute("data-current-page",String(current));if(current>=total){btn.style.display="none";}btn.disabled=false;loading=false;}).catch(function(){btn.disabled=false;loading=false;});});})();</script>';
			}
            $output .= '</div>'; // Fine del contenitore eventi passati
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

// AJAX: restituisce il markup delle card degli eventi passati per la pagina richiesta
function load_past_events_ajax() {
	check_ajax_referer('load_past_events', 'nonce');

	$page = isset($_POST['page']) ? max(1, intval($_POST['page'])) : 1;
	$per_page = isset($_POST['per_page']) ? max(1, intval($_POST['per_page'])) : 9;
    $cat_value = isset($_POST['cat']) ? sanitize_text_field(wp_unslash($_POST['cat'])) : '';

	$args = array(
		'post_type' => 'eventi',
		'posts_per_page' => -1,
		'orderby' => 'meta_value',
		'order' => 'ASC',
		'meta_key' => 'evento_data_inizio',
		'meta_type' => 'NUMERIC',
	);

    // Applica filtro categoria se presente
    if ($cat_value !== '') {
        $args['meta_query'] = isset($args['meta_query']) ? $args['meta_query'] : array();
        $args['meta_query'][] = array(
            'key' => 'evento_category',
            'value' => $cat_value,
            'compare' => '=',
        );
    }

    $query = new WP_Query($args);

	$past_events = [];
	if ($query->have_posts()) {
		$current_date = current_time('timestamp');
		while ($query->have_posts()) {
			$query->the_post();
			$evento_data_inizio = rwmb_meta('evento_data_inizio');
			$evento_data_fine = rwmb_meta('evento_data_fine');
			$start_timestamp = strtotime($evento_data_inizio);
			if ($start_timestamp === false || $start_timestamp < 0) { $start_timestamp = 0; }
			$end_timestamp = strtotime($evento_data_fine);
			if ($end_timestamp === false || $end_timestamp < 0) { $end_timestamp = 0; }
			if (!($start_timestamp > $current_date) && !($start_timestamp <= $current_date && $end_timestamp >= $current_date)) {
				$past_events[] = [
					'id' => get_the_ID(),
					'title' => get_the_title(),
					'start_date' => $start_timestamp,
					'end_date' => $end_timestamp,
					'link' => get_permalink()
				];
			}
		}
	}

	// Ordina passati (più recente in alto - ordine inverso)
	usort($past_events, function($a, $b) { return $b['start_date'] <=> $a['start_date']; });

	$offset = ($page - 1) * $per_page;
	$paged = array_slice($past_events, $offset, $per_page);

	// Output solo le colonne delle card (stesso markup della griglia principale)
	$html = '';
	foreach ($paged as $event) {
		$html .= '<div class="col-12 col-md-4 mb-4 eventi-loop">';
			$html .= '<div class="card rounded overflow-hidden h-100">';
				$html .= '<div class="card-header bg-secondary text-white d-flex justify-content-end align-items-center">';
					$html .= '<span>' . date('Y', $event['start_date']) . '</span>';
					$html .= '<span class="badge bg-white text-dark fs-4 fw-normal">' . format_date_italian($event['start_date'], 'M j') . '</span>';
				$html .= '</div>';
				// Featured image subito dopo l'header
				$image_aria_label = esc_attr(sprintf(__('Vai all\'evento: %s', 'bootscore'), $event['title']));
				$html .= '<div class="card-img-top" style="height: 200px; overflow: hidden; position: relative;">';
					$html .= '<a href="' . esc_url($event['link']) . '" aria-label="' . $image_aria_label . '" title="' . $image_aria_label . '" style="display: block; width: 100%; height: 100%;">';
						$html .= get_evento_image($event['id']);
						$html .= '<span class="visually-hidden-focusable">' . esc_html($event['title']) . '</span>';
					$html .= '</a>';
				$html .= '</div>';
				$html .= '<div class="card-body bg-light p-4" style="border-bottom: 1px dotted #333;">';
					if (date('Y-m-d', $event['start_date']) === date('Y-m-d', $event['end_date'])) {
						$html .= '<span>' . format_date_italian($event['start_date'], 'M j, Y') . '</span>';
					} else {
						$html .= '<span>' . format_date_italian($event['start_date'], 'M j, Y') . ' - ' . format_date_italian($event['end_date'], 'M j, Y') . '</span>';
					}
				$html .= '</div>';
				$html .= '<div class="card-body bg-light p-4">';
					$title_aria_label = esc_attr(sprintf(__('Vai all\'evento: %s', 'bootscore'), $event['title']));
					$html .= '<a href="' . esc_url($event['link']) . '" class="text-dark clickable-parent" aria-label="' . $title_aria_label . '" title="' . $title_aria_label . '"><h6>' . esc_html($event['title']) . '</h6></a>';
					$html .= '<span class="text-primary text-decoration-underline mt-3">Vedi evento ></span>';
				$html .= '</div>';
			$html .= '</div>';
		$html .= '</div>';
	}

	wp_reset_postdata();
	echo $html;
	die();
}

add_action('wp_ajax_load_past_events', 'load_past_events_ajax');
add_action('wp_ajax_nopriv_load_past_events', 'load_past_events_ajax');

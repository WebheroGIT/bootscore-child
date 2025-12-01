<?php
// Assicurati che il file non sia accessibile direttamente
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function articoli_shortcode( $atts ) {
    // Conserva gli attributi passati dall'utente per capire cosa è stato specificato esplicitamente
    $user_atts = is_array($atts) ? $atts : array();

    // Definiamo gli attributi di default
    $atts = shortcode_atts(
        array(
            'posts_per_page' => 6, // Numero di articoli per pagina di default (mantenuto per compatibilità)
            'posts' => '', // Numero di post da visualizzare (nuovo parametro)
            'type' => 'post', // Tipo di post (ora configurabile)
            'orderby' => 'date', // Ordinamento di default per data
            'order' => 'DESC', // Ordinamento decrescente di default
            'slider' => false, // Aggiungiamo un attributo per controllare se vogliamo lo slider (default false)
            'id' => '', // Lista di ID separati da virgola
            'tax' => '', // Tassonomia per filtrare
            'cat' => '', // Categoria/termine della tassonomia
            'stato' => '', // Filtro per stato del progetto (attivo/concluso) - funziona solo se il post type ha il campo pricerca_stato
            'gestione' => '', // Filtro per gestione del progetto (ateneo/economico/ingegneria/umane) - funziona solo se il post type ha il campo pricerca_gestione
            'dipartimento' => '', // Filtro per dipartimento/struttura di gestione (slug o ID della taxonomy)
            'read_more_text' => 'Continua a leggere', // Testo personalizzabile per il link "continua a leggere"
            'columns' => '3', // Numero di colonne per la griglia (1, 2, 3, 4, 6)
            'hide_date' => false, // Nascondere la data (true/false)
            'text_center' => false, // Centrare il testo (true/false)
            'title_size' => 'fs-5', // Dimensione del titolo (fs-1, fs-2, fs-3, fs-4, fs-5, fs-6)
        ),
        $atts,
        'articoli' // Nome dello shortcode
    );

    // Determina il numero di post da mostrare
    $num_posts = !empty($atts['posts']) ? intval($atts['posts']) : intval($atts['posts_per_page']);
    
    // Determina le classi CSS per le colonne
    $columns = intval($atts['columns']);
    $valid_columns = array(1, 2, 3, 4, 5, 6);
    if (!in_array($columns, $valid_columns)) {
        $columns = 3; // Default se non valido
    }

    // Verifica se l'utente ha esplicitamente passato l'attributo "columns"
    $user_set_columns = array_key_exists('columns', $user_atts);
    
    // Valida la dimensione del titolo
    $valid_title_sizes = array('fs-1', 'fs-2', 'fs-3', 'fs-4', 'fs-5', 'fs-6');
    $title_size = $atts['title_size'];
    if (!in_array($title_size, $valid_title_sizes)) {
        $title_size = 'fs-5'; // Default se non valido
    }
    
    // Genera le classi CSS per le colonne
    $column_classes = array(
        1 => 'row-cols-1',
        2 => 'row-cols-1 row-cols-md-2',
        3 => 'row-cols-1 row-cols-md-3',
        4 => 'row-cols-1 row-cols-md-2 row-cols-lg-4',
        5 => 'row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-5 custom-5-cols',
        6 => 'row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-6'
    );
    
    // Impostiamo la query per ottenere gli articoli con gli attributi passati
    $args = array(
        'post_type' => $atts['type'], // Tipo di post configurabile
        'wp_grid_builder' => 'wpgb-content', // Supporto per WP Grid Builder - permette di usare filtri GridBuilder in Gutenberg
        'posts_per_page' => $num_posts, // Numero di articoli
        'orderby' => $atts['orderby'], // Ordinamento
        'order' => $atts['order'], // Più recente in cima
    );

    // Gestione paginazione: usa un parametro dedicato per lo shortcode per evitare conflitti
    $page_param = 'articoli_page';
    if ( isset($_GET[$page_param]) ) {
        $paged = max(1, intval($_GET[$page_param]));
    } else {
        // Fallback a variabili globali (archivi/pagine con pretty permalinks)
        $paged = get_query_var('paged') ? intval(get_query_var('paged')) : (get_query_var('page') ? intval(get_query_var('page')) : 1);
        if ($paged < 1) { $paged = 1; }
    }
    if ($paged < 1) { $paged = 1; }
    $args['paged'] = $paged;
    
    // Aggiungi filtro per tassonomia se specificato
    if (!empty($atts['tax']) && !empty($atts['cat'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => $atts['tax'],
                'field' => 'slug',
                'terms' => $atts['cat']
            )
        );
    }

    // Aggiungi filtro per stato (funziona solo se il post type ha il campo pricerca_stato)
    // Filtra per valore "attivo" o "concluso" nel campo Meta Box pricerca_stato
    // Sicuro: se il post type non ha questo campo, semplicemente non trova risultati (nessun errore)
    if (!empty($atts['stato'])) {
        $stato_value = strtolower(trim($atts['stato']));
        // Valida che lo stato sia uno dei valori accettati
        if (in_array($stato_value, array('attivo', 'concluso'))) {
            // Inizializza meta_query se non esiste già
            if (!isset($args['meta_query'])) {
                $args['meta_query'] = array();
            }
            // Aggiungi filtro per il campo pricerca_stato
            // Cerca il valore esattamente (valori normalizzati: "attivo" o "concluso")
            // Nota: il valore viene normalizzato a lowercase prima del confronto
            $args['meta_query'][] = array(
                'key' => 'pricerca_stato',
                'value' => $stato_value,
                'compare' => '='
            );
            // Se ci sono più meta_query, impostali come AND
            if (count($args['meta_query']) > 1) {
                $args['meta_query']['relation'] = 'AND';
            }
        }
    }

    // Aggiungi filtro per gestione (funziona solo se il post type ha il campo pricerca_gestione)
    // Filtra per valore "ateneo", "economico", "ingegneria", "umane" nel campo Meta Box pricerca_gestione
    // Sicuro: se il post type non ha questo campo, semplicemente non trova risultati (nessun errore)
    // Usabile in combinazione con stato e altri filtri
    if (!empty($atts['gestione'])) {
        $gestione_value = strtolower(trim($atts['gestione']));
        // Valida che la gestione sia uno dei valori accettati
        $valid_gestione_values = array('ateneo', 'economico', 'ingegneria', 'umane');
        if (in_array($gestione_value, $valid_gestione_values)) {
            // Inizializza meta_query se non esiste già
            if (!isset($args['meta_query'])) {
                $args['meta_query'] = array();
            }
            // Aggiungi filtro per il campo pricerca_gestione
            // Cerca il valore esattamente (valori normalizzati a lowercase)
            $args['meta_query'][] = array(
                'key' => 'pricerca_gestione',
                'value' => $gestione_value,
                'compare' => '='
            );
            // Se ci sono più meta_query, impostali come AND
            if (count($args['meta_query']) > 1) {
                $args['meta_query']['relation'] = 'AND';
            }
        }
    }

    // Aggiungi filtro per dipartimento (struttura di gestione)
    // Supporta sia taxonomy che custom field (cerca prima in taxonomy comuni, poi in custom field)
    if (!empty($atts['dipartimento'])) {
        $dipartimento_value = trim($atts['dipartimento']);
        
        // Prova prima con taxonomy comuni per progetti
        $possible_taxonomies = array('cat-dipartimenti', 'dipartimenti', 'struttura-gestione');
        $tax_found = false;
        
        foreach ($possible_taxonomies as $taxonomy) {
            if (taxonomy_exists($taxonomy)) {
                // Inizializza tax_query se non esiste già
                if (!isset($args['tax_query'])) {
                    $args['tax_query'] = array();
                }
                
                // Aggiungi filtro per questa taxonomy
                $args['tax_query'][] = array(
                    'taxonomy' => $taxonomy,
                    'field' => 'slug',
                    'terms' => $dipartimento_value
                );
                $tax_found = true;
                break;
            }
        }
        
        // Se non è una taxonomy, prova con custom field
        if (!$tax_found) {
            if (!isset($args['meta_query'])) {
                $args['meta_query'] = array();
            }
            // Prova con possibili nomi di custom field
            $args['meta_query'][] = array(
                'key' => 'pricerca_dipartimento',
                'value' => $dipartimento_value,
                'compare' => 'LIKE'
            );
        }
        
        // Se ci sono più tax_query, impostali come AND (solo se non esiste già una relazione)
        if (isset($args['tax_query']) && count($args['tax_query']) > 1 && !isset($args['tax_query']['relation'])) {
            $args['tax_query']['relation'] = 'AND';
        }
    }

    // Se sono specificati degli ID, li aggiungiamo alla query
    if ( !empty($atts['id']) ) {
        $post_ids = explode(',', $atts['id']);
        $post_ids = array_map('trim', $post_ids); // Rimuoviamo eventuali spazi
        $post_ids = array_map('intval', $post_ids); // Convertiamo in interi
        $args['post__in'] = $post_ids;
        $args['orderby'] = 'post__in'; // Manteniamo l'ordine specificato negli ID
    }

    $query = new WP_Query($args);

    // ID univoco per questa istanza dello shortcode (per anchor e scroll)
    $shortcode_unique_id = 'articoli-' . uniqid();
    
    // Iniziamo il contenitore con ID per anchor e classe wpgb-content per GridBuilder
    $output = '<div id="' . esc_attr($shortcode_unique_id) . '" class="mt-5 w-100 wpgb-content">';
    
    // CSS responsive per l'altezza minima del card-body
    $output .= '<style>
        @media (max-width: 767px) {
            .news-card-body { min-height: 190px !important; }
        }
        @media (min-width: 768px) {
            .news-card-body { min-height: 240px !important; }
        }
    </style>';

    // Se lo slider è abilitato, iniziamo il contenitore Swiper
    if ( $atts['slider'] ) {
        $output .= '<div class="swiper swiper-article-container swiper-container" id="swiper-article-' . uniqid() . '">';
        $output .= '<div class="swiper-button-next"></div>';
        $output .= '<div class="swiper-button-prev"></div>';
        $output .= '<div class="swiper-pagination"></div>';
        $output .= '<div class="swiper-wrapper swiperArticle-wrapper">';
    } else {
        // Row Bootstrap standard senza d-flex/h-100 che interferiscono con il layout
        $output .= '<div class="row ' . $column_classes[$columns] . ' g-4">'; // Griglia con colonne dinamiche
    }

    // Verifica se ci sono articoli
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            // Generiamo l'ID del post per la paginazione
            $post_id = get_the_ID();

            // Otteniamo la featured image
            $featured_image = get_the_post_thumbnail($post_id, 'full');

            // Otteniamo il titolo e la data
            $title = get_the_title();
            
            // Gestione speciale delle date per gli eventi
            if ($atts['type'] === 'eventi') {
                // Ottieni le date custom field per gli eventi (MetaBox.io)
                $start_date = rwmb_meta('evento_data_inizio', '', $post_id);
                $end_date = rwmb_meta('evento_data_fine', '', $post_id);
                
                if ($start_date && $end_date) {
                    $start_timestamp = strtotime($start_date);
                    $end_timestamp = strtotime($end_date);
                    
                    // Verifica che i timestamp siano validi
                    if ($start_timestamp === false || $start_timestamp < 0) {
                        $start_timestamp = 0;
                    }
                    if ($end_timestamp === false || $end_timestamp < 0) {
                        $end_timestamp = 0;
                    }
                    
                    // Mostra solo una data se inizio e fine sono uguali
                    if (date('Y-m-d', $start_timestamp) === date('Y-m-d', $end_timestamp)) {
                        $date = format_date_italian($start_timestamp, 'M j, Y');
                    } else {
                        $date = format_date_italian($start_timestamp, 'M j, Y') . ' - ' . format_date_italian($end_timestamp, 'M j, Y');
                    }
                } else {
                    $date = get_the_date('d M Y'); // Fallback alla data di pubblicazione
                }
            } else {
                $date = get_the_date('d M Y');
            }

            // Costruzione del box per ogni articolo
            if ( $atts['slider'] ) {
                $output .= '<div class="swiper-slide swiperArticle-slide pb-3">'; // Elemento per Swiper
            } else {
                $output .= '<div class="col h-100">'; // Colonna della griglia
            }

            // Determina se è un progetto per visualizzazione speciale
            $is_progetto = ($atts['type'] === 'progetto');
            
            // Card con position-relative per progetti (per badge stato)
            $card_classes = 'card mb-4 d-flex flex-column';
            if ($is_progetto) {
                $card_classes .= ' position-relative';
            }
            $output .= '<div class="' . $card_classes . '" style="border-radius: 10px;overflow:hidden;height:100%">';

                // Badge stato progetto in alto a sinistra (solo per progetti)
                if ($is_progetto) {
                    $pricerca_stato = function_exists('rwmb_meta') ? rwmb_meta('pricerca_stato', '', $post_id) : get_post_meta($post_id, 'pricerca_stato', true);
                    if (!empty($pricerca_stato)) {
                        // Se il termine è "attivo", usa bg-primary e text-white, altrimenti bg-warning e text-dark
                        $badge_classes = (strtolower(trim($pricerca_stato)) === 'attivo') ? 'bg-primary text-white' : 'bg-warning text-dark';
                        $output .= '<div class="position-absolute top-0 start-0 m-2" style="z-index: 10;">';
                        $output .= '<span class="badge ' . esc_attr($badge_classes) . ' fw-bold">' . esc_html($pricerca_stato) . '</span>';
                        $output .= '</div>';
                    }
                }

                // 1. Featured image grande in alto
                $output .= '<div class="card-img-top" style="height: 200px; overflow: hidden; display: flex; align-items: center; justify-content: center;">';
                    $output .= '<div style="width: 100%; height: 100%; background-image: url(' . get_the_post_thumbnail_url($post_id, 'full') . '); background-size: cover; background-position: center;"></div>';
                $output .= '</div>';

                // 2. Box grigio con titolo, riassunto e data/periodo
                $card_body_classes = 'card-body bg-light p-4 flex-grow-1 news-card-body';
                if ($atts['text_center']) {
                    $card_body_classes .= ' text-center';
                }
                
                $output .= '<div class="' . $card_body_classes . '">';
                    $output .= '<a href="' . get_permalink() . '" class="text-dark clickable-parent"><h3 class="' . $title_size . '">' . esc_html($title) . '</h3></a>';
                    
                    // Per progetti: mostra riassunto (excerpt) sotto il titolo
                    if ($is_progetto && has_excerpt()) {
                        $output .= '<p class="card-text flex-grow-1 excerpt-4-lines small mt-2">' . wp_strip_all_tags(get_the_excerpt()) . '</p>';
                    }
                    
                    // Per progetti: non mostrare la data standard (mostreremo il periodo nel footer)
                    // Per altri post types: mostra la data solo se hide_date è false
                    if (!$is_progetto && !$atts['hide_date']) {
                        $output .= '<p class="text-muted">' . esc_html($date) . '</p>';
                    }
                $output .= '</div>';

                // 3. Box primario con periodo (progetti) o testo personalizzabile (altri)
                $output .= '<div class="card-footer bg-primary text-white py-3 mt-auto">';
                    
                    if ($is_progetto) {
                        // Per progetti: mostra periodo e link "Scopri di più"
                        $pricerca_periodo = function_exists('rwmb_meta') ? rwmb_meta('pricerca_periodo', '', $post_id) : get_post_meta($post_id, 'pricerca_periodo', true);
                        $output .= '<div class="d-flex gap-2 justify-content-between align-items-center">';
                        if (!empty($pricerca_periodo)) {
                            $output .= '<p class="m-0 small fw-bold">' . esc_html($pricerca_periodo) . '</p>';
                        } else {
                            $output .= '<span class="m-0 small">&nbsp;</span>';
                        }
                        $output .= '<a href="' . get_permalink() . '" class="m-0 small text-white">' . esc_html__('Scopri di più', 'bootscore') . '</a>';
                        $output .= '</div>';
                    } else {
                        // Per altri post types: solo link personalizzabile
                        $output .= '<div class="text-center">';
                        $output .= '<a href="' . get_permalink() . '" class="text-white">' . esc_html($atts['read_more_text']) . '</a>';
                        $output .= '</div>';
                    }
                    
                $output .= '</div>';

            $output .= '</div>';
            if ( $atts['slider'] ) {
                $output .= '</div>'; // Chiudi swiperArticle-slide
            } else {
                $output .= '</div>'; // Chiudi colonna
            }
        }

        if ( $atts['slider'] ) {
            $output .= '</div>'; // Chiudi swiperArticle-wrapper
            $output .= '</div>'; // Chiudi swiperArticle-container
        } else {
            // Chiudi la row solo se non è uno slider (la row è stata aperta solo in modalità non-slider)
            $output .= '</div>'; // Chiudi la riga
        }

        // Aggiungiamo la paginazione solo se non è attivo lo slider
        if ( !$atts['slider'] ) {
            // Costruisci i link come array e imposta current/base/format per URL corretti (pagine statiche e archivi)
            // Costruisci sempre link con parametro dedicato allo shortcode, indipendente dal contesto
            // Usa sempre l'URL corrente completo dal REQUEST_URI per evitare problemi con permalink rewriting
            $current_uri = $_SERVER['REQUEST_URI'];
            
            // Rimuovi il query string dall'URI per ottenere solo il path
            $uri_parts = explode('?', $current_uri);
            $current_path = $uri_parts[0];
            
            // Costruisci l'URL base usando il path corrente (non il permalink per evitare redirect)
            $current_base = home_url($current_path);
            
            // Se siamo su un archivio, usa get_pagenum_link
            if (is_archive() || is_tax() || is_category() || is_tag()) {
                $current_base = get_pagenum_link(1, false);
                // Rimuovi eventuali parametri articoli_page esistenti
                $current_base = remove_query_arg($page_param, $current_base);
            } else {
                // Per single post/page: usa sempre il path corrente e gestisci i query params manualmente
                // Rimuovi tutti i parametri di query esistenti per costruire un URL pulito
                $current_base = remove_query_arg(array($page_param), $current_base);
                
                // Se ci sono altri parametri di query oltre articoli_page, manteniamoli
                $query_params = $_GET;
                if (isset($query_params[$page_param])) {
                    unset($query_params[$page_param]);
                }
                
                // Se ci sono altri parametri, aggiungili di nuovo
                if (!empty($query_params)) {
                    $current_base = add_query_arg($query_params, $current_base);
                }
            }
            
            // Costruisci manualmente i link di paginazione per evitare problemi con permalink rewriting
            $total_pages = max(1, intval($query->max_num_pages));
            $current_page = max(1, intval($paged));
            
            // Costruisci i link manualmente
            $links = array();
            
            // Link Previous
            if ($current_page > 1) {
                $prev_page = $current_page - 1;
                $prev_url = add_query_arg($page_param, $prev_page, $current_base) . '#' . $shortcode_unique_id;
                $links[] = '<a class="prev page-numbers" href="' . esc_url($prev_url) . '">&laquo;</a>';
            }
            
            // Link numerici delle pagine
            $start_page = max(1, $current_page - 1);
            $end_page = min($total_pages, $current_page + 1);
            
            // Prima pagina
            if ($start_page > 1) {
                $first_url = add_query_arg($page_param, 1, $current_base) . '#' . $shortcode_unique_id;
                $links[] = '<a class="page-numbers" href="' . esc_url($first_url) . '">1</a>';
                if ($start_page > 2) {
                    $links[] = '<span class="page-numbers dots">…</span>';
                }
            }
            
            // Pagine centrali
            for ($i = $start_page; $i <= $end_page; $i++) {
                if ($i == $current_page) {
                    $links[] = '<span class="page-numbers current">' . $i . '</span>';
                } else {
                    $page_url = add_query_arg($page_param, $i, $current_base) . '#' . $shortcode_unique_id;
                    $links[] = '<a class="page-numbers" href="' . esc_url($page_url) . '">' . $i . '</a>';
                }
            }
            
            // Ultima pagina
            if ($end_page < $total_pages) {
                if ($end_page < $total_pages - 1) {
                    $links[] = '<span class="page-numbers dots">…</span>';
                }
                $last_url = add_query_arg($page_param, $total_pages, $current_base) . '#' . $shortcode_unique_id;
                $links[] = '<a class="page-numbers" href="' . esc_url($last_url) . '">' . $total_pages . '</a>';
            }
            
            // Link Next
            if ($current_page < $total_pages) {
                $next_page = $current_page + 1;
                $next_url = add_query_arg($page_param, $next_page, $current_base) . '#' . $shortcode_unique_id;
                $links[] = '<a class="next page-numbers" href="' . esc_url($next_url) . '">&raquo;</a>';
            }

            if ($links && is_array($links) && count($links) > 0) {
                $output .= '<nav aria-label="Page navigation"><span class="visually-hidden">Page navigation</span><ul class="pagination justify-content-center mb-4">';

                foreach ($links as $link) {
                    // Link pagina corrente
                    if (strpos($link, 'class="page-numbers current"') !== false) {
                        $page_num = strip_tags($link);
                        $output .= '<li class="page-item active"><span class="page-link"><span class="visually-hidden">Current Page </span>' . esc_html($page_num) . '</span></li>';
                        continue;
                    }

                    // Separatore puntini
                    if (strpos($link, 'class="page-numbers dots"') !== false) {
                        $dots = strip_tags($link);
                        $output .= '<li class="page-item disabled"><span class="page-link">' . esc_html($dots) . '</span></li>';
                        continue;
                    }

                    // Link normale (numero pagina) o prev/next
                    preg_match('/href=\"([^\"]+)\"/', $link, $m);
                    $href = isset($m[1]) ? $m[1] : '';
                    $label = strip_tags($link);

                    // Assicurati che l'href abbia l'anchor (se non ce l'ha già)
                    if (!empty($href) && strpos($href, '#' . $shortcode_unique_id) === false) {
                        // Rimuovi eventuali anchor esistenti
                        $href_parts = explode('#', $href);
                        $href = $href_parts[0] . '#' . $shortcode_unique_id;
                    }

                    // Se è prev/next, mantieni l'etichetta; altrimenti aggiungi "Page"
                    $is_prev = strpos($link, 'class="prev') !== false;
                    $is_next = strpos($link, 'class="next') !== false;

                    if ($is_prev) {
                        $output .= '<li class="page-item"><a class="page-link" href="' . esc_url($href) . '"><span class="visually-hidden">Previous Page </span>' . esc_html($label) . '</a></li>';
                    } elseif ($is_next) {
                        $output .= '<li class="page-item"><a class="page-link" href="' . esc_url($href) . '"><span class="visually-hidden">Next Page </span>' . esc_html($label) . '</a></li>';
                    } else {
                        $output .= '<li class="page-item"><a class="page-link" href="' . esc_url($href) . '"><span class="visually-hidden">Page </span>' . esc_html($label) . '</a></li>';
                    }
                }

                $output .= '</ul></nav>';
            }
        }

    } else {
        $output .= '<p>No posts found.</p>';
    }

    // Ripristiniamo la query originale
    wp_reset_postdata();

    // Script per scroll automatico quando viene caricata la pagina con parametro articoli_page
    if (!empty($_GET[$page_param])) {
        $output .= '
        <script>
        (function() {
            // Aspetta che la pagina sia completamente caricata
            if (document.readyState === "loading") {
                document.addEventListener("DOMContentLoaded", scrollToShortcode);
            } else {
                scrollToShortcode();
            }
            
            function scrollToShortcode() {
                var element = document.getElementById("' . esc_js($shortcode_unique_id) . '");
                if (element) {
                    // Piccolo delay per assicurarsi che tutto sia renderizzato
                    setTimeout(function() {
                        var offset = 100; // Offset per eventuale header fisso
                        var elementPosition = element.getBoundingClientRect().top;
                        var offsetPosition = elementPosition + window.pageYOffset - offset;
                        
                        window.scrollTo({
                            top: offsetPosition,
                            behavior: "smooth"
                        });
                    }, 100);
                }
            }
        })();
        </script>';
    }

    // Chiudiamo il contenitore principale (aperto alla riga 183)
    // IMPORTANTE: Questo div deve essere sempre chiuso per evitare problemi di layout
    $output .= '</div>';

    // Se lo slider è abilitato, aggiungiamo il JS per Swiper
    if ( $atts['slider'] ) {
        if ($user_set_columns) {
            // L'utente ha specificato columns: usa valori dinamici basati su columns
            $slides_640  = min(2, $columns);
            $slides_768  = min(3, $columns);
            $slides_1024 = $columns; // desktop medio
            $slides_1140 = $columns; // desktop ampio

            $output .= '
            <script>
            document.addEventListener("DOMContentLoaded", function () {
                var swiper = new Swiper(".swiper-article-container", {
                    slidesPerView: 1,
                    spaceBetween: 10,
                    navigation: {
                        nextEl: ".swiper-button-next", // Pulsante avanti
                        prevEl: ".swiper-button-prev"  // Pulsante indietro
                    },
                    pagination: {
                        el: ".swiper-pagination",
                        clickable: true
                    },
                    breakpoints: {
                        640: {
                            slidesPerView: ' . (int) $slides_640 . ',
                            spaceBetween: 20
                        },
                        768: {
                            slidesPerView: ' . (int) $slides_768 . ',
                            spaceBetween: 20
                        },
                        1024: {
                            slidesPerView: ' . (int) $slides_1024 . ',
                            spaceBetween: 20
                        },
                        1140: {
                            slidesPerView: ' . (int) $slides_1140 . ',
                            spaceBetween: 20
                        }
                    }
                });
            });
            </script>
            ';
        } else {
            // Default originale: 4 su desktop largo, 3 su 1024+, 3 su 768, 2 su 640
            $output .= '
            <script>
            document.addEventListener("DOMContentLoaded", function () {
                var swiper = new Swiper(".swiper-article-container", {
                    slidesPerView: 1,
                    spaceBetween: 10,
                    navigation: {
                        nextEl: ".swiper-button-next",
                        prevEl: ".swiper-button-prev"
                    },
                    pagination: {
                        el: ".swiper-pagination",
                        clickable: true
                    },
                    breakpoints: {
                        640: {
                            slidesPerView: 2,
                            spaceBetween: 20
                        },
                        768: {
                            slidesPerView: 2,
                            spaceBetween: 20
                        },
                        1200: {
                            slidesPerView: 3,
                            spaceBetween: 20
                        },
                        1400: {
                            slidesPerView: 4,
                            spaceBetween: 20
                        }
                    }
                });
            });
            </script>
            ';
        }
    }

    return $output;
}
add_shortcode('articoli', 'articoli_shortcode');

// Esempi di utilizzo:
// [articoli posts_per_page="10" orderby="title" order="ASC"] 
// [articoli] 
// [articoli slider="true" posts="6"] 
// [articoli posts="8" type="eventi"]
// [articoli type="avviso" tax="cat-dipartimenti" cat="comunicazioni"]
// [articoli type="eventi" slider="true" posts="6"]
// [articoli slider="true" id="1952,1950,1945,1957,1947,1943" posts="6"] - Mostra articoli specifici per ID
// [articoli type="eventi" posts="5" tax="cat-eventi" cat="conferenze"]
// [articoli read_more_text="Scopri di più"] - Personalizza il testo del link
// [articoli type="eventi" read_more_text="Partecipa all'evento"] - Testo personalizzato per eventi
// [articoli columns="6"] - Griglia a 6 colonne su desktop
// [articoli columns="5"] - Griglia a 5 colonne su desktop (custom CSS)
// [articoli columns="4" posts="8"] - Griglia a 4 colonne con 8 articoli
// [articoli columns="2" type="eventi"] - Griglia a 2 colonne per eventi
// [articoli columns="1" posts="3"] - Griglia a 1 colonna (lista verticale)
// [articoli hide_date="true"] - Nasconde la data
// [articoli text_center="true"] - Centra il testo
// [articoli hide_date="true" text_center="true"] - Nasconde data e centra testo
// [articoli title_size="fs-3"] - Titolo più grande (fs-1, fs-2, fs-3, fs-4, fs-5, fs-6)
// [articoli title_size="fs-6"] - Titolo più piccolo
// [articoli type="eventi" hide_date="true" text_center="true" columns="4" title_size="fs-4"] - Combinazione completa
// [articoli type="progetto" stato="attivo"] - Filtra solo progetti con stato "attivo" (richiede campo pricerca_stato)
// [articoli type="progetto" stato="concluso"] - Filtra solo progetti con stato "concluso" (richiede campo pricerca_stato)
// [articoli type="progetto" stato="attivo" posts="6" columns="3"] - Progetti attivi con 6 post in 3 colonne
// [articoli type="progetto" stato="attivo" tax="cat-progetto" cat="ricerca"] - Progetti attivi di una specifica categoria
// [articoli type="progetto" dipartimento="nome-dipartimento"] - Filtra progetti per dipartimento/struttura di gestione
// [articoli type="progetto" stato="attivo" dipartimento="nome-dipartimento"] - Progetti attivi di un dipartimento specifico
// [articoli type="progetto" stato="attivo" dipartimento="nome-dipartimento" posts="6"] - Combinazione completa con filtri
// [articoli type="progetto" gestione="ateneo"] - Filtra progetti per gestione "ateneo" (richiede campo pricerca_gestione)
// [articoli type="progetto" gestione="economico"] - Filtra progetti per gestione "economico"
// [articoli type="progetto" gestione="ingegneria"] - Filtra progetti per gestione "ingegneria"
// [articoli type="progetto" gestione="umane"] - Filtra progetti per gestione "umane"
// [articoli type="progetto" stato="attivo" gestione="ateneo"] - Progetti attivi gestiti dall'ateneo
// [articoli type="progetto" stato="attivo" gestione="economico" posts="6"] - Combinazione stato + gestione
// [articoli type="progetto" stato="attivo" gestione="ateneo" dipartimento="nome-dipartimento"] - Combinazione completa con tutti i filtri

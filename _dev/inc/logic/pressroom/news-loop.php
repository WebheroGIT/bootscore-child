<?php
// Assicurati che il file non sia accessibile direttamente
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function articoli_shortcode( $atts ) {
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
        'posts_per_page' => $num_posts, // Numero di articoli
        'orderby' => $atts['orderby'], // Ordinamento
        'order' => $atts['order'], // Più recente in cima
    );
    
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

    // Se sono specificati degli ID, li aggiungiamo alla query
    if ( !empty($atts['id']) ) {
        $post_ids = explode(',', $atts['id']);
        $post_ids = array_map('trim', $post_ids); // Rimuoviamo eventuali spazi
        $post_ids = array_map('intval', $post_ids); // Convertiamo in interi
        $args['post__in'] = $post_ids;
        $args['orderby'] = 'post__in'; // Manteniamo l'ordine specificato negli ID
    }

    $query = new WP_Query($args);

    // Iniziamo il contenitore
    $output = '<div class="mt-5 w-100">';
    
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
        $output .= '<div class="row ' . $column_classes[$columns] . ' g-4 d-flex h-100">'; // Griglia con colonne dinamiche
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

            $output .= '<div class="card mb-4 d-flex flex-column" style="border-radius: 10px;overflow:hidden;height:100%">';

                // 1. Featured image grande in alto
                $output .= '<div class="card-img-top" style="height: 200px; overflow: hidden; display: flex; align-items: center; justify-content: center;">';
                    $output .= '<div style="width: 100%; height: 100%; background-image: url(' . get_the_post_thumbnail_url($post_id, 'full') . '); background-size: cover; background-position: center;"></div>';
                $output .= '</div>';

                // 2. Box grigio con titolo e data
                $card_body_classes = 'card-body bg-light p-4 flex-grow-1 news-card-body';
                if ($atts['text_center']) {
                    $card_body_classes .= ' text-center';
                }
                
                $output .= '<div class="' . $card_body_classes . '">';
                    $output .= '<a href="' . get_permalink() . '" class="text-dark clickable-parent"><h3 class="' . $title_size . '">' . esc_html($title) . '</h3></a>';
                    
                    // Mostra la data solo se hide_date è false
                    if (!$atts['hide_date']) {
                        $output .= '<p class="text-muted">' . esc_html($date) . '</p>';
                    }
                $output .= '</div>';

                // 3. Box primario con testo personalizzabile
                $output .= '<div class="card-footer bg-primary text-white text-center py-3 mt-auto">';
                    $output .= '<a href="' . get_permalink() . '" class="text-white">' . esc_html($atts['read_more_text']) . '</a>';
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
        }

        $output .= '</div>'; // Chiudi la riga

        // Aggiungiamo la paginazione solo se non è attivo lo slider
        if ( !$atts['slider'] ) {
            $output .= '<div class="pagination mt-4">';
                $output .= paginate_links(array(
                    'total' => $query->max_num_pages
                ));
            $output .= '</div>';
        }

    } else {
        $output .= '<p>No posts found.</p>';
    }

    // Ripristiniamo la query originale
    wp_reset_postdata();

    // Chiudiamo il contenitore
    // $output .= '</div>';   tolto questo div perche era di troppo da slide e chiudeva male TODO controllare bene nella griglia news

    // Se lo slider è abilitato, aggiungiamo il JS per Swiper
    if ( $atts['slider'] ) {
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
                        slidesPerView: 2,
                        spaceBetween: 20
                    },
                    768: {
                        slidesPerView: 3,
                        spaceBetween: 20
                    },
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 20
                    },
                    1140: {
                        slidesPerView: 4,
                        spaceBetween: 20
                    }
                }
            });
        });
        </script>
        ';
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

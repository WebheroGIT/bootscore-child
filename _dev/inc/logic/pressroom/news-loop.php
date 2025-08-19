<?php
// Assicurati che il file non sia accessibile direttamente
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function articoli_shortcode( $atts ) {
    // Definiamo gli attributi di default
    $atts = shortcode_atts(
        array(
            'posts_per_page' => 6, // Numero di articoli per pagina di default
            'orderby' => 'date', // Ordinamento di default per data
            'order' => 'DESC', // Ordinamento decrescente di default
            'slider' => false, // Aggiungiamo un attributo per controllare se vogliamo lo slider (default false)
        ),
        $atts,
        'articoli' // Nome dello shortcode
    );

    // Impostiamo la query per ottenere gli articoli con gli attributi passati
    $args = array(
        'post_type' => 'post', // Tipo di post 'post'
        'posts_per_page' => $atts['posts_per_page'], // Numero di articoli per pagina
        'orderby' => $atts['orderby'], // Ordinamento
        'order' => $atts['order'], // Più recente in cima
    );

    $query = new WP_Query($args);

    // Iniziamo il contenitore
    $output = '<div class="mt-5 w-100">';

    // Se lo slider è abilitato, iniziamo il contenitore Swiper
    if ( $atts['slider'] ) {
        $output .= '<div class="swiper swiper-article-container swiper-container" id="swiper-article-' . uniqid() . '">';
        $output .= '<div class="swiper-button-next"></div>';
        $output .= '<div class="swiper-button-prev"></div>';
        $output .= '<div class="swiper-pagination"></div>';
        $output .= '<div class="swiper-wrapper swiperArticle-wrapper">';
    } else {
        $output .= '<div class="row row-cols-1 row-cols-md-3 g-4 d-flex">'; // Impostiamo una griglia con 3 colonne sui dispositivi desktop
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
            $date = get_the_date('d M Y');

            // Costruzione del box per ogni articolo
            if ( $atts['slider'] ) {
                $output .= '<div class="swiper-slide swiperArticle-slide">'; // Elemento per Swiper
            } else {
                $output .= '<div class="col">'; // Colonna della griglia
            }

            $output .= '<div class="card mb-4 d-flex flex-column" style="border-radius: 10px;overflow:hidden;height:100%">';

                // 1. Featured image grande in alto
                $output .= '<div class="card-img-top">' . $featured_image . '</div>';

                // 2. Box grigio con titolo e data
                $output .= '<div class="card-body bg-light p-4">';
                    $output .= '<a href="' . get_permalink() . '" class="text-dark clickable-parent"><h3 class="fs-5">' . esc_html($title) . '</h3></a>';
                    $output .= '<p class="text-muted">' . esc_html($date) . '</p>';
                $output .= '</div>';

                // 3. Box primario con "Continua a leggere"
                $output .= '<div class="card-footer bg-primary text-white text-center py-3">';
                    $output .= '<a href="' . get_permalink() . '" class="text-white">Continua a leggere</a>';
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

        // Aggiungiamo la paginazione
        $output .= '<div class="pagination mt-4">';
            $output .= paginate_links(array(
                'total' => $query->max_num_pages
            ));
        $output .= '</div>';

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
                        spaceBetween: 30
                    },
                    1024: {
                        slidesPerView: 4,
                        spaceBetween: 40
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

// [articoli posts_per_page="10" orderby="title" order="ASC"] [articoli] [articoli slider="true" posts_per_page="6"] [articoli posts_per_page="6"]

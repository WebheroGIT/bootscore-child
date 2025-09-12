<?php

// Assicurati che il file non sia accessibile direttamente
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Banner Evidenza Settings
add_filter('mb_settings_pages', function($settings_pages) {
    $settings_pages[] = [
        'id'          => 'banner-evidenza-settings',
        'menu_title'  => 'Banner Evidenza',
        'option_name' => 'banner_evidenza_options',
        'icon_url'    => 'dashicons-format-gallery',
        'position'    => 81,
    ];
    return $settings_pages;
});

add_filter('rwmb_meta_boxes', function($meta_boxes) {
    $meta_boxes[] = [
        'id'             => 'banner_evidenza_fields',
        'title'          => 'Impostazioni Banner Evidenza',
        'settings_pages' => ['banner-evidenza-settings'],
        'fields'         => [
            [
                'id'         => 'evidenza_slides',
                'type'       => 'group',
                'clone'      => true,
                'sort_clone' => true,
                'fields'     => [
                    [
                        'name' => 'Titolo',
                        'id'   => 'title',
                        'type' => 'text',
                        'required' => true,
                    ],
                    [
                        'name' => 'Media (Immagine)',
                        'id'   => 'media',
                        'type' => 'image_advanced',
                        'max_file_uploads' => 1,
                        'desc' => 'Carica un\'immagine per la card',
                        'required' => true,
                    ],
                    [
                        'name' => 'Testo descrittivo',
                        'id'   => 'text',
                        'type' => 'textarea',
                        'desc' => 'Testo che apparirà nella card',
                        'required' => true,
                    ],
                    [
                        'name' => 'Link "Leggi tutto"',
                        'id'   => 'read_more_link',
                        'type' => 'url',
                        'desc' => 'URL completo per il link "Leggi tutto" (es. https://...)',
                        'required' => true,
                    ],
                    [
                        'name' => 'Slide attiva',
                        'id'   => 'active',
                        'type' => 'checkbox',
                        'std'  => 1,
                    ],
                ],
            ],
        ],
    ];
    return $meta_boxes;
});

// Funzione per mostrare il banner evidenza
function show_banner_evidenza($atts = []) {
    // Attributi di default
    $atts = shortcode_atts([
        'slider' => true,
        'id' => 'banner-evidenza-swiper',
        'class' => 'banner-evidenza-container'
    ], $atts);

    $options = get_option('banner_evidenza_options');
    $slides = $options['evidenza_slides'] ?? [];

    if (empty($slides)) {
        return '';
    }

    // Filtra solo le slide attive
    $active_slides = array_filter($slides, function($slide) {
        return !empty($slide['active']);
    });

    if (empty($active_slides)) {
        return '';
    }

    ob_start();
    ?>
    <div class="<?php echo esc_attr($atts['class']); ?> px-5 position-relative">
        <?php if ($atts['slider']) : ?>
            <div class="cards swiper-container swiper position-static <?php echo esc_attr($atts['id']); ?> swiper-initialized swiper-horizontal swiper-backface-hidden">
                <div class="swiper-wrapper">
        <?php else : ?>
            <div class="row">
        <?php endif; ?>

        <?php foreach ($active_slides as $index => $slide) : 
            $media_id = $slide['media'][0] ?? null;
            $media_url = $media_id ? wp_get_attachment_image_url($media_id, 'large') : '';
            $media_alt = $media_id ? get_post_meta($media_id, '_wp_attachment_image_alt', true) : '';
            ?>
            
            <?php if ($atts['slider']) : ?>
                <div class="swiper-slide card h-auto mb-5" data-swiper-slide-index="<?php echo $index; ?>">
            <?php else : ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-auto">
            <?php endif; ?>

                <?php if ($media_url) : ?>
                    <a href="<?php echo esc_url($slide['read_more_link']); ?>">
                        <img loading="lazy" decoding="async" 
                             src="<?php echo esc_url($media_url); ?>" 
                             class="card-img-top wp-post-image" 
                             alt="<?php echo esc_attr($media_alt ?: $slide['title']); ?>">
                    </a>
                <?php endif; ?>

                <div class="card-body d-flex flex-column">
                    <?php if (!empty($slide['title'])) : ?>
                        <a class="text-body text-decoration-none" href="<?php echo esc_url($slide['read_more_link']); ?>">
                            <h2 class="blog-post-title h5"><?php echo esc_html($slide['title']); ?></h2>
                        </a>
                    <?php endif; ?>

                    <?php if (!empty($slide['text'])) : ?>
                        <p class="card-text">
                            <a class="text-body text-decoration-none" href="<?php echo esc_url($slide['read_more_link']); ?>">
                                <?php echo wp_kses_post($slide['text']); ?>
                            </a>
                        </p>
                    <?php endif; ?>

                    <p class="card-text mt-auto">
                        <a class="read-more" href="<?php echo esc_url($slide['read_more_link']); ?>">
                            Leggi tutto »
                        </a>
                    </p>
                </div>

            <?php if ($atts['slider']) : ?>
                </div><!-- .swiper-slide -->
            <?php else : ?>
                    </div><!-- .card -->
                </div><!-- .col -->
            <?php endif; ?>

        <?php endforeach; ?>

        <?php if ($atts['slider']) : ?>
                </div><!-- .swiper-wrapper -->
                
                <!-- Navigation buttons -->
                <div class="swiper-button-next end-0" tabindex="0" role="button" aria-label="Next slide"></div>
                <div class="swiper-button-prev start-0" tabindex="0" role="button" aria-label="Previous slide"></div>
                
                <!-- Pagination -->
                <div class="swiper-pagination swiper-pagination-clickable swiper-pagination-bullets swiper-pagination-horizontal"></div>
            </div><!-- .swiper-container -->
        <?php else : ?>
            </div><!-- .row -->
        <?php endif; ?>
    </div><!-- .banner-evidenza-container -->

    <?php
    // Se lo slider è abilitato, aggiungiamo il JS per Swiper
    if ( $atts['slider'] ) {
        $output = ob_get_clean();
        $output .= '
<script>
(function() {
    // Inizializza Swiper per Banner Evidenza
    if (typeof Swiper !== "undefined") {
        const bannerEvidenzaSwiper = new Swiper(".'. $atts['id'] .'", {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: ".'. $atts['id'] .' .swiper-button-next",
                prevEl: ".'. $atts['id'] .' .swiper-button-prev",
            },
            pagination: {
                el: ".'. $atts['id'] .' .swiper-pagination",
                clickable: true,
            },
            breakpoints: {
                640: {
                    slidesPerView: 1,
                    spaceBetween: 20,
                },
                768: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 20,
                },
            },
        });
    } else {
        console.warn("Swiper non è caricato. Assicurati che la libreria Swiper sia inclusa.");
    }
})();
</script>';
        return $output;
    }
    
    return ob_get_clean();
}

// Shortcode per il banner evidenza
add_shortcode('banner_evidenza', function($atts) {
    return show_banner_evidenza($atts);
});

// Funzione helper per chiamare il banner evidenza nei template
function display_banner_evidenza($atts = []) {
    echo show_banner_evidenza($atts);
}
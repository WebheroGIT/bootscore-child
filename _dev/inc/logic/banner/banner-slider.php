<?php

// Assicurati che il file non sia accessibile direttamente
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// TODO 2025 03 29 SLIDER SETTINGS
add_filter('mb_settings_pages', function($settings_pages) {
    $settings_pages[] = [
        'id'          => 'slider-settings',
        'menu_title'  => 'Slider Banner',
        'option_name' => 'slider_options',
        'icon_url'    => 'dashicons-slides',
        'position'    => 80,
    ];
    return $settings_pages;
});

add_filter('rwmb_meta_boxes', function($meta_boxes) {
    $meta_boxes[] = [
        'id'             => 'slider_fields',
        'title'          => 'Impostazioni Slider Hero',
        'settings_pages' => ['slider-settings'],
        'fields'         => [
            [
                'id'         => 'slides',
                'type'       => 'group',
                'clone'      => true,
                'sort_clone' => true,
                'fields'     => [
                    [
                        'name' => 'Titolo',
                        'id'   => 'title',
                        'type' => 'text',
                    ],
                    [
                        'name' => 'Sottotitolo',
                        'id'   => 'subtitle',
                        'type' => 'text',
                    ],
                    [
                        'name' => 'Testo sotto linea',
                        'id'   => 'text',
                        'type' => 'textarea',
                    ],
                    [
                        'name' => 'Link pulsante',
                        'id'   => 'button_link',
                        'type' => 'url',
                        'desc' => 'Inserisci URL completo (es. https://...)',
                    ],
                    [
                        'name' => 'Testo pulsante',
                        'id'   => 'button_text',
                        'type' => 'text',
                        'std'  => 'Scopri di più',
                    ],
                    [
                        'name' => 'Link secondo pulsante',
                        'id'   => 'button_link_2',
                        'type' => 'url',
                        'desc' => 'Inserisci URL completo per il secondo pulsante (es. https://...)',
                    ],
                    [
                        'name' => 'Testo secondo pulsante',
                        'id'   => 'button_text_2',
                        'type' => 'text',
                        'desc' => 'Se vuoto, il secondo pulsante non verrà mostrato',
                    ],
                    [
                        'name' => 'Media di sfondo (Immagine o Video)',
                        'id'   => 'media',
                        'type' => 'file_advanced',
                        'max_file_uploads' => 1,
                        'mime_type' => 'image,video',
                        'desc' => 'Carica un\'immagine (JPG, PNG, WebP) o un video (MP4, WebM)',
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

// END slider

// start stamp slider

function show_custom_hero_slider() {
	// if (!is_front_page()) return;

	$options = get_option('slider_options');
	$slides = $options['slides'] ?? [];

	if (empty($slides)) return;

	// Conta le slide attive
	$active_slides_count = 0;
	foreach ($slides as $slide) {
		if (!empty($slide['active'])) {
			$active_slides_count++;
		}
	}

	// Se non ci sono slide attive, non mostrare nulla
	if ($active_slides_count === 0) return;

	?>
	<div class="hero-banner position-relative">
		<div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
			<div class="carousel-inner">
				<?php
				$active = true;
				$slide_index = 0;
				foreach ($slides as $slide) :
					if (empty($slide['active'])) continue;

					$media_id = $slide['media'][0] ?? null;
					$media_url = $media_id ? wp_get_attachment_url($media_id) : '';
					$media_type = $media_id ? get_post_mime_type($media_id) : '';
					$is_video = strpos($media_type, 'video/') === 0;
					$is_first_slide = ($slide_index === 0);
					?>
					<div class="carousel-item <?php echo $active ? 'active' : ''; ?>">
						<div class="position-relative w-100" style="height: calc(100vh - 128px);">
							<?php if ($is_video) : ?>
								<!-- Video di sfondo -->
								<video class="d-block w-100 h-100 object-fit-cover position-absolute top-0 start-0" autoplay muted loop playsinline>
									<source src="<?php echo esc_url($media_url); ?>" type="<?php echo esc_attr($media_type); ?>">
									Il tuo browser non supporta il tag video.
								</video>
							<?php else : ?>
								<!-- Immagine di sfondo -->
								<img src="<?php echo esc_url($media_url); ?>" class="d-block w-100 h-100 object-fit-cover position-absolute top-0 start-0" alt="Hero Slide" />
							<?php endif; ?>

							<!-- Overlay -->
							<div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50"></div>

							<!-- Contenuto allineato a sinistra -->
							<div class="position-absolute w-100 h-100 d-flex flex-column justify-content-between text-white" style="padding-left: 120px; padding-right: 120px;">
								<style>
									@media (max-width: 768px) {
										.carousel-item > div > div {
											padding-left: 60px !important;
											padding-right: 60px !important;
										}
									}
								</style>
								<!-- Titolo e sottotitolo nella parte alta (30% dall'alto) -->
								<div class="d-flex align-items-start" style="padding-top: 30vh;">
									<div class="text-start" style="max-width: 980px;">
										<?php if (!empty($slide['title'])) : ?>
											<?php if ($is_first_slide) : ?>
												<h1 class="display-6 fw-bold mb-3"><?php echo esc_html($slide['title']); ?></h1>
											<?php else : ?>
												<h2 class="display-6 fw-bold mb-3"><?php echo esc_html($slide['title']); ?></h2>
											<?php endif; ?>
										<?php endif; ?>

										<?php if (!empty($slide['subtitle'])) : ?>
											<p class="lead mb-3"><?php echo esc_html($slide['subtitle']); ?></p>
										<?php endif; ?>

										<?php if (!empty($slide['text'])) : ?>
											<div class="border-top border-white pt-3 mt-3">
												<p class="mb-0"><?php echo wp_kses_post($slide['text']); ?></p>
											</div>
										<?php endif; ?>
									</div>
								</div>

								<!-- Pulsanti nella parte bassa -->
								<div class="pb-5 mb-4">
									<div class="d-flex gap-3 flex-wrap">
										<?php if (!empty($slide['button_link']) && !empty($slide['button_text'])) : ?>
											<a href="<?php echo esc_url($slide['button_link']); ?>" class="btn btn-primary px-4 py-2">
												<?php echo esc_html($slide['button_text']); ?>
											</a>
										<?php endif; ?>

										<?php if (!empty($slide['button_link_2']) && !empty($slide['button_text_2'])) : ?>
											<a href="<?php echo esc_url($slide['button_link_2']); ?>" class="btn btn-secondary px-4 py-2">
												<?php echo esc_html($slide['button_text_2']); ?>
											</a>
										<?php endif; ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php
				$active = false;
				$slide_index++;
				endforeach;
				?>
			</div>

			<?php if ($active_slides_count > 1) : ?>
				<!-- Frecce navigazione -->
				<button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
					<span class="carousel-control-prev-icon" aria-hidden="true"></span>
					<span class="visually-hidden">Precedente</span>
				</button>
				<button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
					<span class="carousel-control-next-icon" aria-hidden="true"></span>
					<span class="visually-hidden">Successivo</span>
				</button>

				<!-- Indicatori -->
				<div class="carousel-indicators">
					<?php
					$index = 0;
					foreach ($slides as $slide) :
						if (empty($slide['active'])) continue;
						?>
						<button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?php echo $index; ?>" class="<?php echo $index === 0 ? 'active' : ''; ?>" aria-current="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-label="Slide <?php echo $index + 1; ?>"></button>
						<?php
						$index++;
					endforeach;
					?>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<?php
}


// shortcode slider banner
// Crea lo shortcode [hero_slider]
add_shortcode('hero_slider', function () {
	ob_start();
	show_custom_hero_slider();
	return ob_get_clean();
});

<?php

// Assicurati che il file non sia accessibile direttamente
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Shortcode per visualizzare le informazioni del post type formazione
 * Utilizzo: [formazione_info]
 */

function formazione_info_shortcode($atts) {
    // Verifica che siamo nel post type corretto
    if (get_post_type() !== 'formazione') {
        return '';
    }
    
    ob_start();
    ?>
    
    <div class="formazione-info-wrapper">
        
        <?php
        // Sezione Brochure
        $files = rwmb_meta('corso_brochure');
        if (!empty($files)) {
            $file = reset($files);
            ?>
            <div class="brochure-section mb-4">
                <a class="btn btn-secondary" href="<?php echo esc_url($file['url']); ?>" download>
                    <i class="fas fa-download"></i> Scarica la brochure
                </a>
            </div>
            <?php
        }
        ?>
        
        <?php
        $current_post_id = get_the_ID();
        
        // Query per Piani di Studio (piano -> formazione)
        $piani_query = new WP_Query([
            'post_type' => 'piano',
            'relationship' => [
                'id' => 'rel-pian-form',
                'to' => $current_post_id, // formazione è il destinatario
            ],
            'nopaging' => true,
            'posts_per_page' => -1,
        ]);
        
        // Query per Tirocini (tirocinio -> formazione)
        $tirocini_query = new WP_Query([
            'post_type' => 'tirocinio',
            'relationship' => [
                'id' => 'rel-tir-form',
                'to' => $current_post_id, // formazione è il destinatario
            ],
            'nopaging' => true,
            'posts_per_page' => -1,
        ]);
        
        // Mostra le colonne solo se ci sono risultati
        if ($piani_query->have_posts() || $tirocini_query->have_posts()) {
            ?>
            <div class="row mb-5">
                <?php if ($piani_query->have_posts()) : ?>
                <div class="col-md-6">
                    <h3>Piani di Studi</h3>
                    <ul class="list-unstyled">
                        <?php while ($piani_query->have_posts()) : $piani_query->the_post(); ?>
                            <li class="mb-3">
                                <a href="<?php the_permalink(); ?>" class="text-primary text-decoration-underline">
                                    <?php the_title(); ?>
                                </a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <?php if ($tirocini_query->have_posts()) : ?>
                <div class="col-md-6">
                    <h3>Tirocini</h3>
                    <ul class="list-unstyled">
                        <?php while ($tirocini_query->have_posts()) : $tirocini_query->the_post(); ?>
                            <li class="mb-3">
                                <a href="<?php the_permalink(); ?>" class="text-primary text-decoration-underline">
                                    <?php the_title(); ?>
                                </a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
            <?php
        }
        
        // Reset post data
        wp_reset_postdata();
        ?>
        
        <!-- Quattro box informativi -->
        <div class="row info-boxes">
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="info-box bg-white shadow p-4 text-center h-100">
                    <i class="fas fa-file-alt fa-3x text-primary mb-3"></i>
                    <h4><a href="#" class="text-decoration-none text-dark">Iscriversi</a></h4>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="info-box bg-white shadow p-4 text-center h-100">
                    <i class="fas fa-book fa-3x text-primary mb-3"></i>
                    <h4><a href="#" class="text-decoration-none text-dark">Studiare</a></h4>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="info-box bg-white shadow p-4 text-center h-100">
                    <i class="fas fa-users fa-3x text-primary mb-3"></i>
                    <h4><a href="#" class="text-decoration-none text-dark">Servizi</a></h4>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="info-box bg-white shadow p-4 text-center h-100">
                    <i class="fas fa-graduation-cap fa-3x text-primary mb-3"></i>
                    <h4><a href="#" class="text-decoration-none text-dark">Laurearsi</a></h4>
                </div>
            </div>
        </div>
        
    </div>
    
    <style>
    .formazione-info-wrapper {
        margin: 2rem 0;
    }
    
    .info-box {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 8px;
    }
    
    .info-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    
    .info-box i {
        transition: color 0.3s ease;
    }
    
    .info-box:hover i {
        color: #6c757d !important;
    }
    
    </style>
    
    <?php
    return ob_get_clean();
}

add_shortcode('formazione_info', 'formazione_info_shortcode');
<?php

// Assicurati che il file non sia accessibile direttamente
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Shortcode per visualizzare le informazioni del post type formazione
 * Utilizzo: [formazione_info menu_id="your_menu_id"]
 */

function formazione_info_shortcode($atts) {
    // Verifica che siamo nel post type corretto
    if (get_post_type() !== 'formazione') {
        return '';
    }
    
    // Ottieni i parametri passati nello shortcode
    $atts = shortcode_atts( array(
        'menu_id' => '16', // ID del menu di WordPress
    ), $atts );

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

        <!-- Menu dinamico come box -->
        <div class="row">
            <div class="col-md-12 mb-4">
                
                    <!-- Stampa il menu (primo livello come box) -->
                    <?php
                    // Controlla che l'ID del menu sia fornito
                    if ($atts['menu_id']) :
                        wp_nav_menu(array(
                            'menu' => $atts['menu_id'],  // ID del menu
                            'menu_class' => 'submenu-class menu-formativa',
                            'container' => false,
                            'depth' => 2, // Mostra sia il primo che il secondo livello
                            'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',  // Wrap per il menu
                            'walker' => new Walker_Nav_Menu_Custom()  // Usa il nostro Walker personalizzato
                        ));
                    endif;
                    ?>
            
            </div>
        </div>
    </div>

    <script>


jQuery(document).ready(function($) {
    
    // Quando si clicca su un link del primo livello
    $('#menu-menuformativa .menu-item-level-1 > a').on('click', function(e) {
        e.preventDefault();
        
        var target = $(this).data('target');
        var submenu = $(target);
        
        // Chiudi tutti gli altri sottomenu aperti
        $('#menu-menuformativa .collapse').removeClass('show').hide();
        
        // Apri il sottomenu selezionato
        if (submenu.length) {
            // Aggiungi il pulsante di chiusura se non esiste
            if (submenu.find('.sub-menu-close').length === 0) {
                submenu.find('.sub-menu').prepend('<button class="sub-menu-close" aria-label="Chiudi menu">×</button>');
            }
            
            submenu.addClass('show').fadeIn(200);
            
            // Previeni il scroll del body quando l'overlay è aperto
            $('body').addClass('overflow-hidden');
        }
    });
    
    // Chiusura del sottomenu cliccando sulla X
    $(document).on('click', '#menu-menuformativa .sub-menu-close', function(e) {
        e.stopPropagation();
        closeSubmenu();
    });
    
    // Chiusura del sottomenu cliccando sull'overlay (fuori dal box)
    $('#menu-menuformativa .collapse').on('click', function(e) {
        if (e.target === this) {
            closeSubmenu();
        }
    });
    
    // Chiusura con il tasto ESC
    $(document).on('keydown', function(e) {
        if (e.keyCode === 27) { // ESC key
            closeSubmenu();
        }
    });
    
    // Funzione per chiudere il sottomenu
    function closeSubmenu() {
        $('#menu-menuformativa .collapse.show').removeClass('show').fadeOut(200);
        $('body').removeClass('overflow-hidden');
    }
    
    // Gestione dei link nel sottomenu (opzionale: chiudi dopo aver cliccato un link)
    $('#menu-menuformativa .sub-menu a').on('click', function() {
        // Se vuoi che il sottomenu si chiuda dopo aver cliccato un link, decommenta la riga sotto
        // closeSubmenu();
    });
    
});




    </script>

    <style>
/* Reset e base per il menu */
#menu-menuformativa {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    flex-wrap: wrap;
    gap: 20px;
}
#menu-menuformativa li {
    position: relative;
}

#menu-menuformativa li a, #menu-menuformativa .sub-menu {
    font-family: var(--bs-body-font-family)!important;
}

/* Box del primo livello - stile Bootstrap */
#menu-menuformativa .menu-item-level-1 {
    position: relative;
    width: 100%;
}
@media (max-width: 991px) {
    #menu-menuformativa {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    #menu-menuformativa .menu-item-level-1 {
        flex: 0 0 calc(50% - 10px); /* 2 colonne su tablet */
    }
}

@media (max-width: 576px) {
    #menu-menuformativa .menu-item-level-1 {
        flex: 0 0 100%; /* 1 colonna su mobile */
    }
}

/* Link del primo livello - Box Bootstrap style */
#menu-menuformativa .menu-item-level-1 > a {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    text-decoration: none;
    color: #495057;
}



#menu-menuformativa .info-box {
   transition: all 0.15s ease-in-out;
   
}
/* Hover effect per i box */
#menu-menuformativa .info-box:hover {
    box-shadow: 0 0.3rem 0.7rem rgba(0, 0, 0, 0.08)!important;
    
}

/* Icona del primo livello */
#menu-menuformativa .menu-item-level-1 > a i {
    font-size: 3rem !important;
    color: #007bff !important;
    margin-bottom: 1rem !important;
}

/* Testo del primo livello */
#menu-menuformativa .menu-item-level-1 > a span {
    font-size: 1.1rem;
    font-weight: 500;
    line-height: 1.4;
}

/* Overlay per il sottomenu */
#menu-menuformativa .collapse {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1050;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

/* Quando il sottomenu è visibile */
#menu-menuformativa .collapse.show {
    display: flex !important;
}

/* Container del sottomenu */
#menu-menuformativa .sub-menu {
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    padding: 20px;
    max-width: 600px;
    width: 100%;
    max-height: 80vh;
    overflow-y: auto;
    list-style: none;
    margin: 0;
    position: relative;
    text-align: left;
}

/* Link del sottomenu */
#menu-menuformativa .sub-menu li {
    margin: 0;
    padding: 0;
    border-bottom: 1px solid #e2e2e2;
}

#menu-menuformativa .sub-menu li:last-child {
    border-bottom: none;
}

#menu-menuformativa .sub-menu li a {
    display: block;
    padding: 15px 10px;
    color: #495057;
    text-decoration: none;
    transition: background-color 0.15s ease-in-out;
    font-size: 1rem;
    line-height: 1.5;
    font-weight: 500;
}

#menu-menuformativa .sub-menu li a:hover {
    background-color:rgb(173, 173, 173);
    text-decoration: none;
    background-color: #379975;
    color: #fff;
}

/* Pulsante di chiusura per il sottomenu */
#menu-menuformativa .sub-menu-close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 24px;
    color: #6c757d;
    cursor: pointer;
    z-index: 1051;
    line-height: 1;
    padding: 5px;
    border-radius: 50%;
    transition: all 0.15s ease-in-out;
    background: none;
    border: none;
    width: 34px;
    height: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
}

#menu-menuformativa .sub-menu-close:hover {
    background-color: #f8f9fa;
    color: #495057;
}

/* Animazione di apertura */
@keyframes fadeInScale {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

#menu-menuformativa .collapse.show .sub-menu {
    animation: fadeInScale 0.2s ease-out;
}

/* Responsive per il sottomenu */
@media (max-width: 768px) {
    #menu-menuformativa .sub-menu {
        max-width: 90%;
        margin: 0 auto;
    }
}

@media (max-width: 576px) {
    #menu-menuformativa .sub-menu {
        max-width: 95%;
        padding: 15px;
        max-height: 70vh;
    }
    
    #menu-menuformativa .sub-menu li a {
        padding: 12px 8px;
        font-size: 0.95rem;
    }
}

    </style>

    <?php
    return ob_get_clean();
}

add_shortcode('formazione_info', 'formazione_info_shortcode');

// Aggiungi il nostro Walker personalizzato per gestire i sottomenu e la struttura
class Walker_Nav_Menu_Custom extends Walker_Nav_Menu {

    // Inizio di un nuovo livello di menu (aggiungiamo la classe 'collapse' al secondo livello)
    function start_lvl( &$output, $depth = 0, $args = null ) {
        if ( $depth === 0 ) {
            return parent::start_lvl( $output, $depth, $args );
        }

        // Aggiungi la classe 'collapse' per nascondere il sotto-menu
        $output .= '<ul class="sub-menu collapse" style="display:none;">';
    }

    // Fine di un livello di menu
    function end_lvl( &$output, $depth = 0, $args = null ) {
        if ( $depth === 0 ) {
            return parent::end_lvl( $output, $depth, $args );
        }

        $output .= '</ul>';
    }

    // Inizio di un elemento del menu (gestiamo il comportamento di toggle per il primo livello)
    function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        // Se è il primo livello, aggiungi il comportamento per espandere il sottomenu
        if ( $depth === 0 ) {
            $classes[] = 'menu-item-level-1';
            $output .= '<li class="' . join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) . '">';
            // Usa un link con un evento onclick invece di # per prevenire il comportamento di navigazione
            $output .= '<a href="javascript:void(0);" data-toggle="collapse" data-target="#submenu-' . $item->ID . '" class="d-block clickable-parent">';
            
            $output .= '<h6 class="text-decoration-none text-dark mt-4">' . $item->title . '</h6>';
            $output .= '</a>';
            $output .= '<div id="submenu-' . $item->ID . '" class="collapse">';
        } else {
            // Per il secondo livello, aggiungi il normale link
            parent::start_el( $output, $item, $depth, $args, $id );
        }
    }

    // Fine di un elemento del menu
    function end_el( &$output, $item, $depth = 0, $args = null ) {
        if ( $depth === 0 ) {
            $output .= '</div></li>'; // Chiudi il secondo livello
        } else {
            parent::end_el( $output, $item, $depth, $args );
        }
    }
}

?>

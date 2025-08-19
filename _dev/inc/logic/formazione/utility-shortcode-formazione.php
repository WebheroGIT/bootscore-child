<?php

// Assicurati che il file non sia accessibile direttamente
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/** TODO 2025 08 07 introduzione shrtocde
 * Shortcode per visualizzare il pulsante di download della brochure del corso
 * Uso: [corso_brochure] oppure [corso_brochure post_id="123"]
 * [corso_brochure post_id="123" text="Download PDF" class="btn btn-primary" icon="fas fa-file-pdf"]
 */
function corso_brochure_shortcode( $atts ) {
    // Attributi dello shortcode con valori di default
    $atts = shortcode_atts( array(
        'post_id' => get_the_ID(), // ID del post corrente se non specificato
        'text' => 'Scarica la brochure', // Testo del pulsante
        'class' => 'btn btn-secondary', // Classi CSS del pulsante
        'icon' => 'fas fa-download', // Icona del pulsante
    ), $atts, 'corso_brochure' );

    // Verifica che esista un post ID valido
    if ( ! $atts['post_id'] ) {
        return '';
    }

    // Recupera i file della brochure dal metabox
    $files = rwmb_meta( 'corso_brochure', '', $atts['post_id'] );
    
    // Controlli di sicurezza
    if ( empty( $files ) || ! is_array( $files ) ) {
        return '';
    }
    
    // Prende il primo file disponibile
    $file = reset( $files );
    
    // Verifica che il file abbia URL e nome validi
    if ( empty( $file['url'] ) || empty( $file['name'] ) ) {
        return '';
    }
    
    // Genera il pulsante HTML
    $output = sprintf(
        '<a class="%s" href="%s" download="%s">',
        esc_attr( $atts['class'] ),
        esc_url( $file['url'] ),
        esc_attr( $file['name'] )
    );
    
    // Aggiunge l'icona se specificata
    if ( ! empty( $atts['icon'] ) ) {
        $output .= sprintf( '<i class="%s"></i> ', esc_attr( $atts['icon'] ) );
    }
    
    $output .= esc_html( $atts['text'] );
    $output .= '</a>';
    
    return $output;
}

// Registra lo shortcode
add_shortcode( 'corso_brochure', 'corso_brochure_shortcode' );

/**
 * Versione alternativa per visualizzare tutte le brochure se ce ne sono multiple
 * Uso: [corso_brochure_list] oppure [corso_brochure_list post_id="123"]
 */
function corso_brochure_list_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'post_id' => get_the_ID(),
        'text' => 'Scarica',
        'class' => 'btn btn-secondary',
        'icon' => 'fas fa-download',
        'wrapper_class' => 'brochure-list'
    ), $atts, 'corso_brochure_list' );

    if ( ! $atts['post_id'] ) {
        return '';
    }

    $files = rwmb_meta( 'corso_brochure', '', $atts['post_id'] );
    
    if ( empty( $files ) || ! is_array( $files ) ) {
        return '';
    }
    
    $output = sprintf( '<div class="%s">', esc_attr( $atts['wrapper_class'] ) );
    
    foreach ( $files as $file ) {
        if ( empty( $file['url'] ) || empty( $file['name'] ) ) {
            continue;
        }
        
        $output .= sprintf(
            '<a class="%s" href="%s" download="%s">',
            esc_attr( $atts['class'] ),
            esc_url( $file['url'] ),
            esc_attr( $file['name'] )
        );
        
        if ( ! empty( $atts['icon'] ) ) {
            $output .= sprintf( '<i class="%s"></i> ', esc_attr( $atts['icon'] ) );
        }
        
        $output .= sprintf( '%s %s', esc_html( $atts['text'] ), esc_html( $file['name'] ) );
        $output .= '</a>';
    }
    
    $output .= '</div>';
    
    return $output;
}

// Registra il secondo shortcode
add_shortcode( 'corso_brochure_list', 'corso_brochure_list_shortcode' );
?>
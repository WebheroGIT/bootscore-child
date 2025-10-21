<?php
// Blocca l'accesso diretto al file
// logic settings pagine e slider
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Termina l'esecuzione se il file è chiamato direttamente
}

// Inclusione logica WooCommerce per singolo prodotto
//require_once get_stylesheet_directory() . '/_dev/inc/logic/settings/slider-banner.php';



/**
 * Helper: display name mapping for cat-formazione taxonomy (pluralization, custom labels)
 */
if (!function_exists('bs_cat_formazione_display_name')) {
    function bs_cat_formazione_display_name($term) {
        if (!$term instanceof WP_Term) {
            return '';
        }
        if ($term->taxonomy !== 'cat-formazione') {
            return $term->name;
        }
        // Slug => Display label mapping
        $map = array(
            'laurea-magistrale' => 'Lauree magistrali',
            'lauree-triennali'  => 'Lauree triennali',
            'laurea-ciclo-unico'  => 'Lauree ciclo unico',
            'dottorati-di-ricerca'  => 'Dottorati di ricerca',
            'corso-di-formazione'  => 'Corsi di formazione',
           
            // Aggiungere qui altre regole di visualizzazione
        );
        return isset($map[$term->slug]) ? $map[$term->slug] : $term->name;
    }
}

/**
 * Override archive H1 for cat-formazione using the mapping above
 */
add_filter('get_the_archive_title', function ($title) {
    if (is_tax('cat-formazione')) {
        $term = get_queried_object();
        if ($term instanceof WP_Term) {
            $title = bs_cat_formazione_display_name($term);
        }
    }
    return $title;
}, 20);


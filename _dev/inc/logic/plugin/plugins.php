<?php

// Assicurati che il file non sia accessibile direttamente
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Debug function per WP Grid Builder
function debug_wpgb_status() {
    if (is_post_type_archive('formazione') || is_tax('cat-formazione')) {
        if (current_user_can('administrator')) {
            $wpgb_active = function_exists('wpgb_facet') ? 'ATTIVO' : 'NON ATTIVO';
            echo '<!-- WP Grid Builder Status: ' . $wpgb_active . ' -->';
            if (function_exists('wpgb_facet')) {
                echo '<!-- Facet ID 1 disponibile -->';
            }
        }
    }
}
add_action('wp_head', 'debug_wpgb_status');

// Hook per i filtri della griglia formazione
function add_formazione_grid_filters() {
    // Verifica che WP Grid Builder sia attivo
    if (function_exists('wpgb_facet')) {
        echo '<div class="formazione-filters mb-4">';
        echo do_shortcode('[wpgb_facet id="1" grid="formazione-posts"]');
        echo '</div>';
    }
}
add_action('formazione_grid_filters', 'add_formazione_grid_filters');

// Configurazione WP Grid Builder per la griglia formazione
function formazione_grid_wpgb_settings() {
    // Solo nelle pagine di archivio formazione
    if (is_post_type_archive('formazione') || is_tax('cat-formazione')) {
        if (function_exists('wpgb_facet')) {
            // Registra il grid con WP Grid Builder
            // Questo dice a WPGB di targetizzare il container con ID "formazione-posts"
            wp_add_inline_script('wpgb-facet', '
                document.addEventListener("DOMContentLoaded", function() {
                    if (typeof wpgb !== "undefined" && wpgb.facet) {
                        wpgb.facet.addGrid({
                            id: "formazione-posts",
                            selector: "#formazione-posts"
                        });
                    }
                });
            ');
        }
    }
}
add_action('wp_enqueue_scripts', 'formazione_grid_wpgb_settings');
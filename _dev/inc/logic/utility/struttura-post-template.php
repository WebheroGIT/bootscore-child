<?php
/**
 * Struttura Post Template
 * 
 * 
 * 
 * @package BootScore Child
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
// TODO 2025 01 25 - Abilita template personalizzati per tutti i post types
/**
 * Abilita il supporto per i template personalizzati su tutti i post types
 * Questo permette di vedere e selezionare i template nell'editor dei post
 */
function enable_post_type_templates() {
    // Lista dei post types per cui abilitare i template
    $post_types = array(
        'post', 'ateneo', 'dipartimento', 'formazione', 'piano', 'tirocinio', 'eventi', 'dirigenza',
        'progetto-ricerca', 'avviso', 'dottorato', 'territorio-societa', 'internazionale', 'ricerca',
        'iscriviti', 'servizio', 'press', 'rassegna-stampa', 'piani-studio', 'offerta-formativa'
    );
    
    foreach ($post_types as $post_type) {
        // Verifica che il post type esista prima di aggiungere il supporto
        if (post_type_exists($post_type)) {
            // Forza l'aggiunta del supporto anche per post types già registrati
            add_post_type_support($post_type, 'page-attributes');
            
            // Modifica l'oggetto post type se già registrato
            global $wp_post_types;
            if (isset($wp_post_types[$post_type])) {
                $wp_post_types[$post_type]->supports['page-attributes'] = true;
            }
        }
    }
}
add_action('init', 'enable_post_type_templates', 999); // Priorità molto alta per eseguire dopo la registrazione dei post types

/**
 * Aggiunge i template personalizzati alla lista dei template disponibili
 * per i post types specificati
 */
function add_custom_post_templates($templates, $theme, $post) {
    // Lista dei post types supportati
    $supported_post_types = array(
        'post', 'ateneo', 'dipartimento', 'formazione', 'piano', 'tirocinio', 'eventi', 'dirigenza',
        'progetto-ricerca', 'avviso', 'dottorato', 'territorio-societa', 'internazionale', 'ricerca',
        'iscriviti', 'servizio', 'press', 'rassegna-stampa', 'piani-studio', 'offerta-formativa'
    );
    
    // Verifica se il post type corrente è supportato
    $current_post_type = '';
    if ($post && isset($post->post_type)) {
        $current_post_type = $post->post_type;
    } elseif (isset($_GET['post_type'])) {
        $current_post_type = $_GET['post_type'];
    } elseif (isset($_GET['post'])) {
        $current_post_type = get_post_type($_GET['post']);
    }
    
    // Se non è un post type supportato, restituisce i template originali
    if (!in_array($current_post_type, $supported_post_types)) {
        return $templates;
    }
    
    // Aggiunge i nostri template personalizzati per tutti i post types supportati
    $custom_templates = array(
        'single-templates/single-blank.php' => 'Blank',
        'single-templates/single-blank-container.php' => 'Blank with container',
        'single-templates/single-full-width-image.php' => 'Full width image',
        'single-templates/single-sidebar-none.php' => 'No Sidebar',
        'single-templates/single-sidebar-left.php' => 'Left Sidebar',
        'single-templates/single-full-width-no-img.php' => 'Full width no image',
        'single-templates/unimarconi-hero-full.php' => 'Unimarconi Hero Full Width',
        'single-templates/unimarconi-hero-sidebar.php' => 'Unimarconi Hero with Sidebar'
    );
    
    return array_merge($templates, $custom_templates);
}
add_filter('theme_post_templates', 'add_custom_post_templates', 10, 3);

/**
 * Forza la visibilità del meta box "Attributi pagina" per tutti i post types
 * anche se non supportano nativamente page-attributes
 */
function force_page_attributes_meta_box() {
    $post_types = array(
        'post', 'ateneo', 'dipartimento', 'formazione', 'piano', 'tirocinio', 'eventi', 'dirigenza',
        'progetto-ricerca', 'avviso', 'dottorato', 'territorio-societa', 'internazionale', 'ricerca',
        'iscriviti', 'servizio', 'press', 'rassegna-stampa', 'piani-studio', 'offerta-formativa'
    );
    
    foreach ($post_types as $post_type) {
        // Verifica che il post type esista prima di aggiungere il meta box
        if (post_type_exists($post_type)) {
            add_meta_box(
                'pageparentdiv',
                __('Page Attributes'),
                'page_attributes_meta_box',
                $post_type,
                'side',
                'core'
            );
        }
    }
}
add_action('add_meta_boxes', 'force_page_attributes_meta_box');
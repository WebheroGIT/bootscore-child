<?php

/**
 * @package Bootscore Child
 *
 * @version 6.0.0
 */


// Exit if accessed directly
defined('ABSPATH') || exit;


/**
 * Enqueue scripts and styles
 */
add_action('wp_enqueue_scripts', 'bootscore_child_enqueue_styles');
function bootscore_child_enqueue_styles() {

  // Compiled main.css
  $modified_bootscoreChildCss = date('YmdHi', filemtime(get_stylesheet_directory() . '/assets/css/main.css'));
  wp_enqueue_style('main', get_stylesheet_directory_uri() . '/assets/css/main.css', array('parent-style'), $modified_bootscoreChildCss);

  // style.css
  wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
  
  // custom.js
  // Get modification time. Enqueue file with modification date to prevent browser from loading cached scripts when file content changes. 
  $modificated_CustomJS = date('YmdHi', filemtime(get_stylesheet_directory() . '/assets/js/custom.js'));
  wp_enqueue_script('custom-js', get_stylesheet_directory_uri() . '/assets/js/custom.js', array('jquery'), $modificated_CustomJS, false, true);
  
  // formazione-filters.js - nell'archivio formazione e nelle sue taxonomy
  if (is_post_type_archive('formazione') || is_tax('cat-formazione') || is_tax('area-formazione') || is_tax('modalita-formazione')) {
    $modificated_FormazioneFiltersJS = date('YmdHi', filemtime(get_stylesheet_directory() . '/assets/js/formazione-filters.js'));
    wp_enqueue_script('formazione-filters-js', get_stylesheet_directory_uri() . '/assets/js/formazione-filters.js', array('jquery'), $modificated_FormazioneFiltersJS, true);
  }
}


// enqueue style 

// enqueue HUBSPOT
function enqueue_hubspot_script() {
    // Carica lo script solo dove serve (es. pagine con form)
    if (is_page() || is_single()) {
        wp_enqueue_script(
            'hubspot-forms', 
            '//js.hsforms.net/forms/shell.js', 
            array(), 
            null, 
            true // carica nel footer
        );
    }
}
add_action('wp_enqueue_scripts', 'enqueue_hubspot_script');


// regole nuovo pacchetto css e js da dev

function enqueue_custom_assets() {
  // Trova il file CSS con l'hash dinamico
  $css_files = glob( get_stylesheet_directory() . '/_dev/build/css/style-*.css' ); // Trova tutti i file CSS con l'hash
  if ( $css_files ) {
      $css_version = filemtime( $css_files[0] ); // Prendi il timestamp del file trovato
      wp_enqueue_style(
          'theme-style', 
          get_stylesheet_directory_uri() . '/_dev/build/css/' . basename( $css_files[0] ),  // Percorso completo del file CSS
          array(),
          $css_version
      );
  }

  // Trova il file JS con l'hash dinamico
  $js_files = glob( get_stylesheet_directory() . '/_dev/build/js/script-*.js' ); // Trova tutti i file JS con l'hash
  if ( $js_files ) {
      $js_version = filemtime( $js_files[0] ); // Prendi il timestamp del file trovato
      wp_enqueue_script(
          'theme-script', 
          get_stylesheet_directory_uri() . '/_dev/build/js/' . basename( $js_files[0] ), // Percorso completo del file JS
          array(), 
          $js_version, 
          true  // Carica JS nel footer
      );
  }
}
add_action( 'wp_enqueue_scripts', 'enqueue_custom_assets' );



require_once get_stylesheet_directory() . '/_dev/inc/logic/logic.php';


// Rimuovi funzione breadcrumb del tema principale
remove_action('wp_head', 'the_breadcrumb');
// Reinclude il file breadcrumb del tema child (dove abbiamo aggiunto la gestione degli archivi)s
require_once get_stylesheet_directory() . '/inc/breadcrumb.php';




/// TODO temporanea change text

function modify_related_posts_title($translated_text, $text, $domain) {
    // Verifica se il testo da cambiare è "You might also like"
    if ($text === 'You might also like') {
        // Restituisci il nuovo testo che desideri
        return 'Potrebbe interessarti';
    }
    
    // Se non è il testo che cerchi, restituisci il testo originale
    return $translated_text;
}

// Aggiungi il filtro per modificare il testo
add_filter('gettext', 'modify_related_posts_title', 10, 3);



// TODO mod categoira togli prefisso

function remove_custom_taxonomy_prefix($title) {
    if (is_tax('cat-formazione')) {
        // Rimuove il prefisso dalla tassonomia 'cat-formazione' (categoria formazione)
        $title = preg_replace('/^Categoria\s+.*?:\s*/', '', $title);
    }
    return $title;
}
add_filter('get_the_archive_title', 'remove_custom_taxonomy_prefix');



// TODO 2025 08 05 AGGIUNTA VISTA CATEGORIE SUI POS TYPE BACKEND

/**
 * Sistema unificato per aggiungere colonne personalizzate ai post type
 * con le rispettive tassonomie
 */

// Configurazione centralizzata: post_type => taxonomy
$custom_post_taxonomies = array(
    'internazionale' => 'cat-internazionale',
    'formazione'     => 'cat-formazione',
    // Aggiungi qui altri post type e tassonomie
    // 'eventi'      => 'cat-eventi',
    // 'prodotti'    => 'cat-prodotti',
    // 'servizi'     => 'cat-servizi',
);

/**
 * Funzione per aggiungere colonne ai post type
 */
function add_custom_taxonomy_columns($columns) {
    global $typenow, $custom_post_taxonomies;
    
    if (isset($custom_post_taxonomies[$typenow])) {
        $taxonomy = $custom_post_taxonomies[$typenow];
        $taxonomy_obj = get_taxonomy($taxonomy);
        $label = $taxonomy_obj ? $taxonomy_obj->labels->name : ucfirst(str_replace(array('cat-', '-'), array('', ' '), $taxonomy));
        
        $columns["taxonomy-{$taxonomy}"] = $label;
    }
    
    return $columns;
}

/**
 * Funzione per popolare le colonne con i termini della tassonomia
 */
function populate_custom_taxonomy_columns($column, $post_id) {
    global $custom_post_taxonomies;
    
    foreach ($custom_post_taxonomies as $post_type => $taxonomy) {
        if ($column === "taxonomy-{$taxonomy}") {
            $terms = get_the_terms($post_id, $taxonomy);
            
            if ($terms && !is_wp_error($terms)) {
                $categories = array_map(function($term) {
                    return esc_html($term->name);
                }, $terms);
                echo implode(', ', $categories);
            } else {
                echo '<span style="color: #999;">Nessuna categoria</span>';
            }
            break;
        }
    }
}

/**
 * Registra automaticamente gli hook per tutti i post type configurati
 */
function register_custom_taxonomy_columns() {
    global $custom_post_taxonomies;
    
    foreach ($custom_post_taxonomies as $post_type => $taxonomy) {
        // Hook per aggiungere la colonna
        add_filter("manage_{$post_type}_posts_columns", 'add_custom_taxonomy_columns');
        
        // Hook per popolare la colonna
        add_action("manage_{$post_type}_posts_custom_column", 'populate_custom_taxonomy_columns', 10, 2);
    }
}

// Inizializza il sistema
add_action('admin_init', 'register_custom_taxonomy_columns');

/**
 * BONUS: Rendi le colonne ordinabili (opzionale)
 */
function make_taxonomy_columns_sortable($columns) {
    global $typenow, $custom_post_taxonomies;
    
    if (isset($custom_post_taxonomies[$typenow])) {
        $taxonomy = $custom_post_taxonomies[$typenow];
        $columns["taxonomy-{$taxonomy}"] = "taxonomy-{$taxonomy}";
    }
    
    return $columns;
}

// Registra le colonne ordinabili per tutti i post type
function register_sortable_columns() {
    global $custom_post_taxonomies;
    
    foreach ($custom_post_taxonomies as $post_type => $taxonomy) {
        add_filter("manage_edit-{$post_type}_sortable_columns", 'make_taxonomy_columns_sortable');
    }
}

add_action('admin_init', 'register_sortable_columns');
// END VISTA CATEGORIE SUI POS TYPE BACKEND


// TODO 2025 09 01 - Registrazione sidebar per eventi
/**
 * Registra sidebar specifica per gli eventi
 */
function register_eventi_sidebar() {
    register_sidebar(array(
        'name'          => esc_html__('Eventi Sidebar', 'bootscore'),
        'id'            => 'eventi-sidebar',
        'description'   => esc_html__('Sidebar specifica per i post type eventi con form di contatto.', 'bootscore'),
        'before_widget' => '<section id="%1$s" class="widget mb-4">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title h5">',
        'after_title'   => '</h2>',
    ));
}
add_action('widgets_init', 'register_eventi_sidebar');


// TODO 2025 08 19 
// Register new 3 level depth nav-walker
// Register new 3 level depth nav-walker
function register_navwalker() {
  require_once('inc/class-bootstrap-5-navwalker.php');
  // Register Menus
  register_nav_menu('main-menu', 'Main menu');
  register_nav_menu('footer-menu', 'Footer menu');
}
add_action('init', 'register_navwalker');
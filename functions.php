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




/**
 * Carica gli stili CSS personalizzati solo nell'admin di WordPress
 */
function load_admin_styles() {
    wp_enqueue_style(
        'bootscore-child-admin-styles',
        get_stylesheet_directory_uri() . '/admin-styles.css',
        array(),
        wp_get_theme()->get('Version')
    );
}
add_action('admin_enqueue_scripts', 'load_admin_styles');

/**
 * Keep featured formazione on top using menu_order (stable ordering)
 * - If is_featured=1 => set menu_order = -1
 * - Else => set menu_order = 0
 */
add_action('save_post_formazione', function ($post_id, $post, $update) {
  // Avoid autosave/revisions
  if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
    return;
  }
  // Read Meta Box value
  $featured = get_post_meta($post_id, 'is_featured', true);
  $desired_order = ($featured === '1' || $featured === 1) ? -1 : 0;
  // Update only if changed
  if ((int) $post->menu_order !== (int) $desired_order) {
    wp_update_post(array(
      'ID' => $post_id,
      'menu_order' => $desired_order,
    ));
  }
}, 10, 3);

/**
 * Admin Tools page: Sync featured (formazione) -> menu_order
 */
add_action('admin_menu', function () {
  add_management_page(
    __('Sincronizza "In evidenza" Formazione', 'bootscore'),
    __('Sincronizza "In evidenza" Formazione', 'bootscore'),
    'manage_options',
    'sync-formazione-featured',
    function () {
      if (!current_user_can('manage_options')) {
        wp_die(__('Non hai i permessi necessari per accedere a questa pagina.', 'bootscore'));
      }

      $synced = isset($_GET['synced']) ? intval($_GET['synced']) : null;
      $updated = isset($_GET['updated']) ? intval($_GET['updated']) : null;

      echo '<div class="wrap">';
      echo '<h1>' . esc_html__('Sincronizza "In evidenza" Formazione', 'bootscore') . '</h1>';

      if ($synced !== null) {
        printf('<div class="notice notice-success"><p>' . esc_html__('%d elaborati, %d aggiornati.', 'bootscore') . '</p></div>', $synced, $updated);
      }

      $url = wp_nonce_url(admin_url('tools.php?page=sync-formazione-featured&do_sync=1'), 'sync_formazione_featured');
      echo '<p>' . esc_html__('Allinea il campo menu_order dei post "formazione" in base al flag "is_featured" (In evidenza).', 'bootscore') . '</p>';
      echo '<a href="' . esc_url($url) . '" class="button button-primary">' . esc_html__('Esegui sincronizzazione', 'bootscore') . '</a>';
      echo '</div>';
    }
  );
});

add_action('load-tools_page_sync-formazione-featured', function () {
  if (!current_user_can('manage_options')) {
    return;
  }
  if (!isset($_GET['do_sync'])) {
    return;
  }
  check_admin_referer('sync_formazione_featured');

  $paged = 1;
  $per_page = 500;
  $processed = 0;
  $updated = 0;

  do {
    $q = new WP_Query(array(
      'post_type'      => 'formazione',
      'post_status'    => 'any',
      'posts_per_page' => $per_page,
      'paged'          => $paged,
      'fields'         => 'ids',
      'no_found_rows'  => true,
    ));

    if (empty($q->posts)) {
      break;
    }

    foreach ($q->posts as $post_id) {
      $processed++;
      $featured = get_post_meta($post_id, 'is_featured', true);
      $desired = ($featured === '1' || $featured === 1) ? -1 : 0;
      $current = (int) get_post_field('menu_order', $post_id);
      if ($current !== (int) $desired) {
        wp_update_post(array('ID' => $post_id, 'menu_order' => $desired));
        $updated++;
      }
    }

    $paged++;
  } while (true);

  wp_safe_redirect(admin_url('tools.php?page=sync-formazione-featured&synced=' . $processed . '&updated=' . $updated));
  exit;
});


// Meta Box fields for Intro Full Width template button
add_filter('rwmb_meta_boxes', function ($meta_boxes) {
  // Ensure Meta Box plugin is active
  if (!function_exists('rwmb_meta')) {
    return $meta_boxes;
  }

  $meta_boxes[] = array(
    'title'      => __('Intro Button', 'bootscore'),
    'id'         => 'intro_button_fields',
    'post_types' => array('page', 'post', 'ateneo', 'dipartimento', 'formazione', 'piano', 'tirocinio', 'eventi', 'progetto-ricerca', 'avviso', 'dottorato', 'territorio-societa', 'internazionale', 'ricerca', 'iscriviti', 'servizio', 'press', 'rassegna-stampa', 'piani-studio', 'offerta-formativa', 'dirigenza'),
    'context'    => 'normal',
    'priority'   => 'high',
    'autosave'   => false,
    'fields'     => array(
      array(
        'name' => __('Button Text', 'bootscore'),
        'id'   => 'intro_button_text',
        'type' => 'text',
      ),
      array(
        'name' => __('Button URL', 'bootscore'),
        'id'   => 'intro_button_url',
        'type' => 'url',
      ),
    ),
    // Show these fields only when this specific template is selected
    'show'       => array(
      'template' => array('single-templates/single-intro-full-width.php'),
    ),
  );

  // Featured (Metti in evidenza) for formazione CPT
  $meta_boxes[] = array(
    'title'      => __('Metti in evidenza', 'bootscore'),
    'id'         => 'formazione_featured_flag',
    'post_types' => array('formazione'),
    'context'    => 'side',
    'priority'   => 'high',
    'autosave'   => false,
    'fields'     => array(
      array(
        'name' => __('Mostra questo contenuto in evidenza', 'bootscore'),
        'id'   => 'is_featured',
        'type' => 'checkbox',
        'std'  => 0,
      ),
    ),
  );

  return $meta_boxes;
});
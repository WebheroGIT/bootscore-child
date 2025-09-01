<?php
/**
 * Admin Internal Search Plugin
 * 
 * Provides an internal search functionality for WordPress admin
 * to search through all post types with advanced filtering
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class AdminInternalSearch {
    
    private static $instance = null;
    
    /**
     * Get singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_admin_internal_search', array($this, 'ajax_search'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            'Ricerca Interna',
            'Ricerca Interna',
            'manage_options',
            'admin-internal-search',
            array($this, 'admin_page'),
            'dashicons-search',
            30
        );
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts($hook) {
        if ($hook !== 'toplevel_page_admin-internal-search') {
            return;
        }
        
        wp_enqueue_script('jquery');
        wp_enqueue_script(
            'admin-internal-search',
            get_stylesheet_directory_uri() . '/_dev/assets-admin/admin-internal-search.js',
            array('jquery'),
            '1.0.0',
            true
        );
        
        wp_localize_script('admin-internal-search', 'adminSearch', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('admin_search_nonce'),
            'admin_url' => admin_url()
        ));
        
        wp_enqueue_style(
            'admin-internal-search',
            get_stylesheet_directory_uri() . '/_dev/assets-admin/internal-search.css',
            array(),
            '1.0.0'
        );
    }
    
    /**
     * Admin page content
     */
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1>Ricerca Interna</h1>
            <div class="admin-search-container">
                <div class="search-form">
                    <input type="text" id="search-input" placeholder="Inserisci il titolo o parte di esso..." />
                    <button type="button" id="search-button" class="button button-primary">Cerca</button>
                    <button type="button" id="clear-button" class="button">Pulisci</button>
                </div>
                
                <div id="search-results" class="search-results">
                    <div id="loading" class="loading" style="display: none;">
                        <span class="spinner is-active"></span>
                        Ricerca in corso...
                    </div>
                    
                    <div id="results-table" style="display: none;">
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th>Titolo</th>
                                    <th>Slug</th>
                                    <th>Tipo Post</th>
                                    <th>Data</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody id="results-tbody">
                                <!-- Results will be inserted here -->
                            </tbody>
                        </table>
                    </div>
                    
                    <div id="no-results" style="display: none;">
                        <p>Nessun risultato trovato.</p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * AJAX search handler
     */
    public function ajax_search() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'admin_search_nonce')) {
            wp_die('Security check failed');
        }
        
        // Check user permissions
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        $search_term = sanitize_text_field($_POST['search_term']);
        
        if (empty($search_term)) {
            wp_send_json_error('Search term is required');
        }
        
        // Get all public post types
        $post_types = get_post_types(array('public' => true), 'names');
        
        // Add private post types that might be useful
        $post_types[] = 'revision';
        $post_types[] = 'nav_menu_item';
        
        $args = array(
            'post_type' => $post_types,
            'post_status' => array('publish', 'draft', 'private', 'pending'),
            's' => $search_term,
            'posts_per_page' => 50,
            'orderby' => 'relevance',
            'order' => 'DESC'
        );
        
        $query = new WP_Query($args);
        $results = array();
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();
                $post_type = get_post_type();
                $post_type_object = get_post_type_object($post_type);
                
                $results[] = array(
                    'id' => $post_id,
                    'title' => get_the_title(),
                    'slug' => get_post_field('post_name', $post_id),
                    'post_type' => $post_type,
                    'post_type_label' => $post_type_object ? $post_type_object->labels->singular_name : $post_type,
                    'date' => get_the_date('Y-m-d H:i:s'),
                    'edit_link' => get_edit_post_link($post_id),
                    'view_link' => get_permalink($post_id),
                    'status' => get_post_status()
                );
            }
            wp_reset_postdata();
        }
        
        wp_send_json_success($results);
    }
}

// Initialize the plugin
AdminInternalSearch::getInstance();
<?php
/**
 * Sidebar Text Alternative
 * 
 * Gestisce testi personalizzati per la sidebar tramite Meta Box
 * 
 * @package Bootscore Child
 */

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Aggiunge Meta Box per testo sidebar personalizzato
 */
function add_sidebar_text_meta_box() {
    // Lista di tutti i post types e page
    $post_types = array(
        'post', 'page', 'ateneo', 'dipartimento', 'formazione', 'piano', 'tirocinio', 'eventi', 'dirigenza',
        'progetto-ricerca', 'avviso', 'dottorato', 'territorio-societa', 'internazionale', 'ricerca',
        'iscriviti', 'servizio', 'press', 'rassegna-stampa', 'piani-studio', 'offerta-formativa', 'territorio'
    );
    
    foreach ($post_types as $post_type) {
        if (post_type_exists($post_type)) {
            add_meta_box(
                'sidebar_text_meta_box',
                __('Testo Sidebar Personalizzato', 'bootscore'),
                'sidebar_text_meta_box_callback',
                $post_type,
                'side',
                'default'
            );
        }
    }
}
add_action('add_meta_boxes', 'add_sidebar_text_meta_box');

/**
 * Callback per il Meta Box
 */
function sidebar_text_meta_box_callback($post) {
    // Aggiunge nonce per sicurezza
    wp_nonce_field('sidebar_text_meta_box', 'sidebar_text_meta_box_nonce');
    
    // Recupera il valore attuale
    $sidebar_button_text = get_post_meta($post->ID, '_sidebar_button_text', true);
    
    ?>
    <table class="form-table">
        <tr>
            <td>
                <label for="sidebar_button_text"><strong><?php _e('Testo Bottone Mobile:', 'bootscore'); ?></strong></label><br>
                <input type="text" 
                       id="sidebar_button_text" 
                       name="sidebar_button_text" 
                       value="<?php echo esc_attr($sidebar_button_text); ?>" 
                       style="width: 100%; margin-top: 5px;"
                       placeholder="<?php _e('es. Apri menu laterale', 'bootscore'); ?>" />
                <p class="description"><?php _e('Testo che appare nel bottone mobile per aprire la sidebar', 'bootscore'); ?></p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Salva i dati del Meta Box
 */
function save_sidebar_text_meta_box($post_id) {
    // Verifica nonce
    if (!isset($_POST['sidebar_text_meta_box_nonce']) || 
        !wp_verify_nonce($_POST['sidebar_text_meta_box_nonce'], 'sidebar_text_meta_box')) {
        return;
    }
    
    // Verifica permessi
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Salva i dati
    if (isset($_POST['sidebar_button_text'])) {
        update_post_meta($post_id, '_sidebar_button_text', sanitize_text_field($_POST['sidebar_button_text']));
    }
}
add_action('save_post', 'save_sidebar_text_meta_box');

/**
 * Filtro per sovrascrivere il testo del bottone mobile della sidebar
 */

// Filtro per il testo del bottone mobile
function custom_sidebar_button_text($text) {
    $post_id = get_the_ID();
    if (!$post_id) return $text;
    
    $custom_button_text = get_post_meta($post_id, '_sidebar_button_text', true);
    if (!empty($custom_button_text)) {
        return $custom_button_text;
    }
    
    return $text;
}
add_filter('bootscore/offcanvas/sidebar/button/text', 'custom_sidebar_button_text', 20);


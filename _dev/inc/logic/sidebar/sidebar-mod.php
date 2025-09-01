<?php
/**
 * Sidebar con regole personalizzate
 *
 * @package Bootscore Child
 */

// Exit if accessed directly
defined('ABSPATH') || exit;

function custom_sidebar_rules() {
    // ========== REGOLE PER LA SIDEBAR ==========
    
    $current_post_id = get_the_ID();
    $current_post_type = get_post_type();
    $show_sidebar = false;
    $sidebar_content = '';
    
    // Lista degli ID dei post dove mostrare la sidebar (commentati per ora)
    $allowed_post_ids = array(
        // 56,
        // 59, 
        // 69,
        // 62
    );
    
    // Lista dei post type dove mostrare la sidebar (commentati per ora)
    $allowed_post_types = array(
        // 'formazione',
        // 'servizi'
    );
    
    // Regola speciale per formazione con corso_hubspot_id
    if ($current_post_type === 'formazione') {
        $corso_hubspot_id = rwmb_meta('corso_hubspot_id');
        if (!empty($corso_hubspot_id)) {
            $show_sidebar = true;
            $sidebar_content = 'hubspot'; // Indica che deve mostrare contenuto HubSpot
        }
    }
    
    // Regola speciale per eventi con form WSForm
    if ($current_post_type === 'eventi') {
        $show_sidebar = true;
        $sidebar_content = 'eventi_form'; // Indica che deve mostrare form WSForm per eventi
    }
    
    // Controlla se il post ID corrente è nella lista degli ID consentiti
    if (in_array($current_post_id, $allowed_post_ids)) {
        $show_sidebar = true;
        $sidebar_content = 'default'; // Sidebar normale
    }
    
    // Controlla se il post type corrente è nella lista dei post type consentiti
    if (in_array($current_post_type, $allowed_post_types)) {
        $show_sidebar = true;
        $sidebar_content = 'default'; // Sidebar normale
    }
    
    // Mostra la sidebar se le condizioni sono soddisfatte
    if ($show_sidebar) {
        if ($sidebar_content === 'eventi_form') {
            // Sidebar speciale per eventi con WSForm
            ?>
            <div class="<?= apply_filters('bootscore/class/sidebar/col', 'col-lg-3 order-first order-lg-2'); ?>">
                <aside id="secondary" class="widget-area">
                    <button class="<?= apply_filters('bootscore/class/sidebar/button', 'd-lg-none btn btn-outline-secondary w-100 mb-4 d-flex justify-content-between align-items-center'); ?>" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
                        <?= apply_filters('bootscore/offcanvas/sidebar/button/text', __('Informazioni Evento', 'bootscore')); ?> <?= apply_filters('bootscore/icon/ellipsis-vertical', '<i class="fa-solid fa-ellipsis-vertical"></i>'); ?>
                    </button>
                    <div class="<?= apply_filters('bootscore/class/sidebar/offcanvas', 'offcanvas-lg offcanvas-end'); ?>" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
                        <div class="offcanvas-header <?= apply_filters('bootscore/class/offcanvas/header', '', 'sidebar'); ?>">
                            <span class="h5 offcanvas-title" id="sidebarLabel"><?= apply_filters('bootscore/offcanvas/sidebar/title', __('Richiedi Informazioni', 'bootscore')); ?></span>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body flex-column <?= apply_filters('bootscore/class/offcanvas/body', '', 'sidebar'); ?>">
                            
                            <?php do_action('bootscore_before_sidebar_widgets'); ?>
                            
                        
                                <!-- WSForm con ID 1 -->
                                <?php echo do_shortcode('[ws_form id="1"]'); ?>
                            
                            
                            <?php do_action('bootscore_after_sidebar_widgets'); ?>
                            
                        </div>
                    </div>
                </aside>
            </div>
            <?php
        } elseif ($sidebar_content === 'hubspot') {
            // Sidebar speciale per formazione con HubSpot ID
            $corso_hubspot_id = rwmb_meta('corso_hubspot_id');
            ?>
            <div class="<?= apply_filters('bootscore/class/sidebar/col', 'col-lg-3 order-first order-lg-2'); ?>">
                <aside id="secondary" class="widget-area">
                    <button class="<?= apply_filters('bootscore/class/sidebar/button', 'd-lg-none btn btn-outline-secondary w-100 mb-4 d-flex justify-content-between align-items-center'); ?>" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
                        <?= apply_filters('bootscore/offcanvas/sidebar/button/text', __('Richiedi informazioni', 'bootscore')); ?> <?= apply_filters('bootscore/icon/ellipsis-vertical', '<i class="fa-solid fa-ellipsis-vertical"></i>'); ?>
                    </button>
                    <div class="<?= apply_filters('bootscore/class/sidebar/offcanvas', 'offcanvas-lg offcanvas-end'); ?>" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
                        <div class="offcanvas-header <?= apply_filters('bootscore/class/offcanvas/header', '', 'sidebar'); ?>">
                            <span class="h5 offcanvas-title" id="sidebarLabel"><?= apply_filters('bootscore/offcanvas/sidebar/title', __('Iscriviti al Corso', 'bootscore')); ?></span>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body flex-column <?= apply_filters('bootscore/class/offcanvas/body', '', 'sidebar'); ?>">
                            
                            <?php do_action('bootscore_before_sidebar_widgets'); ?>
                            
                            <div class="widget border rounded p-3">
                                <!-- Container per il form HubSpot -->
                                 <h3 class="h5 text-primary">Richiedi informazioni</h3>
                                <div id="hubspot-form-container"></div>
                                
                                <!-- Script HubSpot -->
                                <script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        hbspt.forms.create({
                                            region: "na1",
                                            portalId: "19545315",
                                            formId: "<?php echo esc_js($corso_hubspot_id); ?>",
                                            target: '#hubspot-form-container'
                                        });
                                        jQuery('.bd-example-modal-lg').on('hidden.bs.modal', function () {
                                            location.reload();
                                        });
                                    });
                                </script>
                            </div>
                            
                            <?php do_action('bootscore_after_sidebar_widgets'); ?>
                            
                        </div>
                    </div>
                </aside>
            </div>
            <?php
        } else {
            // Sidebar normale
            get_sidebar();
        }
    }
}
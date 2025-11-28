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
    
    // NON mostrare la sidebar nell'archivio del post type "progetto"
    if (is_post_type_archive('progetto')) {
        return; // Esci senza mostrare nulla
    }
    
    // IMPORTANTE: Questa funzione deve essere chiamata solo in contesti single/page
    // Non funziona in archivi, quindi usciamo prima se siamo in un archivio
    if (is_archive() || is_home() || is_search()) {
        // In archivi, mostra la sidebar standard
        get_sidebar();
        return;
    }
    
    // Verifica che siamo in un contesto valido (single o page)
    $current_post_id = get_the_ID();
    if (!$current_post_id) {
        // Se non c'è un post ID, mostra la sidebar standard e esci
        get_sidebar();
        return;
    }
    
    $current_post_type = get_post_type();
    $show_sidebar = false;
    $suppress_default_sidebar = false; // Se true, evita la sidebar di default
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
    
    // Regola speciale per formazione, scuole e dottorato (usano corso_wysiwyg_sidebar)
    if ($current_post_type === 'formazione' || $current_post_type === 'scuole' || $current_post_type === 'dottorato') {
        // Prima controlla se c'è contenuto WYSIWYG personalizzato
        $corso_wysiwyg_sidebar = rwmb_meta('corso_wysiwyg_sidebar');
        if (!empty($corso_wysiwyg_sidebar)) {
            $show_sidebar = true;
            $sidebar_content = 'wysiwyg'; // Indica che deve mostrare contenuto WYSIWYG
        } else {
            // Se non c'è WYSIWYG, controlla HubSpot ID
            $corso_hubspot_id = rwmb_meta('corso_hubspot_id');
            if (!empty($corso_hubspot_id)) {
                $show_sidebar = true;
                $sidebar_content = 'hubspot'; // Indica che deve mostrare contenuto HubSpot
            }
        }
    }
    
    // Regola speciale per tirocinio: mostra le relazioni in sidebar, mai form
    if ($current_post_type === 'tirocinio') {
        if (class_exists('MB_Relationships_API')) {
            $rel_posts = MB_Relationships_API::get_connected([
                'id'   => 'rel-tir-form',
                'from' => $current_post_id,
                'type' => 'to',
            ]);
            if (!empty($rel_posts)) {
                $show_sidebar = true;
                $sidebar_content = 'tirocinio_relations';
                // Evita qualunque default successivo
                $suppress_default_sidebar = true;
            }
        }
    }

    // Regola per post type "progetto":
    // - Se esiste il campo WYSIWYG 'pricerca_campo_sidebar', mostra quel contenuto
    // - Altrimenti non mostrare alcuna sidebar (niente default, niente form)
    if ($current_post_type === 'progetto') {
        $progetto_wysiwyg = rwmb_meta('pricerca_campo_sidebar');
        if (!empty($progetto_wysiwyg)) {
            $show_sidebar = true;
            $sidebar_content = 'wysiwyg_progetto';
        } else {
            $show_sidebar = false;
            $suppress_default_sidebar = true;
        }
    }
    
    // Regola per all_wysiwyg_sidebar: applicata a tutti gli altri post types/pagine
    // (escludendo formazione, scuole, dottorato che usano corso_wysiwyg_sidebar)
    // Questa regola ha priorità sulla sidebar di default ma non sovrascrive le regole speciali sopra
    if ($current_post_type !== 'formazione' && $current_post_type !== 'scuole' && $current_post_type !== 'dottorato') {
        // Controlla all_wysiwyg_sidebar solo se non è già stata impostata una sidebar_content speciale
        if (!in_array($sidebar_content, ['eventi_form', 'tirocinio_relations', 'wysiwyg_progetto'])) {
            $all_wysiwyg_sidebar = rwmb_meta('all_wysiwyg_sidebar');
            if (!empty($all_wysiwyg_sidebar)) {
                $show_sidebar = true;
                $sidebar_content = 'wysiwyg_all'; // Indica che deve mostrare contenuto WYSIWYG da all_wysiwyg_sidebar
            }
        }
    }
    
    // Regola per eventi: di default nessuna sidebar a meno che non ci sia contenuto custom
    if ($current_post_type === 'eventi' && empty($sidebar_content)) {
        $suppress_default_sidebar = true;
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
    
    // Se non è stato ancora impostato show_sidebar
    if (!$show_sidebar) {
        if (!$suppress_default_sidebar) {
            // Mostra la sidebar predefinita per tutti gli altri casi
            $show_sidebar = true;
            $sidebar_content = 'default';
        }
    }
    
    // Mostra la sidebar se le condizioni sono soddisfatte
    if ($show_sidebar) {
        if ($sidebar_content === 'eventi_form') {
            // Sidebar speciale per eventi con WSForm
            ?>
            <div class="<?= apply_filters('bootscore/class/sidebar/col', 'col-xl-3 order-first order-xl-2'); ?>">
                <aside id="secondary" class="widget-area">
                    <button class="<?= apply_filters('bootscore/class/sidebar/button', 'd-xl-none btn btn-outline-secondary w-100 mb-4 d-flex justify-content-between align-items-center'); ?>" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
                        <?= apply_filters('bootscore/offcanvas/sidebar/button/text', __('Informazioni Evento', 'bootscore')); ?> <?= apply_filters('bootscore/icon/ellipsis-vertical', '<i class="fa-solid fa-ellipsis-vertical"></i>'); ?>
                    </button>
                    <div class="<?= apply_filters('bootscore/class/sidebar/offcanvas', 'offcanvas-xl offcanvas-end'); ?>" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
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
        } elseif ($sidebar_content === 'wysiwyg') {
            // Sidebar speciale per formazione/scuole/dottorato con contenuto WYSIWYG personalizzato (corso_wysiwyg_sidebar)
            $corso_wysiwyg_sidebar = rwmb_meta('corso_wysiwyg_sidebar');
            ?>
            <div class="<?= apply_filters('bootscore/class/sidebar/col', 'col-xl-3 order-first order-xl-2'); ?>">
                <aside id="secondary" class="widget-area">
                    <button class="<?= apply_filters('bootscore/class/sidebar/button', 'd-xl-none btn btn-outline-secondary w-100 mb-4 d-flex justify-content-between align-items-center'); ?>" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
                        <?= apply_filters('bootscore/offcanvas/sidebar/button/text', __('Informazioni', 'bootscore')); ?> <?= apply_filters('bootscore/icon/ellipsis-vertical', '<i class="fa-solid fa-ellipsis-vertical"></i>'); ?>
                    </button>
                    <div class="<?= apply_filters('bootscore/class/sidebar/offcanvas', 'offcanvas-xl offcanvas-end'); ?>" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
                        <div class="offcanvas-header <?= apply_filters('bootscore/class/offcanvas/header', '', 'sidebar'); ?>">
                            <span class="h5 offcanvas-title" id="sidebarLabel"><?= apply_filters('bootscore/offcanvas/sidebar/title', __('Informazioni', 'bootscore')); ?></span>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body flex-column <?= apply_filters('bootscore/class/offcanvas/body', '', 'sidebar'); ?>">
                            
                            <?php do_action('bootscore_before_sidebar_widgets'); ?>
                            
                            <div class="widget">
                                <!-- Contenuto WYSIWYG personalizzato -->
                                <?php echo wp_kses_post($corso_wysiwyg_sidebar); ?>
                            </div>
                            
                            <?php do_action('bootscore_after_sidebar_widgets'); ?>
                            
                        </div>
                    </div>
                </aside>
            </div>
            <?php
        } elseif ($sidebar_content === 'wysiwyg_all') {
            // Sidebar per tutti gli altri post types/pagine con contenuto WYSIWYG personalizzato (all_wysiwyg_sidebar)
            $all_wysiwyg_sidebar = rwmb_meta('all_wysiwyg_sidebar');
            ?>
            <div class="<?= apply_filters('bootscore/class/sidebar/col', 'col-xl-3 order-first order-xl-2'); ?>">
                <aside id="secondary" class="widget-area">
                    <button class="<?= apply_filters('bootscore/class/sidebar/button', 'd-xl-none btn btn-outline-secondary w-100 mb-4 d-flex justify-content-between align-items-center'); ?>" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
                        <?= apply_filters('bootscore/offcanvas/sidebar/button/text', __('Informazioni', 'bootscore')); ?> <?= apply_filters('bootscore/icon/ellipsis-vertical', '<i class="fa-solid fa-ellipsis-vertical"></i>'); ?>
                    </button>
                    <div class="<?= apply_filters('bootscore/class/sidebar/offcanvas', 'offcanvas-xl offcanvas-end'); ?>" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
                        <div class="offcanvas-header <?= apply_filters('bootscore/class/offcanvas/header', '', 'sidebar'); ?>">
                            <span class="h5 offcanvas-title" id="sidebarLabel"><?= apply_filters('bootscore/offcanvas/sidebar/title', __('Informazioni', 'bootscore')); ?></span>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body flex-column <?= apply_filters('bootscore/class/offcanvas/body', '', 'sidebar'); ?>">
                            
                            <?php do_action('bootscore_before_sidebar_widgets'); ?>
                            
                            <div class="widget">
                                <!-- Contenuto WYSIWYG personalizzato da all_wysiwyg_sidebar -->
                                <?php echo wp_kses_post($all_wysiwyg_sidebar); ?>
                            </div>
                            
                            <?php do_action('bootscore_after_sidebar_widgets'); ?>
                            
                        </div>
                    </div>
                </aside>
            </div>
            <?php
        } elseif ($sidebar_content === 'wysiwyg_progetto') {
            // Sidebar per post type progetto con contenuto WYSIWYG personalizzato
            $progetto_wysiwyg = rwmb_meta('pricerca_campo_sidebar');
            ?>
            <div class="<?= apply_filters('bootscore/class/sidebar/col', 'col-xl-3 order-first order-xl-2'); ?>">
                <aside id="secondary" class="widget-area">
                    <button class="<?= apply_filters('bootscore/class/sidebar/button', 'd-xl-none btn btn-outline-secondary w-100 mb-4 d-flex justify-content-between align-items-center'); ?>" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
                        <?= apply_filters('bootscore/offcanvas/sidebar/button/text', __('Informazioni', 'bootscore')); ?> <?= apply_filters('bootscore/icon/ellipsis-vertical', '<i class="fa-solid fa-ellipsis-vertical"></i>'); ?>
                    </button>
                    <div class="<?= apply_filters('bootscore/class/sidebar/offcanvas', 'offcanvas-xl offcanvas-end'); ?>" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
                        <div class="offcanvas-header <?= apply_filters('bootscore/class/offcanvas/header', '', 'sidebar'); ?>">
                            <span class="h5 offcanvas-title" id="sidebarLabel"><?= apply_filters('bootscore/offcanvas/sidebar/title', __('Informazioni', 'bootscore')); ?></span>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body flex-column <?= apply_filters('bootscore/class/offcanvas/body', '', 'sidebar'); ?>">
                            
                            <?php do_action('bootscore_before_sidebar_widgets'); ?>
                            
                            <div class="widget">
                                <?php echo wp_kses_post($progetto_wysiwyg); ?>
                            </div>
                            
                            <?php do_action('bootscore_after_sidebar_widgets'); ?>
                            
                        </div>
                    </div>
                </aside>
            </div>
            <?php
        } elseif ($sidebar_content === 'tirocinio_relations') {
            // Sidebar per Tirocinio con relazioni ai corsi collegati
            $rel_posts = class_exists('MB_Relationships_API') ? MB_Relationships_API::get_connected([
                'id'   => 'rel-tir-form',
                'from' => get_the_ID(),
                'type' => 'to',
            ]) : array();
            ?>
            <div class="<?= apply_filters('bootscore/class/sidebar/col', 'col-xl-3 order-first order-xl-2'); ?>">
                <aside id="secondary" class="widget-area">
                    <button class="<?= apply_filters('bootscore/class/sidebar/button', 'd-xl-none btn btn-outline-secondary w-100 mb-4 d-flex justify-content-between align-items-center'); ?>" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
                        <?= apply_filters('bootscore/offcanvas/sidebar/button/text', __('Corsi collegati', 'bootscore')); ?> <?= apply_filters('bootscore/icon/ellipsis-vertical', '<i class=\"fa-solid fa-ellipsis-vertical\"></i>'); ?>
                    </button>
                    <div class="<?= apply_filters('bootscore/class/sidebar/offcanvas', 'offcanvas-xl offcanvas-end'); ?>" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
                        <div class="offcanvas-header <?= apply_filters('bootscore/class/offcanvas/header', '', 'sidebar'); ?>">
                            <span class="h5 offcanvas-title" id="sidebarLabel"><?= apply_filters('bootscore/offcanvas/sidebar/title', __('Corsi collegati', 'bootscore')); ?></span>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body flex-column <?= apply_filters('bootscore/class/offcanvas/body', '', 'sidebar'); ?>">
                            <?php do_action('bootscore_before_sidebar_widgets'); ?>

                            <?php if (!empty($rel_posts)) : ?>
                                <div class="widget">
                                    <ul class="list-unstyled m-0">
                                    <?php foreach ($rel_posts as $rel_post) : ?>
                                        <?php $fid = $rel_post->ID; ?>
                                        <li class="mb-3 pb-3 border-bottom">
                                            <a href="<?= esc_url(get_permalink($fid)); ?>" class="fw-semibold text-decoration-none d-block mb-1"><?= esc_html(get_the_title($fid)); ?></a>
                                            <div class="small text-muted d-flex gap-2 flex-wrap">
                                                <?php 
                                                $durata = function_exists('rwmb_meta') ? rwmb_meta('corso_durata', '', $fid) : '';
                                                $cfu    = function_exists('rwmb_meta') ? rwmb_meta('corso_cfu', '', $fid) : '';
                                                if (!empty($durata)) echo '<span>' . esc_html($durata) . '</span>';
                                                if (!empty($durata) && !empty($cfu)) echo '<span>|</span>';
                                                if (!empty($cfu)) echo '<span>' . esc_html($cfu) . ' CFU</span>';
                                                ?>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

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
            <div class="<?= apply_filters('bootscore/class/sidebar/col', 'col-xl-3 order-first order-xl-2'); ?>">
                <aside id="secondary" class="widget-area">
                    <button class="<?= apply_filters('bootscore/class/sidebar/button', 'd-xl-none btn btn-outline-secondary w-100 mb-4 d-flex justify-content-between align-items-center'); ?>" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
                        <?= apply_filters('bootscore/offcanvas/sidebar/button/text', __('Richiedi informazioni', 'bootscore')); ?> <?= apply_filters('bootscore/icon/ellipsis-vertical', '<i class="fa-solid fa-ellipsis-vertical"></i>'); ?>
                    </button>
                    <div class="<?= apply_filters('bootscore/class/sidebar/offcanvas', 'offcanvas-xl offcanvas-end'); ?>" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
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

// Hook per rimuovere la sidebar nell'archivio progetti (in caso venga chiamata da altri template o hook)
add_action('wp', function() {
    if (is_post_type_archive('progetto')) {
        // Rimuovi eventuali hook che potrebbero mostrare la sidebar
        remove_action('get_sidebar', 'custom_sidebar_rules', 10);
        // Disabilita solo la sidebar principale (sidebar-1) per l'archivio progetti
        // NON disabilitare i widget di header/footer (top-bar, top-nav, top-nav-2, etc.)
        add_filter('is_active_sidebar', function($is_active_sidebar, $index) {
            if (is_post_type_archive('progetto') && $index === 'sidebar-1') {
                return false; // Disabilita solo la sidebar principale
            }
            return $is_active_sidebar;
        }, 10, 2);
    }
}, 999);
/**
 * Gestisce la visibilità della paginazione negli archivi formazione e progetto,
 * attivo solo in questi archivi e nelle loro taxonomy collegate
 * basandosi sui filtri WPGB attivi
 */
(function($) {
    'use strict';
    
    // Funzione per controllare se ci sono filtri attivi
    function hasActiveFilters() {
        // Controlla se l'URL contiene parametri _filter
        const urlParams = new URLSearchParams(window.location.search);
        for (let [key, value] of urlParams) {
            if (key.startsWith('_filter') && value !== '') {
                return true;
            }
        }
        return false;
    }
    
    // Funzione per aggiornare la visibilità della paginazione
    function updatePaginationVisibility() {
        // Selettore più specifico: solo la paginazione dentro .entry-footer, non quella dei facet
        // Nascondiamo sia il nav che contiene la paginazione, sia la ul.pagination stessa
        const $paginationNav = $('.entry-footer nav[aria-label="Page navigation"]');
        const $pagination = $('.entry-footer .pagination');
        
        const hasFilters = hasActiveFilters();
        
        if (hasFilters) {
            // Nascondi la paginazione normale quando ci sono filtri attivi
            $paginationNav.hide();
            $pagination.hide();
        } else {
            // Mostra la paginazione normale quando non ci sono filtri attivi
            $paginationNav.show();
            $pagination.show();
        }
    }
    
    // Funzione per verificare se siamo in un contesto supportato (formazione o progetto)
    function isSupportedContext() {
        return $('body').hasClass('post-type-archive-formazione') || 
               $('body').hasClass('tax-cat-formazione') || 
               $('body').hasClass('tax-area-formazione') || 
               $('body').hasClass('tax-modalita-formazione') ||
               $('body').hasClass('post-type-archive-progetto') || 
               $('body').hasClass('tax-cat-progetto');
    }
    
    // Esegui al caricamento della pagina
    $(document).ready(function() {
        // Negli archivi formazione/progetto e nelle loro taxonomy
        if (isSupportedContext()) {
            updatePaginationVisibility();
            
            // Monitora i cambiamenti nei filtri WPGB
            $(document).on('change', '.wpgb-select, .wpgb-checkbox, .wpgb-radio', function() {
                // Piccolo delay per permettere l'aggiornamento dell'URL
                setTimeout(updatePaginationVisibility, 100);
            });
            
            // Monitora il pulsante reset
            $(document).on('click', '.wpgb-reset', function() {
                setTimeout(updatePaginationVisibility, 100);
            });
            
            // Monitora i cambiamenti nell'URL (per filtri AJAX)
            let currentUrl = window.location.href;
            setInterval(function() {
                if (window.location.href !== currentUrl) {
                    currentUrl = window.location.href;
                    updatePaginationVisibility();
                }
            }, 500);
        }
    });
    
})(jQuery);
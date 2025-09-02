/**
 * Gestisce la visibilità della paginazione nell'archivio formazione, attivo solo in questo archivo e taxonomy collegata
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
        const $pagination = $('.pagination');
        const $paginationParent = $pagination.closest('.entry-footer');
        
        if (hasActiveFilters()) {
            // Nascondi la paginazione normale quando ci sono filtri attivi
            $pagination.hide();
        } else {
            // Mostra la paginazione normale quando non ci sono filtri attivi
            $pagination.show();
        }
    }
    
    // Esegui al caricamento della pagina
    $(document).ready(function() {
        // Nell'archivio formazione e nelle sue taxonomy
        if ($('body').hasClass('post-type-archive-formazione') || 
            $('body').hasClass('tax-cat-formazione') || 
            $('body').hasClass('tax-area-formazione') || 
            $('body').hasClass('tax-modalita-formazione')) {
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
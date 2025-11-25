/**
 * Sticky buttons per pagine formazione (mobile only)
 * 
 * Da 991px in giù:
 * - Button "Richiedi informazioni" della sidebar → sticky sotto header a sinistra
 * - Link "Iscriviti" → sticky sotto header a destra
 * 
 * @package Bootscore Child
 */

document.addEventListener('DOMContentLoaded', function() {
    // Verifica se siamo su una pagina formazione (single)
    function isFormazionePage() {
        return document.body.classList.contains('single-formazione') ||
               document.body.classList.contains('post-type-archive-formazione') ||
               (document.querySelector('.single-formazione') !== null);
    }
    
    // Verifica se siamo in mobile
    function isMobile() {
        return window.innerWidth < 992;
    }
    
    // Se non siamo su formazione o desktop, esci
    if (!isFormazionePage() || !isMobile()) {
        return;
    }
    
    // Elementi da rendere sticky
    let sidebarButton = null;
    let iscrizioneButton = null;
    
    // Container sticky per i pulsanti
    let stickyContainer = null;
    let sidebarButtonClone = null;
    let iscrizioneButtonClone = null;
    
    // Stato sticky
    let sidebarButtonSticky = false;
    let iscrizioneButtonSticky = false;
    
    // Trova gli elementi originali
    function findElements() {
        // Trova il button della sidebar (quello che apre l'offcanvas con "Richiedi informazioni")
        const sidebarButtonSelector = '#secondary button[data-bs-toggle="offcanvas"][data-bs-target="#sidebar"]';
        sidebarButton = document.querySelector(sidebarButtonSelector);
        
        // Trova il link "Iscriviti" (può avere classi diverse, cerchiamo per href o testo)
        const iscrizioneSelectors = [
            'a.btn-iscrizioni-formazione',
            'a[href*="platform.unimarconi.it/shibd"]',
            'a:contains("Iscriviti")'
        ];
        
        for (let selector of iscrizioneSelectors) {
            iscrizioneButton = document.querySelector(selector);
            if (iscrizioneButton) break;
        }
        
        // Fallback: cerca per testo contenuto
        if (!iscrizioneButton) {
            const allLinks = document.querySelectorAll('a');
            for (let link of allLinks) {
                if (link.textContent.trim() === 'Iscriviti' && link.href.includes('platform.unimarconi.it')) {
                    iscrizioneButton = link;
                    break;
                }
            }
        }
    }
    
    // Aggiorna la posizione del container sticky
    function updateStickyContainerPosition() {
        if (!stickyContainer) return;
        
        const header = document.querySelector('#masthead');
        if (header) {
            const headerRect = header.getBoundingClientRect();
            const headerHeight = headerRect.height;
            
            // L'header è sticky-top, quindi rimane sempre in posizione 0 durante lo scroll
            stickyContainer.style.top = headerHeight + 'px';
        }
    }
    
    // Crea il container sticky sotto l'header
    function createStickyContainer() {
        if (stickyContainer) return;
        
        const header = document.querySelector('#masthead');
        if (!header) return;
        
        stickyContainer = document.createElement('div');
        stickyContainer.className = 'formazione-sticky-buttons-container';
        
        // Inserisci dopo l'header
        header.parentNode.insertBefore(stickyContainer, header.nextSibling);
        
        // Calcola la posizione iniziale
        updateStickyContainerPosition();
    }
    
    // Gestisci sticky per sidebar button (sinistra)
    function handleSidebarButtonSticky() {
        if (!sidebarButton || !stickyContainer) return;
        
        const rect = sidebarButton.getBoundingClientRect();
        const header = document.querySelector('#masthead');
        if (!header) return;
        
        const headerHeight = header.getBoundingClientRect().height;
        const buttonTop = rect.top;
        
        // Se il button è sopra o oltre la posizione sticky, renderlo sticky
        if (buttonTop <= headerHeight && !sidebarButtonSticky) {
            // Crea clone e nascondi originale
            sidebarButtonClone = sidebarButton.cloneNode(true);
            sidebarButtonClone.classList.add('formazione-sticky-sidebar-btn');
            sidebarButton.style.visibility = 'hidden'; // Nascondi ma mantieni spazio
            sidebarButton.style.opacity = '0';
            
            // Aggiungi al container sticky (sinistra)
            stickyContainer.appendChild(sidebarButtonClone);
            stickyContainer.classList.add('has-sidebar-btn');
            
            sidebarButtonSticky = true;
            
            // Riattacca eventi al clone
            attachButtonEvents(sidebarButtonClone, sidebarButton);
        } 
        // Se il button è tornato visibile, rimuovi sticky
        else if (buttonTop > headerHeight + 50 && sidebarButtonSticky) {
            if (sidebarButtonClone) {
                sidebarButtonClone.remove();
                sidebarButtonClone = null;
            }
            sidebarButton.style.visibility = '';
            sidebarButton.style.opacity = '';
            stickyContainer.classList.remove('has-sidebar-btn');
            sidebarButtonSticky = false;
        }
    }
    
    // Gestisci sticky per iscrizione button (destra)
    function handleIscrizioneButtonSticky() {
        if (!iscrizioneButton || !stickyContainer) return;
        
        const rect = iscrizioneButton.getBoundingClientRect();
        const header = document.querySelector('#masthead');
        if (!header) return;
        
        const headerHeight = header.getBoundingClientRect().height;
        const buttonTop = rect.top;
        
        // Se il button è sopra o oltre la posizione sticky, renderlo sticky
        if (buttonTop <= headerHeight && !iscrizioneButtonSticky) {
            // Crea clone e nascondi originale
            iscrizioneButtonClone = iscrizioneButton.cloneNode(true);
            iscrizioneButtonClone.classList.add('formazione-sticky-iscrizione-btn');
            iscrizioneButton.style.visibility = 'hidden';
            iscrizioneButton.style.opacity = '0';
            
            // Aggiungi al container sticky (destra)
            stickyContainer.appendChild(iscrizioneButtonClone);
            stickyContainer.classList.add('has-iscrizione-btn');
            
            iscrizioneButtonSticky = true;
            
            // Riattacca eventi al clone
            attachButtonEvents(iscrizioneButtonClone, iscrizioneButton);
        } 
        // Se il button è tornato visibile, rimuovi sticky
        else if (buttonTop > headerHeight + 50 && iscrizioneButtonSticky) {
            if (iscrizioneButtonClone) {
                iscrizioneButtonClone.remove();
                iscrizioneButtonClone = null;
            }
            iscrizioneButton.style.visibility = '';
            iscrizioneButton.style.opacity = '';
            stickyContainer.classList.remove('has-iscrizione-btn');
            iscrizioneButtonSticky = false;
        }
    }
    
    // Riattacca eventi ai cloni
    function attachButtonEvents(clone, original) {
        // Per il button sidebar (deve aprire l'offcanvas)
        if (clone.hasAttribute('data-bs-toggle') && clone.hasAttribute('data-bs-target')) {
            clone.addEventListener('click', function(e) {
                e.preventDefault();
                const targetSelector = clone.getAttribute('data-bs-target');
                const offcanvas = document.querySelector(targetSelector);
                if (offcanvas) {
                    const bsOffcanvas = new bootstrap.Offcanvas(offcanvas);
                    bsOffcanvas.show();
                }
            });
        }
        // Per il link iscrizione (deve aprire il link)
        else if (clone.tagName === 'A') {
            clone.addEventListener('click', function(e) {
                // Il link si apre normalmente
            });
        }
    }
    
    // Funzione principale per gestire lo scroll
    function handleScroll() {
        if (!isMobile()) {
            // Se siamo passati a desktop, rimuovi tutto
            cleanup();
            return;
        }
        
        handleSidebarButtonSticky();
        handleIscrizioneButtonSticky();
    }
    
    // Cleanup: rimuovi sticky e ripristina originali
    function cleanup() {
        if (sidebarButtonClone) {
            sidebarButtonClone.remove();
            sidebarButtonClone = null;
        }
        if (iscrizioneButtonClone) {
            iscrizioneButtonClone.remove();
            iscrizioneButtonClone = null;
        }
        if (stickyContainer) {
            stickyContainer.remove();
            stickyContainer = null;
        }
        if (sidebarButton) {
            sidebarButton.style.visibility = '';
            sidebarButton.style.opacity = '';
        }
        if (iscrizioneButton) {
            iscrizioneButton.style.visibility = '';
            iscrizioneButton.style.opacity = '';
        }
        sidebarButtonSticky = false;
        iscrizioneButtonSticky = false;
    }
    
    // Inizializzazione
    function init() {
        findElements();
        
        if (!sidebarButton && !iscrizioneButton) {
            // Nessun elemento trovato, esci
            return;
        }
        
        createStickyContainer();
        handleScroll();
        
        // Listener per scroll
        let scrollTimer;
        window.addEventListener('scroll', function() {
            clearTimeout(scrollTimer);
            scrollTimer = setTimeout(handleScroll, 10);
        });
        
        // Listener per resize (per gestire passaggio mobile/desktop)
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (!isMobile() || !isFormazionePage()) {
                    cleanup();
                } else {
                    // Ricrea se necessario
                    if (!stickyContainer) {
                        createStickyContainer();
                    }
                    updateStickyContainerPosition();
                    handleScroll();
                }
            }, 250);
        });
    }
    
    // Avvia dopo un breve delay per assicurarsi che il DOM sia completo
    setTimeout(init, 100);
});


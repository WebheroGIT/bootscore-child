document.addEventListener('DOMContentLoaded', function() {
    // Elementi da spostare
    const myUniButton = document.querySelector('.btn-my-container');
    const searchToggle = document.querySelector('.search-toggle-wrapper');
    const mobileContainer = document.querySelector('.mobile-actions-container');
    
    // Trova l'offcanvas-body dove inserire il btn-my-container (prima del menu)
    const offcanvasBody = document.querySelector('#offcanvas-navbar .offcanvas-body');
    const bootscoreNavbar = document.querySelector('#bootscore-navbar');
    
    // Trova il logo bianco nel top-bar-widget (quello con logo_unimarconi_bianco.svg)
    const topBarWidget = document.querySelector('.top-bar-widget');
    const whiteLogo = topBarWidget ? topBarWidget.querySelector('img[src*="logo_unimarconi_bianco.svg"]') : null;
    const whiteLogoContainer = whiteLogo ? whiteLogo.closest('div') : null;
    
    // Elementi originali parent per ripristino
    const originalMyUniParent = myUniButton?.parentElement;
    const originalSearchParent = searchToggle?.parentElement;
    
    // Clona gli elementi per mantenere l'originale
    let logoClone = null;
    let myUniOffcanvasClone = null; // Clone separato per l'offcanvas (spostato dentro il menu)
    let myUniFixedClone = null; // Clone separato per il pulsante fixed sotto header (solo home, solo mobile)
    let searchClone = null;
    
    // Verifica se siamo sulla home page
    function isHomePage() {
        return document.body.classList.contains('home') || 
               window.location.pathname === '/' || 
               window.location.pathname === '/index.php' ||
               (document.querySelector('body.home') !== null);
    }
    
    function handleResponsiveElements() {
        const isMobile = window.innerWidth < 992;
        const isHome = isHomePage();
        
        if (isMobile) {
            // Se siamo in mobile e non abbiamo già spostato gli elementi
            // PRIMA spostiamo il logo nel mobile-actions-container (deve essere il più a sinistra)
            if (mobileContainer && !logoClone && whiteLogoContainer) {
                // Clona e nascondi originale
                logoClone = whiteLogoContainer.cloneNode(true);
                whiteLogoContainer.style.display = 'none';
                // Inserisci per primo (più a sinistra)
                mobileContainer.insertBefore(logoClone, mobileContainer.firstChild);
            }
            
            // Search toggle va nel mobile-actions-container
            if (mobileContainer && !searchClone && searchToggle) {
                // Clona e nascondi originale
                searchClone = searchToggle.cloneNode(true);
                searchToggle.style.display = 'none';
                mobileContainer.appendChild(searchClone);
                
                // Riattacca eventi al clone della ricerca
                attachSearchEvents(searchClone);
            }
            
            // Pulsante MyUniMarconi fixed sotto header (solo home, solo mobile)
            if (isHome && !myUniFixedClone && myUniButton) {
                const header = document.querySelector('#masthead');
                if (header) {
                    // Clona il pulsante
                    myUniFixedClone = myUniButton.cloneNode(true);
                    myUniFixedClone.classList.add('btn-my-fixed-home');
                    
                    // Crea un container per il pulsante fixed
                    const fixedContainer = document.createElement('div');
                    fixedContainer.className = 'btn-my-fixed-container';
                    fixedContainer.appendChild(myUniFixedClone);
                    
                    // Inserisci dopo l'header
                    header.parentNode.insertBefore(fixedContainer, header.nextSibling);
                    
                    // Calcola e imposta la posizione top (sotto l'header)
                    updateFixedButtonPosition();
                }
            }
            
            // Aggiorna la posizione del pulsante fixed se presente
            if (myUniFixedClone && isHome) {
                updateFixedButtonPosition();
            }
        } else {
            // Se siamo in desktop, rimuovi cloni e mostra SEMPRE gli originali
            if (logoClone) {
                logoClone.remove();
                logoClone = null;
            }
            // IMPORTANTE: Ripristina SEMPRE il logo bianco su desktop, anche se non c'era un clone
            if (whiteLogoContainer) {
                whiteLogoContainer.style.display = '';
                whiteLogoContainer.style.visibility = '';
                whiteLogoContainer.style.opacity = '';
            }
            
            if (myUniOffcanvasClone) {
                myUniOffcanvasClone.remove();
                myUniOffcanvasClone = null;
            }
            // IMPORTANTE: Ripristina SEMPRE il pulsante MyUni su desktop
            if (myUniButton) {
                myUniButton.style.display = '';
                myUniButton.style.visibility = '';
                myUniButton.style.opacity = '';
            }
            
            // Rimuovi il pulsante fixed se presente
            if (myUniFixedClone) {
                const fixedContainer = myUniFixedClone.closest('.btn-my-fixed-container');
                if (fixedContainer) {
                    fixedContainer.remove();
                }
                myUniFixedClone = null;
            }
            
            if (searchClone) {
                searchClone.remove();
                searchClone = null;
            }
            // IMPORTANTE: Ripristina SEMPRE la ricerca su desktop
            if (searchToggle) {
                searchToggle.style.display = '';
                searchToggle.style.visibility = '';
                searchToggle.style.opacity = '';
            }
        }
        
        // Se non siamo più sulla home, rimuovi il pulsante fixed
        if (!isHome && myUniFixedClone) {
            const fixedContainer = myUniFixedClone.closest('.btn-my-fixed-container');
            if (fixedContainer) {
                fixedContainer.remove();
            }
            myUniFixedClone = null;
        }
    }
    
    // Funzione per aggiornare la posizione del pulsante fixed sotto l'header
    function updateFixedButtonPosition() {
        if (!myUniFixedClone) return;
        
        const fixedContainer = myUniFixedClone.closest('.btn-my-fixed-container');
        if (!fixedContainer) return;
        
        const header = document.querySelector('#masthead');
        if (header) {
            // L'header è sticky-top, quindi rimane sempre in posizione 0 durante lo scroll
            // Il pulsante deve essere sempre a headerHeight pixel dall'alto
            const headerRect = header.getBoundingClientRect();
            const headerHeight = headerRect.height;
            
            // Non aggiungiamo scrollY perché l'header è sticky e non si muove
            fixedContainer.style.top = headerHeight + 'px';
        }
    }
    
    // Funzione per riattaccare gli eventi di ricerca al clone
    function attachSearchEvents(clonedSearch) {
        const toggleBtn = clonedSearch.querySelector('.search-toggle');
        const overlay = clonedSearch.querySelector('.search-form-overlay');
        
        if (toggleBtn && overlay) {
            toggleBtn.addEventListener('click', function() {
                overlay.classList.toggle('d-none');
            });
            
            // Chiudi con click fuori
            document.addEventListener('click', function(e) {
                if (!clonedSearch.contains(e.target)) {
                    overlay.classList.add('d-none');
                }
            });
        }
    }
    
    // Funzione per verificare che il menu sia presente e spostare btn-my-container
    function ensureMyUniButtonInOffcanvas() {
        const offcanvasBody = document.querySelector('#offcanvas-navbar .offcanvas-body');
        const bootscoreNavbar = document.querySelector('#bootscore-navbar');
        
        // IMPORTANTE: Ripristina sempre gli elementi su desktop PRIMA di controllare mobile
        if (window.innerWidth >= 992) {
            // Su desktop, assicurati che il pulsante originale sia visibile
            if (myUniOffcanvasClone) {
                myUniOffcanvasClone.remove();
                myUniOffcanvasClone = null;
            }
            if (myUniButton) {
                myUniButton.style.display = '';
            }
            return; // Esci perché non dobbiamo fare nulla su desktop
        }
        
        // Se siamo in mobile e il menu è presente ma il clone non esiste ancora
        if (window.innerWidth < 992 && offcanvasBody && bootscoreNavbar && !myUniOffcanvasClone && myUniButton) {
            // Sposta btn-my-container nell'offcanvas-body, prima del menu
            myUniOffcanvasClone = myUniButton.cloneNode(true);
            myUniButton.style.display = 'none';
            // Inserisci prima del menu (#bootscore-navbar), allineato a sinistra come il menu
            offcanvasBody.insertBefore(myUniOffcanvasClone, bootscoreNavbar);
        }
    }
    
    // IMPORTANTE: PRIMA di tutto, su desktop ripristina SEMPRE tutti gli elementi
    // Questo previene che handleResponsiveElements() nasconda elementi su desktop
    if (window.innerWidth >= 992) {
        // Trova e ripristina tutti gli elementi possibili su desktop
        const allTopBarWidgets = document.querySelectorAll('.top-bar-widget');
        allTopBarWidgets.forEach(function(widget) {
            widget.style.display = '';
            widget.style.visibility = '';
            widget.style.opacity = '';
        });
        
        const allSearchToggles = document.querySelectorAll('.search-toggle-wrapper');
        allSearchToggles.forEach(function(toggle) {
            toggle.style.display = '';
            toggle.style.visibility = '';
            toggle.style.opacity = '';
        });
        
        const allMyUniButtons = document.querySelectorAll('.btn-my-container');
        allMyUniButtons.forEach(function(btn) {
            btn.style.display = '';
            btn.style.visibility = '';
            btn.style.opacity = '';
        });
        
        // Se whiteLogoContainer esiste, ripristinalo
        if (whiteLogoContainer) {
            whiteLogoContainer.style.display = '';
            whiteLogoContainer.style.visibility = '';
            whiteLogoContainer.style.opacity = '';
        }
        
        if (searchToggle) {
            searchToggle.style.display = '';
            searchToggle.style.visibility = '';
            searchToggle.style.opacity = '';
        }
        
        if (myUniButton) {
            myUniButton.style.display = '';
            myUniButton.style.visibility = '';
            myUniButton.style.opacity = '';
        }
    }
    
    // POI esegui la logica responsive (che gestirà mobile)
    handleResponsiveElements();
    
    // Controlla se il menu è già presente, altrimenti riprova dopo un breve delay (solo mobile)
    if (window.innerWidth < 992) {
        if (!document.querySelector('#bootscore-navbar')) {
            // Se il menu non è presente, riprova dopo che il DOM è completamente caricato
            setTimeout(() => {
                ensureMyUniButtonInOffcanvas();
                updateFixedButtonPosition();
            }, 100);
        } else {
            ensureMyUniButtonInOffcanvas();
            updateFixedButtonPosition();
        }
    } else {
        // Su desktop, assicurati che il pulsante non sia nascosto
        ensureMyUniButtonInOffcanvas();
        
        // Ripristina ancora una volta per sicurezza
        if (whiteLogoContainer) whiteLogoContainer.style.display = '';
        if (searchToggle) searchToggle.style.display = '';
        if (myUniButton) myUniButton.style.display = '';
    }
    
    // Aggiorna la posizione del pulsante fixed dopo che tutto è caricato
    setTimeout(() => {
        if (isHomePage() && window.innerWidth < 992) {
            updateFixedButtonPosition();
        }
        
        // ULTIMO CONTROLLO: Ripristina SEMPRE tutto su desktop dopo un breve delay
        // Questo previene eventuali problemi di timing o altri script che nascondono elementi
        if (window.innerWidth >= 992) {
            const allTopBarWidgets = document.querySelectorAll('.top-bar-widget');
            allTopBarWidgets.forEach(function(widget) {
                widget.style.display = '';
                widget.style.visibility = '';
                widget.style.opacity = '';
            });
            
            const allSearchToggles = document.querySelectorAll('.search-toggle-wrapper');
            allSearchToggles.forEach(function(toggle) {
                toggle.style.display = '';
                toggle.style.visibility = '';
                toggle.style.opacity = '';
            });
            
            const allMyUniButtons = document.querySelectorAll('.btn-my-container');
            allMyUniButtons.forEach(function(btn) {
                btn.style.display = '';
                btn.style.visibility = '';
                btn.style.opacity = '';
            });
        }
    }, 200);
    
    // Esegui al resize con debounce
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            handleResponsiveElements();
            ensureMyUniButtonInOffcanvas();
            updateFixedButtonPosition();
        }, 250);
    });
    
    // Nota: Non c'è bisogno di aggiornare durante lo scroll perché l'header è sticky
    // e rimane sempre in posizione fissa, quindi il pulsante mantiene sempre la stessa
    // posizione relativa all'header
});
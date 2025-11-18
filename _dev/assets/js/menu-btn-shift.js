document.addEventListener('DOMContentLoaded', function() {
    // Elementi da spostare
    const myUniButton = document.querySelector('.btn-my-container');
    const searchToggle = document.querySelector('.search-toggle-wrapper');
    const mobileContainer = document.querySelector('.mobile-actions-container');
    
    // Elementi originali parent per ripristino
    const originalMyUniParent = myUniButton?.parentElement;
    const originalSearchParent = searchToggle?.parentElement;
    
    // Clona gli elementi per mantenere l'originale
    let myUniClone = null;
    let searchClone = null;
    
    function handleResponsiveElements() {
        const isMobile = window.innerWidth < 992;
        
        if (isMobile && mobileContainer) {
            // Se siamo in mobile e non abbiamo già spostato gli elementi
            if (!myUniClone && myUniButton) {
                // Clona e nascondi originale
                myUniClone = myUniButton.cloneNode(true);
                myUniButton.style.display = 'none';
                mobileContainer.appendChild(myUniClone);
            }
            
            if (!searchClone && searchToggle) {
                // Clona e nascondi originale
                searchClone = searchToggle.cloneNode(true);
                searchToggle.style.display = 'none';
                mobileContainer.appendChild(searchClone);
                
                // Riattacca eventi al clone della ricerca
                attachSearchEvents(searchClone);
            }
        } else {
            // Se siamo in desktop, rimuovi cloni e mostra originali
            if (myUniClone) {
                myUniClone.remove();
                myUniClone = null;
                if (myUniButton) myUniButton.style.display = '';
            }
            
            if (searchClone) {
                searchClone.remove();
                searchClone = null;
                if (searchToggle) searchToggle.style.display = '';
            }
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
    
    // Esegui al caricamento
    handleResponsiveElements();
    
    // Esegui al resize con debounce
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(handleResponsiveElements, 250);
    });
});
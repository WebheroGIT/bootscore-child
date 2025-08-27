/**
 * Platform Course Interceptor Module
 * Intercetta i link /externalplatformprogram/ e mostra un popup con contenuto dalla piattaforma interna
 */

(function() {
    'use strict';

    // Configurazione
    const config = {
        externalPattern: '/externalplatformprogram/',
        platformBaseUrl: 'https://platform.unimarconi.it/chairprogram/',
        proxyUrl: '/course-proxy.php', // Percorso corretto nella root del sito
        popupId: 'platform-course-popup',
        overlayId: 'platform-course-overlay',
        debugMode: true
    };

    // Logger
    function log(...messages) {
        if (config.debugMode) {
            console.log('[Platform Interceptor]', ...messages);
        }
    }

    // Stato dell'applicazione
    const state = {
        currentPopup: null,
        isLoading: false
    };

    /**
     * Inizializzazione
     */
    function init() {
        log('Initializing platform interceptor');
        
        // Attendi che il DOM sia pronto
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupInterceptors);
        } else {
            setupInterceptors();
        }
    }

    /**
     * Configura gli interceptor per i link
     */
    function setupInterceptors() {
        log('Setting up link interceptors');
        
        // Intercetta i click sui link con pattern specifico
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (!link) return;
            
            const href = link.getAttribute('href');
            if (!href || !href.includes(config.externalPattern)) return;
            
            log('Intercepted link click:', href);
            e.preventDefault();
            e.stopPropagation();
            
            // Estrai l'ID del corso
            const courseId = extractCourseId(href);
            if (!courseId) {
                log('Could not extract course ID from:', href);
                return;
            }
            
            // Mostra il popup con i dati del corso
            showCoursePopup(courseId, link);
        }, true); // useCapture = true per intercettare prima di altri handler
        
        // Gestione tasti
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && state.currentPopup) {
                closePopup();
            }
        });
    }

    /**
     * Estrae l'ID del corso dall'URL
     */
    function extractCourseId(href) {
        const match = href.match(new RegExp(config.externalPattern + '(\\d+)'));
        return match ? match[1] : null;
    }

    /**
     * Mostra il popup del corso
     */
    function showCoursePopup(courseId, triggerElement) {
        log('Showing popup for course ID:', courseId);
        
        // Chiudi popup esistente
        if (state.currentPopup) {
            closePopup();
        }
        
        // Crea il popup
        createPopup(courseId);
        
        // Carica i dati del corso
        loadCourseData(courseId);
    }

    /**
     * Crea la struttura HTML del popup
     */
    function createPopup(courseId) {
        log('Creating popup structure');
        
        // Rimuovi popup esistenti
        removeExistingPopups();
        
        // Crea overlay
        const overlay = document.createElement('div');
        overlay.id = config.overlayId;
        overlay.className = 'platform-popup-overlay';
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                closePopup();
            }
        });
        
        // Crea popup
        const popup = document.createElement('div');
        popup.id = config.popupId;
        popup.className = 'platform-popup';
        popup.setAttribute('role', 'dialog');
        popup.setAttribute('aria-modal', 'true');
        popup.setAttribute('aria-labelledby', 'popup-title');
        
        // Header del popup
        const header = document.createElement('div');
        header.className = 'platform-popup-header';
        header.innerHTML = `
            <h3 id="popup-title">Informazioni Corso</h3>
            <button type="button" class="platform-popup-close" aria-label="Chiudi popup">&times;</button>
        `;
        
        // Contenuto del popup
        const content = document.createElement('div');
        content.className = 'platform-popup-content';
        content.innerHTML = `
            <div class="platform-loading">
                <div class="platform-spinner"></div>
                <p>Caricamento informazioni corso...</p>
                <p class="platform-url">Caricando da: ${config.platformBaseUrl}${courseId}</p>
            </div>
        `;
        
        // Footer del popup
        const footer = document.createElement('div');
        footer.className = 'platform-popup-footer';
        footer.innerHTML = `
            <button type="button" class="platform-btn platform-btn-secondary" onclick="closePlatformPopup()">Chiudi</button>
        `;
        
        // Assembla il popup
        popup.appendChild(header);
        popup.appendChild(content);
        popup.appendChild(footer);
        overlay.appendChild(popup);
        
        // Aggiungi al DOM
        document.body.appendChild(overlay);
        
        // Event listener per il pulsante di chiusura
        header.querySelector('.platform-popup-close').addEventListener('click', closePopup);
        
        // Aggiungi CSS se non presente
        addPopupStyles();
        
        // Focus management
        popup.focus();
        
        // Salva riferimento
        state.currentPopup = {
            overlay: overlay,
            popup: popup,
            courseId: courseId
        };
        
        // Animazione di apertura
        setTimeout(() => {
            overlay.classList.add('show');
        }, 10);
    }

    /**
     * Carica i dati del corso dalla piattaforma
     */
    function loadCourseData(courseId) {
        log('Loading course data for ID:', courseId);
        
        if (state.isLoading) return;
        state.isLoading = true;
        
        // Usa il proxy PHP invece della chiamata diretta
        const proxyUrl = `${config.proxyUrl}?course_id=${courseId}`;
        
        fetch(proxyUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            log('Course data loaded:', data);
            
            // Controlla se ci sono errori nel proxy
            if (data.error) {
                throw new Error(data.details || data.error);
            }
            
            displayCourseData(data);
        })
        .catch(error => {
            log('Error loading course data:', error);
            showErrorMessage(error.message);
        })
        .finally(() => {
            state.isLoading = false;
        });
    }

    /**
     * Visualizza i dati del corso nel popup
     */
    function displayCourseData(data) {
        log('Displaying course data');
        
        const content = document.querySelector('.platform-popup-content');
        const title = document.querySelector('#popup-title');
        
        if (!content) return;
        
        // Aggiorna il titolo del popup
        const courseData = data.program || data; // Supporta sia la struttura con program che quella diretta
        if (title && courseData.chair_short_title) {
            title.textContent = courseData.chair_short_title;
        }
        
        // Decodifica HTML entities e JSON escape sequences
        const decodeHTML = (html) => {
            if (!html || typeof html !== 'string') return '';
            const txt = document.createElement('textarea');
            // Prima decodifica le sequenze JSON escape
            const unescaped = html.replace(/\\u([0-9a-fA-F]{4})/g, (match, grp) => 
                String.fromCharCode(parseInt(grp, 16))
            ).replace(/\\"/g, '"').replace(/\\'/g, "'").replace(/\\\\/g, '\\');
            txt.innerHTML = unescaped;
            return txt.value;
        };
        
        // Costruisci il contenuto HTML
        const courseHTML = `
            <div class="modal-body">
                <div class="course-detail-container">
                    <div class="course-detail-container">
                        <h5 class="course-detail-content">Insegnamento</h5>
                        <p class="course-teacher-content">${courseData.chair_short_title || 'N/A'}</p>
                    </div>
                    
                    <div class="course-detail-teacher-container">
                        <h5 class="course-detail-teacher-title">Docente</h5>
                        <a class="course-detail-teacher-content" href="#" data-teacher="${courseData.teacher_id || ''}">${courseData.teacher ? 'Prof. ' + courseData.teacher : 'N/A'}</a>
                    </div>
                    
                    <div class="course-detail-sector-container">
                        <h5 class="course-detail-sector-title">Settore scientifico Disciplinare</h5>
                        <p class="course-detail-sector-content">${courseData.ssd || 'N/A'}</p>
                    </div>
                    
                    <div class="course-detail-cfu-container">
                        <h5 class="course-detail-cfu-title">CFU</h5>
                        <p class="course-detail-cfu-content">${courseData.credits || 'N/A'}</p>
                    </div>
                    
                    ${courseData.description ? `
                    <div class="course-detail-description-container">
                        <h5 class="course-detail-description-title">Descrizione dell'insegnamento</h5>
                        <p class="course-detail-description-content">${decodeHTML(courseData.description)}</p>
                    </div>
                    ` : ''}
                    
                    ${courseData.didactic_objectives ? `
                    <div class="course-detail-objectives-container">
                        <h5 class="course-detail">Obiettivi formativi (espressi come risultati di apprendimento attesi)</h5>
                        <p class="course-detail-objectives-content">${decodeHTML(courseData.didactic_objectives)}</p>
                    </div>
                    ` : ''}
                    
                    ${courseData.prerequisites ? `
                    <div class="course-detail-prerequisites-container">
                        <h5 class="course-detail-prerequisites-title">Prerequisiti</h5>
                        <p class="course-detail-prerequisites-content">${decodeHTML(courseData.prerequisites)}</p>
                    </div>
                    ` : ''}
                    
                    ${courseData.program ? `
                    <div class="course-detail-content-container">
                        <h5 class="course-detail-content-title">Contenuti dell'insegnamento</h5>
                        <p class="course-detail-content-content">${decodeHTML(courseData.program)}</p>
                    </div>
                    ` : ''}
                    
                    ${courseData.didactic_activities ? `
                    <div class="course-detail-activities-container">
                        <h5 class="course-detail-activities-title">Attività didattiche</h5>
                        <p class="course-detail-activities-content">${decodeHTML(courseData.didactic_activities)}</p>
                    </div>
                    ` : ''}
                    
                    ${courseData.evaluation_criteria ? `
                    <div class="course-detail-criteria-container">
                        <h5 class="course-detail-criteria-title">Criteri di valutazione</h5>
                        <p class="course-detail-criteria-content">${decodeHTML(courseData.evaluation_criteria)}</p>
                    </div>
                    ` : ''}
                    
                    ${courseData.learning_check ? `
                    <div class="course-detail-verification-container">
                        <h5 class="course-detail-verification-title">Modalità di verifica dell'apprendimento</h5>
                        <p class="course-detail-verification-content">${decodeHTML(courseData.learning_check)}</p>
                    </div>
                    ` : ''}
                    
                    ${courseData.books ? `
                    <div class="course-detail-books-container">
                        <h5 class="course-detail-books-title">Libri di testo</h5>
                        <p class="course-detail-books-content">${decodeHTML(courseData.books)}</p>
                    </div>
                    ` : ''}
                    
                    ${courseData.student_reception ? `
                    <div class="course-detail-receiving-container">
                        <h5 class="course-detail-receiving-title">Ricevimento studenti</h5>
                        <p class="course-detail-receiving-content">${decodeHTML(courseData.student_reception)}</p>
                    </div>
                    ` : ''}
                </div>
            </div>
        `;
        
        content.innerHTML = courseHTML;
    }

    /**
     * Mostra un messaggio di errore
     */
    function showErrorMessage(errorMessage) {
        const content = document.querySelector('.platform-popup-content');
        if (content) {
            content.innerHTML = `
                <div class="platform-error">
                    <h4>Errore nel caricamento</h4>
                    <p>Non è stato possibile caricare le informazioni del corso.</p>
                    <p class="platform-error-details">Dettagli: ${errorMessage}</p>
                    <p><small>Riprova più tardi o contatta l'assistenza tecnica.</small></p>
                </div>
            `;
        }
    }

    /**
     * Chiude il popup
     */
    function closePopup() {
        if (!state.currentPopup) return;
        
        log('Closing popup');
        
        const overlay = state.currentPopup.overlay;
        overlay.classList.remove('show');
        
        setTimeout(() => {
            if (overlay.parentNode) {
                overlay.parentNode.removeChild(overlay);
            }
            state.currentPopup = null;
            state.isLoading = false;
        }, 300);
    }

    /**
     * Rimuove popup esistenti
     */
    function removeExistingPopups() {
        const existingOverlay = document.getElementById(config.overlayId);
        if (existingOverlay) {
            existingOverlay.remove();
        }
    }

    /**
     * Aggiunge gli stili CSS per il popup
     */
    function addPopupStyles() {
        if (document.getElementById('platform-popup-styles')) return;
        
        const style = document.createElement('style');
        style.id = 'platform-popup-styles';
        style.textContent = `
            .platform-popup-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.7);
                z-index: 999999;
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                transition: opacity 0.3s ease;
            }
            
            .platform-popup-overlay.show {
                opacity: 1;
            }
            
            .platform-popup {
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
                max-width: 600px;
                width: 90%;
                max-height: 80vh;
                overflow-y: auto;
                transform: scale(0.9);
                transition: transform 0.3s ease;
            }
            
            .platform-popup-overlay.show .platform-popup {
                transform: scale(1);
            }
            
            .platform-popup-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 20px;
                border-bottom: 1px solid #eee;
            }
            
            .platform-popup-header h3 {
                margin: 0;
                color: #333;
            }
            
            .platform-popup-close {
                background: none;
                border: none;
                font-size: 24px;
                cursor: pointer;
                color: #666;
                padding: 0;
                width: 30px;
                height: 30px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .platform-popup-close:hover {
                color: #000;
            }
            
            .platform-popup-content {
                padding: 20px;
                min-height: 200px;
            }
            
            .platform-loading {
                text-align: center;
                padding: 40px 20px;
            }
            
            .platform-spinner {
                width: 40px;
                height: 40px;
                border: 4px solid #f3f3f3;
                border-top: 4px solid #007cba;
                border-radius: 50%;
                animation: platform-spin 1s linear infinite;
                margin: 0 auto 20px;
            }
            
            @keyframes platform-spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            
            .platform-url {
                font-size: 12px;
                color: #666;
                margin-top: 10px;
            }
            
            .platform-popup-footer {
                padding: 20px;
                border-top: 1px solid #eee;
                display: flex;
                justify-content: flex-end;
                gap: 10px;
            }
            
            .platform-btn {
                padding: 10px 20px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                text-decoration: none;
                display: inline-block;
                font-size: 14px;
            }
            
            .platform-btn-primary {
                background: #007cba;
                color: white;
            }
            
            .platform-btn-secondary {
                background: #f1f1f1;
                color: #333;
            }
            
            .platform-btn:hover {
                opacity: 0.9;
            }
            
            .platform-course-info h4 {
                margin-top: 0;
                color: #007cba;
            }
            
            .platform-placeholder {
                background: #f9f9f9;
                padding: 15px;
                border-radius: 4px;
                margin-top: 15px;
            }
            
            .platform-placeholder ul {
                margin: 10px 0 0 20px;
            }
            
            .platform-error {
                text-align: center;
                padding: 40px 20px;
                color: #d63638;
            }
            
            .platform-error h4 {
                margin-top: 0;
                color: #d63638;
            }
            
            .platform-error-details {
                background: #f9f9f9;
                padding: 10px;
                border-radius: 4px;
                font-family: monospace;
                font-size: 12px;
                margin: 15px 0;
            }
            
            /* Stili per il contenuto del corso */
            .modal-body {
                line-height: 1.6;
            }
            
            .course-detail-container,
            .course-detail-teacher-container,
            .course-detail-sector-container,
            .course-detail-cfu-container,
            .course-detail-description-container,
            .course-detail-objectives-container,
            .course-detail-prerequisites-container,
            .course-detail-content-container,
            .course-detail-activities-container,
            .course-detail-criteria-container,
            .course-detail-verification-container,
            .course-detail-books-container,
            .course-detail-receiving-container {
                margin-bottom: 25px;
                padding-bottom: 15px;
                border-bottom: 1px solid #f0f0f0;
            }
            
            .course-detail-receiving-container {
                border-bottom: none;
            }
            
            .course-detail-container h5,
            .course-detail-teacher-container h5,
            .course-detail-sector-container h5,
            .course-detail-cfu-container h5,
            .course-detail-description-container h5,
            .course-detail-objectives-container h5,
            .course-detail-prerequisites-container h5,
            .course-detail-content-container h5,
            .course-detail-activities-container h5,
            .course-detail-criteria-container h5,
            .course-detail-verification-container h5,
            .course-detail-books-container h5,
            .course-detail-receiving-container h5 {
                color: #007cba;
                font-size: 16px;
                font-weight: 600;
                margin: 0 0 10px 0;
            }
            
            .course-detail-teacher-content {
                color: #007cba;
                text-decoration: none;
            }
            
            .course-detail-teacher-content:hover {
                text-decoration: underline;
            }
        `;
        
        document.head.appendChild(style);
    }

    // Funzione globale per chiudere il popup (usata nel footer)
    window.closePlatformPopup = closePopup;

    // Avvia l'inizializzazione
    init();

})();

export default {};
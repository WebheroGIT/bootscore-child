/**
 * Video Modal JavaScript
 * Handles video modal functionality for links with modal-video or video class
 */

(function() {
    'use strict';
    
    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initVideoModal);
    } else {
        initVideoModal();
    }
    
    /**
     * Initialize video modal functionality
     */
    function initVideoModal() {
        // Get existing modal or create if it doesn't exist
        let modal = document.getElementById('videoModal');
        if (!modal) {
            createModalCSS();
            document.body.insertAdjacentHTML('beforeend', createModalHTML());
            modal = document.getElementById('videoModal');
        } else {
            // Ensure CSS is loaded for existing modal
            createModalCSS();
            // Ensure modal is hidden by default
            modal.classList.remove('show');
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
        }
        
        const video = modal.querySelector('video');
        const source = video.querySelector('source');
        const closeBtn = modal.querySelector('.video-modal-close');
        
        // Define modal functions
        function openModal(videoUrl) {
            source.src = videoUrl;
            video.load();
            modal.classList.add('show');
            modal.style.display = 'flex';
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            setTimeout(() => video.play(), 100);
        }
        
        function closeModal() {
            modal.classList.remove('show');
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            video.pause();
            video.currentTime = 0;
            source.src = '';
        }
        
        // Bind events to video links
        bindVideoLinks(openModal);
        
        // Bind close events
        bindCloseEvents(modal, closeModal);
        
        // Bind keyboard events
        bindKeyboardEvents(modal, closeModal);
        
        // Make functions globally accessible
        window.openModal = openModal;
        window.closeModal = closeModal;
    }
    
    /**
     * Bind click events to video links
     */
    function bindVideoLinks(openModal) {
        const videoLinks = document.querySelectorAll('a.video, a.modal-video');
        
        videoLinks.forEach(function(link) {
            // Skip if already initialized
            if (link.hasAttribute('data-video-modal-init')) {
                return;
            }
            
            link.setAttribute('data-video-modal-init', 'true');
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const videoUrl = this.getAttribute('href');
                if (videoUrl) {
                    openModal(videoUrl);
                }
            });
        });
    }
    
    /**
     * Create modal HTML structure
     */
    function createModalHTML() {
        return `
        <!-- Video Modal -->
        <div id="videoModal" class="video-modal-overlay" style="display: none;" aria-hidden="true" role="dialog">
            <div class="video-modal-dialog">
                <div class="video-modal-content">
                    <div class="video-modal-header">
                        <button type="button" class="video-modal-close" aria-label="Chiudi video" title="Chiudi video">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="video-modal-body">
                        <div class="video-wrapper">
                            <video controls preload="none" aria-label="Video player">
                                <source src="" type="video/mp4">
                                Il tuo browser non supporta il tag video.
                            </video>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    }
    
    /**
     * Create and inject modal CSS
     */
    function createModalCSS() {
        // Check if CSS is already injected
        if (document.getElementById('video-modal-css')) {
            return;
        }
        
        const css = `
        .video-modal-overlay{display:none!important;position:fixed!important;z-index:999999!important;top:0!important;left:0!important;width:100%!important;height:100%!important;background:rgba(0,0,0,0.75)!important;overflow:hidden!important;outline:0!important}
        .video-modal-overlay.show{display:flex!important;align-items:center!important;justify-content:center!important}
        .video-modal-dialog{position:relative!important;width:auto!important;max-width:90%!important;margin:1.75rem auto!important;pointer-events:none!important}
        .video-modal-content{position:relative!important;display:flex!important;flex-direction:column!important;width:100%!important;pointer-events:auto!important;background:#fff!important;border:1px solid rgba(0,0,0,0.2)!important;border-radius:0.375rem!important;box-shadow:0 0.5rem 1rem rgba(0,0,0,0.15)!important;outline:0!important}
        .video-modal-header{display:flex!important;flex-shrink:0!important;align-items:center!important;justify-content:flex-end!important;padding:1rem!important;border-bottom:1px solid #dee2e6!important;border-top-left-radius:calc(0.375rem - 1px)!important;border-top-right-radius:calc(0.375rem - 1px)!important}
        .video-modal-close{padding:0.5rem!important;margin:-0.5rem -0.5rem -0.5rem auto!important;background:0 0!important;border:0!important;font-size:1.125rem!important;font-weight:700!important;line-height:1!important;color:#000!important;text-decoration:none!important;opacity:0.5!important;cursor:pointer!important}
        .video-modal-close:hover{color:#000!important;text-decoration:none!important;opacity:0.75!important}
        .video-modal-body{position:relative!important;flex:1 1 auto!important;padding:0!important;background:#000!important}
        .video-wrapper{position:relative!important;width:100%!important;padding-bottom:56.25%!important;height:0!important;overflow:hidden!important}
        .video-wrapper video{position:absolute!important;top:0!important;left:0!important;width:100%!important;height:100%!important;border:none!important;object-fit:contain!important;background:#000!important}
        @media (min-width:576px){.video-modal-dialog{max-width:500px!important;margin:1.75rem auto!important}}
        @media (min-width:768px){.video-modal-dialog{max-width:700px!important}}
        @media (min-width:992px){.video-modal-dialog{max-width:900px!important}}
        @media (min-width:1200px){.video-modal-dialog{max-width:1140px!important}}`;
        
        const style = document.createElement('style');
        style.id = 'video-modal-css';
        style.textContent = css;
        document.head.appendChild(style);
    }
    
    /**
     * Bind close events
     */
    function bindCloseEvents(modal, closeModal) {
        const closeBtn = modal.querySelector('.video-modal-close');
        
        // Close button click
        closeBtn.addEventListener('click', closeModal);
        
        // Click outside modal
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    }
    
    /**
     * Bind keyboard events
     */
    function bindKeyboardEvents(modal, closeModal) {
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.classList.contains('show')) {
                closeModal();
            }
        });
    }
    
    /**
     * Public API for dynamic content
     */
    window.VideoModal = {
        init: function() {
            document.querySelectorAll('a.modal-video:not([data-modal-initialized]), a.video:not([data-modal-initialized])').forEach(link => {
                link.setAttribute('data-modal-initialized', 'true');
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const videoUrl = this.getAttribute('href');
                    if (videoUrl && window.openModal) {
                        window.openModal(videoUrl);
                    }
                });
            });
        },
        open: function(url) {
            if (window.openModal) {
                window.openModal(url);
            }
        },
        close: function() {
            if (window.closeModal) {
                window.closeModal();
            }
        }
    }
    
})();
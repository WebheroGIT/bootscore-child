/**
 * Video Modal Utility
 * 
 * Lightweight video modal functionality for links with 'modal-video' class
 * Optimized for performance and accessibility
 * 
 * @package BootScore Child
 * @since 1.0.0
 */

(function() {
    'use strict';
    
    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        initVideoModal();
    });
    
    /**
     * Initialize video modal functionality
     */
    function initVideoModal() {
        const modal = document.getElementById('videoModal');
        if (!modal) return;
        
        const video = modal.querySelector('video');
        const source = video.querySelector('source');
        const closeBtn = modal.querySelector('.wh-video-modal-close');
        
        // Bind events to modal-video links
        bindVideoLinks();
        
        // Bind close events
        bindCloseEvents(modal, video, source, closeBtn);
        
        // Bind keyboard events
        bindKeyboardEvents(modal);
    }
    
    /**
     * Bind click events to video links
     */
    function bindVideoLinks() {
        const videoLinks = document.querySelectorAll('a.modal-video');
        
        videoLinks.forEach(function(link) {
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
     * Bind close events
     */
    function bindCloseEvents(modal, video, source, closeBtn) {
        // Close button click
        closeBtn.addEventListener('click', function() {
            closeModal(modal, video, source);
        });
        
        // Click outside modal
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal(modal, video, source);
            }
        });
    }
    
    /**
     * Bind keyboard events
     */
    function bindKeyboardEvents(modal) {
        document.addEventListener('keyup', function(e) {
            if (e.key === 'Escape' && modal.style.display === 'flex') {
                const video = modal.querySelector('video');
                const source = video.querySelector('source');
                closeModal(modal, video, source);
            }
        });
    }
    
    /**
     * Open video modal
     */
    function openModal(videoUrl) {
        const modal = document.getElementById('videoModal');
        const video = modal.querySelector('video');
        const source = video.querySelector('source');
        
        // Set video source
        source.src = videoUrl;
        video.load();
        
        // Show modal
        modal.style.display = 'flex';
        modal.setAttribute('aria-hidden', 'false');
        
        // Prevent body scroll
        document.body.style.overflow = 'hidden';
        
        // Focus on video for accessibility
        video.focus();
        
        // Optional: Auto-play (consider user preferences)
        // video.play();
    }
    
    /**
     * Close video modal
     */
    function closeModal(modal, video, source) {
        // Hide modal
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
        
        // Restore body scroll
        document.body.style.overflow = '';
        
        // Stop and reset video
        video.pause();
        video.currentTime = 0;
        source.src = '';
    }
    
    /**
     * Public API for dynamic content
     */
    window.WHVideoModal = {
        init: initVideoModal,
        open: openModal,
        close: function() {
            const modal = document.getElementById('videoModal');
            if (modal) {
                const video = modal.querySelector('video');
                const source = video.querySelector('source');
                closeModal(modal, video, source);
            }
        }
    };
    
})();
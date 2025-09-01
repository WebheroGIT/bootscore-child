<?php
/**
 * Video Modal Utility
 * 
 * Provides a lightweight video modal functionality for links with 'modal-video' class
 * Optimized for performance and minimal footprint
 * 
 * @package BootScore Child
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class VideoModal {
    
    private static $instance = null;
    private $is_enqueued = false;
    
    /**
     * Get singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Initialize the video modal
     */
    public function __construct() {
        add_action('wp_footer', array($this, 'render_modal_html'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    /**
     * Enqueue scripts and styles only when needed
     */
    public function enqueue_scripts() {
        // Check if page content contains modal-video or video class
        global $post;
        $content = '';
        if (is_object($post)) {
            $content = $post->post_content;
        }
        
        // Check for shortcode or classes in content
        if (has_shortcode($content, 'modal-video') || 
            strpos($content, 'modal-video') !== false || 
            strpos($content, 'class="video"') !== false || 
            strpos($content, "class='video'") !== false) {
            $this->enqueue_assets();
        }
    }
    
    /**
     * Force enqueue assets (for dynamic content)
     */
    public function force_enqueue() {
        if (!$this->is_enqueued) {
            $this->enqueue_assets();
        }
    }
    
    /**
     * Enqueue the actual assets
     */
    private function enqueue_assets() {
        // Inline CSS for better performance
        wp_add_inline_style('bootscore-style', $this->get_modal_css());
        
        // Inline JS for better performance
        wp_add_inline_script('bootscore-script', $this->get_modal_js());
        
        $this->is_enqueued = true;
    }
    
    /**
     * Render modal HTML in footer
     */
    public function render_modal_html() {
        // Only render if assets are enqueued
        if (!$this->is_enqueued) {
            return;
        }
        
        echo $this->get_modal_html();
    }
    
    /**
     * Get modal HTML structure
     */
    private function get_modal_html() {
        return '
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
        </div>';
    }
    
    /**
     * Get optimized CSS
     */
    private function get_modal_css() {
        return '<style id="video-modal-css">
        .video-modal-overlay{display:none!important;position:fixed!important;z-index:999999!important;top:0!important;left:0!important;width:100%!important;height:100%!important;background:rgba(0,0,0,0.75)!important;overflow:hidden!important;outline:0!important}
        .video-modal-overlay.show{display:flex!important;align-items:center!important;justify-content:center!important}
        .video-modal-dialog{position:relative!important;width:100%!important;max-width:90%!important;margin:1.75rem auto!important;pointer-events:none!important}
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
        @media (min-width:1200px){.video-modal-dialog{max-width:1140px!important}}
        </style>';
    }
    
    /**
     * Get optimized JavaScript
     */
    private function get_modal_js() {
        return '
        document.addEventListener("DOMContentLoaded",function(){
            const modal=document.getElementById("videoModal");
            if(!modal)return;
            const video=modal.querySelector("video");
            const source=video.querySelector("source");
            const closeBtn=modal.querySelector(".video-modal-close");
            
            function openModal(url){
                source.src=url;
                video.load();
                modal.classList.add("show");
                modal.style.display="flex";
                modal.setAttribute("aria-hidden","false");
                document.body.style.overflow="hidden";
                setTimeout(()=>video.play(),100);
            }
            
            function closeModal(){
                modal.classList.remove("show");
                modal.style.display="none";
                modal.setAttribute("aria-hidden","true");
                document.body.style.overflow="";
                video.pause();
                video.currentTime=0;
                source.src="";
            }
            
            document.querySelectorAll("a.video, a.modal-video").forEach(link=>{
                link.addEventListener("click",function(e){
                    e.preventDefault();
                    const videoUrl=this.getAttribute("href");
                    if(videoUrl)openModal(videoUrl);
                });
            });
            
            closeBtn.addEventListener("click",closeModal);
            modal.addEventListener("click",function(e){
                if(e.target===modal)closeModal();
            });
            
            document.addEventListener("keydown",function(e){
                if(e.key==="Escape"&&modal.classList.contains("show"))closeModal();
            });
            
            window.openVideoModal=openModal;
            window.closeVideoModal=closeModal;
        });';
    }
    
    /**
     * Shortcode for manual activation
     */
    public static function shortcode($atts) {
        $instance = self::getInstance();
        $instance->force_enqueue();
        return '';
    }
}

// Initialize the video modal
VideoModal::getInstance();

// Register shortcode for manual activation
add_shortcode('modal-video', array('VideoModal', 'shortcode'));

/**
 * Helper function to force enqueue video modal assets
 * Use this in templates when you know modal-video links will be present
 */
function wh_enqueue_video_modal() {
    VideoModal::getInstance()->force_enqueue();
}
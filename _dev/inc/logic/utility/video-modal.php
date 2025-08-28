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
        // Check if page content contains modal-video class
        global $post;
        if (is_object($post) && has_shortcode($post->post_content, 'modal-video') || 
            (is_object($post) && strpos($post->post_content, 'modal-video') !== false)) {
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
        <div id="videoModal" class="wh-video-modal" aria-hidden="true" role="dialog" aria-labelledby="videoModalTitle">
            <div class="wh-video-modal-content">
                <button class="wh-video-modal-close" aria-label="Chiudi video" title="Chiudi video">&times;</button>
                <div class="wh-video-wrapper">
                    <video controls preload="none" aria-label="Video player">
                        <source src="" type="video/mp4">
                        Il tuo browser non supporta la riproduzione video.
                    </video>
                </div>
            </div>
        </div>';
    }
    
    /**
     * Get optimized CSS
     */
    private function get_modal_css() {
        return '
        .wh-video-modal{display:none;position:fixed;z-index:9999;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.8);justify-content:center;align-items:center}
        .wh-video-modal-content{position:relative;width:90%;max-width:800px;background:#000;border-radius:8px;overflow:hidden}
        .wh-video-modal-close{position:absolute;top:10px;right:15px;font-size:24px;color:#fff;cursor:pointer;z-index:2;background:none;border:none;padding:0;line-height:1}
        .wh-video-wrapper{position:relative;width:100%;padding-bottom:56.25%;height:0}
        .wh-video-wrapper video{position:absolute;top:0;left:0;width:100%;height:100%;border:none;object-fit:contain;background:#000}
        @media (max-width:768px){.wh-video-modal-content{width:95%;border-radius:4px}}';
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
            const closeBtn=modal.querySelector(".wh-video-modal-close");
            
            function openModal(url){
                source.src=url;
                video.load();
                modal.style.display="flex";
                modal.setAttribute("aria-hidden","false");
                document.body.style.overflow="hidden";
                video.focus();
            }
            
            function closeModal(){
                modal.style.display="none";
                modal.setAttribute("aria-hidden","true");
                document.body.style.overflow="";
                video.pause();
                video.currentTime=0;
                source.src="";
            }
            
            document.querySelectorAll("a.modal-video").forEach(link=>{
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
            
            document.addEventListener("keyup",function(e){
                if(e.key==="Escape"&&modal.style.display==="flex")closeModal();
            });
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
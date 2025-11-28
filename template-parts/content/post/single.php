<?php
/**
 * Template per il singolo post standard
 * Basato sul layout degli eventi ma senza custom fields
 *
 * @package Bootscore Child
 */

// Exit if accessed directly
defined('ABSPATH') || exit;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    
    <!-- Header del post -->
    <div class="rounded-3 overflow-hidden bg-primary text-white mb-5">
        <div class="grid-7-5 grid-xl-1 row-iscrizioni-formazione align-items-stretch position-relative">
            <div class="p-4 flex-grow-1 align-items-center d-flex">
                <h1 class="mb-2 mt-2"><?php the_title(); ?></h1>
            </div>
            
            <!-- Featured Image -->
            <div class="col-featured position-relative h-100 overflow-hidden" style="aspect-ratio: 14/9;overflow: hidden;">
                <?php if (has_post_thumbnail()): ?>
                    <?php the_post_thumbnail('large', array('class' => 'featured img-fluid h-100 w-100 img-formazione wp-post-image')); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Contenuto del post -->
    <div class="entry-content">
        <?php the_content(); ?>
    </div>
    
    <!-- Meta informazioni del post -->
    <div class="mt-4 mb-4">
        <div class="d-flex align-items-center mb-3">
            <i class="fas fa-calendar me-2 text-primary"></i>
            <span><?php echo get_the_date('j F Y'); ?></span>
        </div>
        
        <?php if (has_category()): ?>
        <div class="d-flex align-items-center mb-3">
            <i class="fas fa-folder me-2 text-primary"></i>
            <span><?php the_category(', '); ?></span>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Tags se presenti -->
    <?php if (has_tag()): ?>
    <div class="mb-4">
        <h5>Tag:</h5>
        <?php the_tags('<span class="badge bg-secondary me-2">', '</span><span class="badge bg-secondary me-2">', '</span>'); ?>
    </div>
    <?php endif; ?>
    
    <!-- Navigation tra post -->
    <div class="row mt-5">
        <div class="col-6">
            <?php 
            $prev_post = get_previous_post();
            if ($prev_post): 
            ?>
                <a href="<?php echo get_permalink($prev_post->ID); ?>" class="btn btn-outline-primary">
                    <i class="fas fa-chevron-left me-2"></i>Post precedente
                </a>
            <?php endif; ?>
        </div>
        <div class="col-6 text-end">
            <?php 
            $next_post = get_next_post();
            if ($next_post): 
            ?>
                <a href="<?php echo get_permalink($next_post->ID); ?>" class="btn btn-outline-primary">
                    Post successivo<i class="fas fa-chevron-right ms-2"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
    
</article>
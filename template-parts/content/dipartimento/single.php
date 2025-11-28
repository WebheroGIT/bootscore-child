<div class="custom-template-piano">
  
<!-- Header dell'evento -->
    <div class="rounded-3 overflow-hidden bg-primary text-white mb-5">
        <div class="grid-7-5 grid-xl-1 row-iscrizioni-formazione align-items-stretch position-relative">
            <div class="p-4 flex-grow-1 align-items-center d-flex">
                <h1 class="mb-2 mt-2"><?php the_title(); ?></h1>
                
               
            </div>
            
            <!-- Featured Image -->
            <div class="col-featured position-relative h-100 overflow-hidden">
                <?php if (has_post_thumbnail()): ?>
                    <?php the_post_thumbnail('large', array('class' => 'featured img-fluid h-100 w-100 img-formazione wp-post-image')); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>


  <div><?php the_content(); ?></div>

  <!-- puoi aggiungere qui layout, card, tabelle ecc. -->
</div>
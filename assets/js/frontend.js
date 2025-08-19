jQuery(document).ready(function () {
    if(jQuery(window).width() < 1200){
        jQuery('.navbar-toggler').on('click', function () {
            jQuery('.animated-icon').toggleClass('open');
        });
        jQuery('.dropdown-menu a.dropdown-toggle').on('click', function(e) {
            if (!jQuery(this).next().hasClass('show')) {
                jQuery(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
            }
            var $subMenu = jQuery(this).next(".dropdown-menu");
            $subMenu.toggleClass('show');


            jQuery(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
                jQuery('.dropdown-menu .dropdown-menu.show').removeClass("show");
            });


            return false;
        });

        jQuery('li.menu-item a.dropdown-toggle').on('click', function(e) {
            jQuery(this).parent().find('li').removeClass('open');
        });
        /* accordion menu */ 
        jQuery('#secondary .sidebar-btn').on('click', function(e) {
            event.preventDefault();
            jQuery('#secondary').toggleClass('open');
        });
        jQuery('.close-search').on('click touch', function(e){
            jQuery('.function').css('visibility','hidden');
        });
        jQuery( ".search-container .module.widget-handle .search" ).on( "click", function(e) {
            e.stopPropagation();
            jQuery(".function").css('visibility','visible');
        });
    } else {
        jQuery( ".search-container .module.widget-handle .search" ).on( "click", function(e) {
            jQuery("#searchBox").css('visibility','visible');
        });
        jQuery( ".close-search" ).on( "click", function(e) {
            jQuery("#searchBox").css('visibility','hidden');
        });
    }

    jQuery('.panel-collapse').on('show.bs.collapse', function(e) {
        var $panel = jQuery(this).closest('.panel');
        var $open = jQuery(this).closest('.panel-group');//.find('.panel-collapse.show');
        var additionalOffset = 200;
        /*if($panel.prevAll().filter($open.closest('.panel')).length !== 0)
        {
            additionalOffset =  $open.height();
        }*/
        jQuery('html,body').animate({
            scrollTop: $panel.offset().top - additionalOffset
        }, 500);
    });

    if(jQuery(window).width() > 1200){
        jQuery('.main-menu .nav-menu .menu .menu-item .dropdown-menu .menu-item .dropdown-toggle').on('click', function(e) {
            e.preventDefault(); 
        });
        jQuery('.main-menu .nav-menu .menu .menu-item .dropdown-menu .menu-item .dropdown-toggle').css("cursor","default");
    }

});

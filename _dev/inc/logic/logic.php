<?php
// Previeni l'accesso diretto
defined('ABSPATH') || exit;


// Inclusione logica settings pagine e slider
require_once get_stylesheet_directory() . '/_dev/inc/logic/settings/logic-settings.php';

// Inclusione logica settings pagine e slider
require_once get_stylesheet_directory() . '/_dev/inc/logic/banner/banner-slider.php';
// Inclusione logica settings pagine e slider
require_once get_stylesheet_directory() . '/_dev/inc/logic/banner/banner-evidenza.php';


// Inclusione logica per DUPLICARE POST
require_once get_stylesheet_directory() . '/_dev/inc/logic/settings/duplicate-post.php';


// whatsAPP login include
require_once get_stylesheet_directory(). '/_dev/inc/logic/whatsapp/whatsapp.php';


// PressROOM login include
require_once get_stylesheet_directory(). '/_dev/inc/logic/pressroom/rassegna-stampa.php';
// NEWS login include
require_once get_stylesheet_directory(). '/_dev/inc/logic/pressroom/news-loop.php';
// Events login include
require_once get_stylesheet_directory(). '/_dev/inc/logic/pressroom/events-loop.php';



// Dirigenza login include
require_once get_stylesheet_directory(). '/_dev/inc/logic/dirigenza/dirigenza.php';

// Dirigenza login include
require_once get_stylesheet_directory(). '/_dev/inc/logic/dipartimenti/logic-dipartimenti.php';


// Formazione login include
require_once get_stylesheet_directory(). '/_dev/inc/logic/formazione/formazione-loop.php';
require_once get_stylesheet_directory(). '/_dev/inc/logic/formazione/utility-shortcode-formazione.php';


// Sidebar include
require_once get_stylesheet_directory(). '/_dev/inc/logic/sidebar/sidebar-mod.php';

// plugins include
require_once get_stylesheet_directory(). '/_dev/inc/logic/plugin/plugins.php';

// Video Modal utility include
require_once get_stylesheet_directory(). '/_dev/inc/logic/utility/video-modal.php';
// struttura post template include
require_once get_stylesheet_directory(). '/_dev/inc/logic/utility/struttura-post-templatephp';

// Video Modal utility include
require_once get_stylesheet_directory(). '/_dev/assets-admin/admin-search.php';


<?php

/**
 * Breadcrumb personalizzato con supporto per Custom Post Type
 *
 * @package Bootscore Child
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

if (!function_exists('the_breadcrumb')) :
  function the_breadcrumb() {

    if (!is_home()) {

      echo '<nav aria-label="breadcrumb" class="' . apply_filters('bootscore/class/breadcrumb/nav', 'overflow-x-auto text-nowrap mb-4 mt-2 py-2 px-3 bg-body-tertiary rounded') . '">';
      echo '<ol class="breadcrumb ' . apply_filters('bootscore/class/breadcrumb/ol', 'flex-nowrap mb-0') . '">';

      // Home
      echo '<li class="breadcrumb-item"><a class="' . apply_filters('bootscore/class/breadcrumb/item/link', '') . '" href="' . home_url() . '">' . apply_filters('bootscore/icon/home', '<i class="fa-solid fa-house"></i>') . '<span class="visually-hidden">' . __('Home', 'bootscore') . '</span></a></li>';

      // Se è un single post o custom post
      if (is_single()) {
        $post_type = get_post_type();

        // Se è un custom post type (diverso da "post")
        if ($post_type !== 'post') {
          $post_type_obj = get_post_type_object($post_type);
          if ($post_type_obj && $post_type_obj->has_archive) {
            $archive_link = get_post_type_archive_link($post_type);
            echo '<li class="breadcrumb-item"><a class="' . apply_filters('bootscore/class/breadcrumb/item/link', '') . '" href="' . esc_url($archive_link) . '">' . esc_html($post_type_obj->labels->singular_name) . '</a></li>';
          }
        } else {
          // Se è un post normale, mostra le categorie
          $cat_IDs = wp_get_post_categories(get_the_ID());
          foreach ($cat_IDs as $cat_ID) {
            $cat = get_category($cat_ID);
            echo '<li class="breadcrumb-item"><a class="' . apply_filters('bootscore/class/breadcrumb/item/link', '') . '" href="' . get_term_link($cat->term_id) . '">' . esc_html($cat->name) . '</a></li>';
          }
        }
      }

      // Titolo della pagina o post corrente
      if (is_page() || is_single()) {
        echo '<li class="breadcrumb-item active" aria-current="page">' . esc_html(get_the_title()) . '</li>';
      }

      echo '</ol>';
      echo '</nav>';
    }
  }

  add_filter('breadcrumbs', 'breadcrumbs');
endif;

<?php

/**
 * Breadcrumb personalizzato con supporto per Custom Post Type e Tassonomie
 *
 * @package Bootscore Child
 * @version 1.1.0
 */

defined('ABSPATH') || exit;

if (!function_exists('the_breadcrumb')) :
  function the_breadcrumb() {

    if (!is_home()) {

      echo '<nav aria-label="breadcrumb" class="' . apply_filters('bootscore/class/breadcrumb/nav', 'overflow-x-auto text-nowrap mb-4 mt-2 py-2 px-3 bg-body-tertiary rounded') . '">';
      echo '<ol class="breadcrumb ' . apply_filters('bootscore/class/breadcrumb/ol', 'flex-nowrap mb-0') . '">';

      // Home
      echo '<li class="breadcrumb-item"><a class="' . apply_filters('bootscore/class/breadcrumb/item/link', '') . '" href="' . home_url() . '">' . apply_filters('bootscore/icon/home', '<i class="fa-solid fa-house"></i>') . '<span class="visually-hidden">' . __('Home', 'bootscore') . '</span></a></li>';

      // CONFIGURAZIONE: Post Types e Tassonomie Associate
      $post_type_taxonomies = [
        'formazione' => ['cat-formazione'], // Post type 'formazione' usa la tassonomia 'cat-formazione'
        //'corsi' => ['categoria-corsi'],      // Esempio per altri post types
        //'eventi' => ['categoria-eventi'],    // Esempio per altri post types
        // Aggiungi qui altri post types e le loro tassonomie
      ];

      // GESTIONE ARCHIVE PAGES per i post types
      if (is_post_type_archive()) {
        $post_type = get_post_type();
        $post_type_obj = get_post_type_object($post_type);
        
        if ($post_type_obj) {
          echo '<li class="breadcrumb-item active" aria-current="page">' . esc_html($post_type_obj->labels->name) . '</li>';
        }
      }

      // Se è un single post o custom post
      elseif (is_single()) {
        $post_type = get_post_type();

        // Se è un custom post type (diverso da "post")
        if ($post_type !== 'post') {
          $post_type_obj = get_post_type_object($post_type);
          if ($post_type_obj && $post_type_obj->has_archive) {
            $archive_link = get_post_type_archive_link($post_type);
            echo '<li class="breadcrumb-item"><a class="' . apply_filters('bootscore/class/breadcrumb/item/link', '') . '" href="' . esc_url($archive_link) . '">' . esc_html($post_type_obj->labels->name) . '</a></li>';
          }

          // GESTIONE TASSONOMIE per i Custom Post Types configurati
          if (array_key_exists($post_type, $post_type_taxonomies)) {
            $taxonomies_to_check = $post_type_taxonomies[$post_type];
            
            foreach ($taxonomies_to_check as $taxonomy) {
              $terms = wp_get_post_terms(get_the_ID(), $taxonomy);
              
              if (!empty($terms) && !is_wp_error($terms)) {
                // Se ci sono più termini, prendiamo il primo (o potresti implementare una logica diversa)
                $term = $terms[0];
                
                // Costruisci la gerarchia dei termini (parent -> child)
                $term_hierarchy = [];
                $current_term = $term;
                
                // Risali la gerarchia fino al termine padre
                while ($current_term && $current_term->parent != 0) {
                  array_unshift($term_hierarchy, $current_term);
                  $current_term = get_term($current_term->parent, $taxonomy);
                }
                // Aggiungi il termine radice
                if ($current_term) {
                  array_unshift($term_hierarchy, $current_term);
                }
                
                // Se non abbiamo gerarchia, usa il termine corrente
                if (empty($term_hierarchy)) {
                  $term_hierarchy = [$term];
                }
                
                // Stampa tutti i termini nella gerarchia
                foreach ($term_hierarchy as $hier_term) {
                  $term_link = get_term_link($hier_term);
                  if (!is_wp_error($term_link)) {
                    echo '<li class="breadcrumb-item"><a class="' . apply_filters('bootscore/class/breadcrumb/item/link', '') . '" href="' . esc_url($term_link) . '">' . esc_html($hier_term->name) . '</a></li>';
                  }
                }
                
                // Esci dal loop dopo aver trovato termini per questa tassonomia
                break;
              }
            }
          }

        } else {
          // Se è un post normale, mostra le categorie
          $cat_IDs = wp_get_post_categories(get_the_ID());
          foreach ($cat_IDs as $cat_ID) {
            $cat = get_category($cat_ID);
            echo '<li class="breadcrumb-item"><a class="' . apply_filters('bootscore/class/breadcrumb/item/link', '') . '" href="' . get_term_link($cat->term_id) . '">' . esc_html($cat->name) . '</a></li>';
          }
        }
        
        // Titolo del post corrente
        echo '<li class="breadcrumb-item active" aria-current="page">' . esc_html(get_the_title()) . '</li>';
      }

      // GESTIONE ARCHIVE PAGES per le tassonomie
      elseif (is_tax()) {
        $current_term = get_queried_object();
        $taxonomy = $current_term->taxonomy;
        
        // Trova il post type associato a questa tassonomia
        $associated_post_type = null;
        foreach ($post_type_taxonomies as $pt => $taxonomies) {
          if (in_array($taxonomy, $taxonomies)) {
            $associated_post_type = $pt;
            break;
          }
        }
        
        // Mostra il link all'archivio del post type se trovato
        if ($associated_post_type) {
          $post_type_obj = get_post_type_object($associated_post_type);
          if ($post_type_obj && $post_type_obj->has_archive) {
            $archive_link = get_post_type_archive_link($associated_post_type);
            echo '<li class="breadcrumb-item"><a class="' . apply_filters('bootscore/class/breadcrumb/item/link', '') . '" href="' . esc_url($archive_link) . '">' . esc_html($post_type_obj->labels->name) . '</a></li>';
          }
        }
        
        // Mostra la gerarchia dei termini se esistono parent
        $term_hierarchy = [];
        $current_term_temp = $current_term;
        
        while ($current_term_temp && $current_term_temp->parent != 0) {
          array_unshift($term_hierarchy, $current_term_temp);
          $current_term_temp = get_term($current_term_temp->parent, $taxonomy);
        }
        if ($current_term_temp) {
          array_unshift($term_hierarchy, $current_term_temp);
        }
        
        if (empty($term_hierarchy)) {
          $term_hierarchy = [$current_term];
        }
        
        // Stampa tutti i termini tranne l'ultimo (che sarà mostrato come attivo)
        for ($i = 0; $i < count($term_hierarchy) - 1; $i++) {
          $term_link = get_term_link($term_hierarchy[$i]);
          if (!is_wp_error($term_link)) {
            echo '<li class="breadcrumb-item"><a class="' . apply_filters('bootscore/class/breadcrumb/item/link', '') . '" href="' . esc_url($term_link) . '">' . esc_html($term_hierarchy[$i]->name) . '</a></li>';
          }
        }
        
        // Termine corrente come attivo
        echo '<li class="breadcrumb-item active" aria-current="page">' . esc_html($current_term->name) . '</li>';
      }
      
      // Titolo della pagina
      elseif (is_page()) {
        echo '<li class="breadcrumb-item active" aria-current="page">' . esc_html(get_the_title()) . '</li>';
      }

      echo '</ol>';
      echo '</nav>';
    }
  }

  add_filter('breadcrumbs', 'breadcrumbs');
endif;
<?php
/**
 * Breadcrumb specifico per post type "piano"
 * Mostra: Home > Offerta Formativa > Categorie formazione > Formazione > Piano
 * 
 * @package Bootscore Child
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Genera il breadcrumb per i post di tipo "piano"
 * Ottimizzato per minimizzare le query al database
 * 
 * @param int $piano_id ID del post di tipo piano
 * @return void
 */
function breadcrumb_piano($piano_id) {
  // Verifica che l'ID sia valido
  if (!$piano_id || !is_numeric($piano_id)) {
    return;
  }
  
  // Verifica che il post esista
  $piano_post = get_post($piano_id);
  if (!$piano_post || $piano_post->post_type !== 'piano') {
    return;
  }
  
  // Verifica che la classe MB_Relationships_API esista
  if (!class_exists('MB_Relationships_API')) {
    return;
  }
  
  // 1. Recupera la formazione collegata (relazione piano -> formazione)
  // Ottimizzazione: una sola chiamata API
  try {
    $formazioni = MB_Relationships_API::get_connected([
      'id'   => 'rel-pian-form',
      'from' => $piano_id,
      'type' => 'to',
    ]);
  } catch (Exception $e) {
    // Se c'è un errore, esci silenziosamente
    return;
  }
  
  // Se non ci sono formazioni collegate, esci
  if (empty($formazioni)) {
    return;
  }
  
  // Gestisci diversi formati di risultato
  $formazione_id = null;
  
  // Se è un array
  if (is_array($formazioni)) {
    // Se è un array di oggetti WP_Post
    if (isset($formazioni[0]) && is_object($formazioni[0])) {
      if (isset($formazioni[0]->ID)) {
        $formazione_id = (int) $formazioni[0]->ID;
      } elseif (method_exists($formazioni[0], 'get_id')) {
        $formazione_id = (int) $formazioni[0]->get_id();
      }
    }
    // Se è un array di ID numerici
    elseif (isset($formazioni[0]) && is_numeric($formazioni[0])) {
      $formazione_id = (int) $formazioni[0];
    }
    // Se è un array associativo con chiave 'ID'
    elseif (isset($formazioni['ID'])) {
      $formazione_id = (int) $formazioni['ID'];
    }
  }
  // Se è un oggetto WP_Post direttamente
  elseif (is_object($formazioni) && isset($formazioni->ID)) {
    $formazione_id = (int) $formazioni->ID;
  }
  
  // Se non abbiamo un ID valido, esci
  if (!$formazione_id || !get_post($formazione_id)) {
    return;
  }
  
  // 2. Link all'archivio "Offerta Formativa"
  $formazione_archive_link = get_post_type_archive_link('formazione');
  if ($formazione_archive_link) {
    $formazione_obj = get_post_type_object('formazione');
    if ($formazione_obj) {
      echo '<li class="breadcrumb-item"><a class="' . apply_filters('bootscore/class/breadcrumb/item/link', '') . '" href="' . esc_url($formazione_archive_link) . '">' . esc_html($formazione_obj->labels->name) . '</a></li>';
    }
  }
  
  // 3. Recupera le categorie (cat-formazione) della formazione
  // Ottimizzazione: una sola chiamata get_terms con caching
  $terms = wp_get_post_terms($formazione_id, 'cat-formazione', array(
    'orderby' => 'term_order',
    'order' => 'ASC'
  ));
  
  if (!empty($terms) && !is_wp_error($terms)) {
    // Se più termini, usa il primario di Rank Math se disponibile
    $term = $terms[0];
    if (count($terms) > 1) {
      // 1) Prova helper Rank Math
      if (function_exists('rank_math_get_primary_term')) {
        $primary_term = rank_math_get_primary_term('cat-formazione', $formazione_id);
        if ($primary_term instanceof WP_Term) {
          $term = $primary_term;
        }
      } else {
        // 2) Fallback su meta Rank Math
        $primary_term_id = get_post_meta($formazione_id, 'rank_math_primary_cat-formazione', true);
        if (!empty($primary_term_id)) {
          $candidate = get_term((int) $primary_term_id, 'cat-formazione');
          if ($candidate && !is_wp_error($candidate)) {
            $term = $candidate;
          }
        }
      }
    }
    
    // Costruisci la gerarchia dei termini (parent -> child)
    // Ottimizzazione: usa get_term per evitare loop multipli
    $term_hierarchy = [];
    $current_term = $term;
    
    // Risali la gerarchia fino al termine padre
    while ($current_term && $current_term->parent != 0) {
      array_unshift($term_hierarchy, $current_term);
      $current_term = get_term($current_term->parent, 'cat-formazione');
      if (!$current_term || is_wp_error($current_term)) {
        break;
      }
    }
    
    // Aggiungi il termine radice
    if ($current_term && !is_wp_error($current_term)) {
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
        $display_name = $hier_term->name;
        // Usa la funzione di display personalizzata se disponibile
        if (function_exists('bs_cat_formazione_display_name') && $hier_term->taxonomy === 'cat-formazione') {
          $display_name = bs_cat_formazione_display_name($hier_term);
        }
        echo '<li class="breadcrumb-item"><a class="' . apply_filters('bootscore/class/breadcrumb/item/link', '') . '" href="' . esc_url($term_link) . '">' . esc_html($display_name) . '</a></li>';
      }
    }
  }
  
  // 4. Link alla formazione
  $formazione_permalink = get_permalink($formazione_id);
  $formazione_title = get_the_title($formazione_id);
  if ($formazione_permalink) {
    echo '<li class="breadcrumb-item"><a class="' . apply_filters('bootscore/class/breadcrumb/item/link', '') . '" href="' . esc_url($formazione_permalink) . '">' . esc_html($formazione_title) . '</a></li>';
  }
  
  // 5. Titolo del piano corrente (attivo)
  echo '<li class="breadcrumb-item active" aria-current="page">' . esc_html(get_the_title($piano_id)) . '</li>';
}


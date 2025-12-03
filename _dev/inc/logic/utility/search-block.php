<?php
/**
 * Search Block Accessibility Fix
 * 
 * Fixes WAVE accessibility error: "Multiple labels" for the Gutenberg Search block
 * by hiding the visible label and keeping aria-label on button for screen readers
 * 
 * @package BootScore Child
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Fix accessibility issue with Search block (multiple labels)
 * 
 * The Gutenberg Search block has both a visible <label> and a button with aria-label="Ricerca"
 * This causes a WAVE error. We hide the visible label to fix it.
 * 
 * @param string $block_content The block content.
 * @param array  $block         The full block, including name and attributes.
 * @return string The filtered block content.
 */
function bootscore_child_search_block_accessibility_fix($block_content, $block) {
    // Apply only to the Search block
    if ('core/search' !== $block['blockName']) {
        return $block_content;
    }

    // If content is empty, return as is
    if (empty($block_content)) {
        return $block_content;
    }

    // Use DOMDocument to process HTML
    $dom = new DOMDocument();
    
    // Suppress errors for imperfect HTML
    libxml_use_internal_errors(true);
    
    // Encode UTF-8 and load content
    @$dom->loadHTML(mb_convert_encoding($block_content, 'HTML-ENTITIES', 'UTF-8'));
    
    // Clear errors
    libxml_clear_errors();
    
    // Find all labels with wp-block-search__label class
    $xpath = new DOMXPath($dom);
    $labels = $xpath->query('//label[contains(@class, "wp-block-search__label")]');
    
    foreach ($labels as $label) {
        // Get the associated input ID from the 'for' attribute
        $for_attr = $label->getAttribute('for');
        
        // Find the associated input
        $input = null;
        if (!empty($for_attr)) {
            $input_query = $xpath->query("//input[@id='{$for_attr}']");
            if ($input_query->length > 0) {
                $input = $input_query->item(0);
            }
        }
        
        // Remove the 'for' attribute from label to break the association
        // This prevents WAVE from seeing multiple labels
        if ($label->hasAttribute('for')) {
            $label->removeAttribute('for');
        }
        
        // Add visually-hidden class to hide the label visually
        // But also add aria-hidden since we're using button aria-label instead
        $current_classes = $label->getAttribute('class');
        if (strpos($current_classes, 'visually-hidden') === false) {
            $new_classes = trim($current_classes . ' visually-hidden');
            $label->setAttribute('class', $new_classes);
        }
        
        // Add aria-hidden to ensure screen readers skip it
        $label->setAttribute('aria-hidden', 'true');
        
        // Ensure input has proper aria-label if it doesn't have one
        if ($input && !$input->hasAttribute('aria-label') && !$input->hasAttribute('aria-labelledby')) {
            $input->setAttribute('aria-label', esc_attr(__('Search', 'bootscore')));
        }
    }
    
    // Extract only body content (removes html, head, body tags added by DOMDocument)
    $body = $dom->getElementsByTagName('body')->item(0);
    if ($body) {
        $new_content = '';
        foreach ($body->childNodes as $child) {
            $new_content .= $dom->saveHTML($child);
        }
        return $new_content;
    }
    
    return $block_content;
}
add_filter('render_block_core/search', 'bootscore_child_search_block_accessibility_fix', 20, 2);


/**
 * Admin Internal Search JavaScript
 * Handles AJAX search functionality for the admin search page
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        const $searchInput = $('#search-input');
        const $searchButton = $('#search-button');
        const $clearButton = $('#clear-button');
        const $loading = $('#loading');
        const $resultsTable = $('#results-table');
        const $resultsTbody = $('#results-tbody');
        const $noResults = $('#no-results');
        
        // Search on button click
        $searchButton.on('click', function() {
            performSearch();
        });
        
        // Search on Enter key
        $searchInput.on('keypress', function(e) {
            if (e.which === 13) {
                performSearch();
            }
        });
        
        // Clear results
        $clearButton.on('click', function() {
            $searchInput.val('');
            hideAllResults();
        });
        
        // Auto-search while typing (debounced)
        let searchTimeout;
        $searchInput.on('input', function() {
            clearTimeout(searchTimeout);
            const searchTerm = $(this).val().trim();
            
            if (searchTerm.length >= 3) {
                searchTimeout = setTimeout(function() {
                    performSearch();
                }, 500);
            } else if (searchTerm.length === 0) {
                hideAllResults();
            }
        });
        
        /**
         * Perform AJAX search
         */
        function performSearch() {
            const searchTerm = $searchInput.val().trim();
            
            if (searchTerm.length < 2) {
                alert('Inserisci almeno 2 caratteri per la ricerca.');
                return;
            }
            
            // Show loading
            hideAllResults();
            $loading.show();
            
            // AJAX request
            $.ajax({
                url: adminSearch.ajax_url,
                type: 'POST',
                data: {
                    action: 'admin_internal_search',
                    search_term: searchTerm,
                    nonce: adminSearch.nonce
                },
                success: function(response) {
                    $loading.hide();
                    
                    if (response.success && response.data.length > 0) {
                        displayResults(response.data);
                    } else {
                        $noResults.show();
                    }
                },
                error: function(xhr, status, error) {
                    $loading.hide();
                    console.error('Search error:', error);
                    alert('Errore durante la ricerca. Riprova.');
                }
            });
        }
        
        /**
         * Display search results
         */
        function displayResults(results) {
            $resultsTbody.empty();
            
            results.forEach(function(post) {
                const row = createResultRow(post);
                $resultsTbody.append(row);
            });
            
            $resultsTable.show();
        }
        
        /**
         * Create a result row
         */
        function createResultRow(post) {
            const statusClass = getStatusClass(post.status);
            const postTypeLink = getPostTypeLink(post.post_type);
            
            const $row = $('<tr>');
            
            // Title column with link
            const $titleCell = $('<td>').html(
                `<strong><a href="${post.view_link}" target="_blank">${post.title}</a></strong>`
            );
            
            // Slug column
            const $slugCell = $('<td>').text(post.slug);
            
            // Post type column with link
            const $postTypeCell = $('<td>').html(
                `<a href="${postTypeLink}">${post.post_type_label}</a>`
            );
            
            // Date column
            const $dateCell = $('<td>').text(formatDate(post.date));
            
            // Actions column
            const $actionsCell = $('<td>').html(createActionsHtml(post));
            
            $row.append($titleCell, $slugCell, $postTypeCell, $dateCell, $actionsCell);
            $row.addClass(statusClass);
            
            // Add hover effects for actions
            addHoverEffects($row, post);
            
            return $row;
        }
        
        /**
         * Create actions HTML
         */
        function createActionsHtml(post) {
            let actions = [];
            
            // Edit link
            if (post.edit_link) {
                actions.push(`<a href="${post.edit_link}" class="edit-link">Modifica</a>`);
            }
            
            // View link
            if (post.view_link && post.status === 'publish') {
                actions.push(`<a href="${post.view_link}" target="_blank" class="view-link">Visualizza</a>`);
            }
            
            // Quick edit (placeholder for now)
            actions.push(`<span class="quick-edit-link" data-post-id="${post.id}">Modifica veloce</span>`);
            
            return actions.join(' | ');
        }
        
        /**
         * Add hover effects similar to WordPress
         */
        function addHoverEffects($row, post) {
            $row.on('mouseenter', function() {
                $(this).addClass('hover-highlight');
                // Show additional actions on hover
                const $actionsCell = $(this).find('td:last-child');
                $actionsCell.addClass('row-actions-visible');
            });
            
            $row.on('mouseleave', function() {
                $(this).removeClass('hover-highlight');
                const $actionsCell = $(this).find('td:last-child');
                $actionsCell.removeClass('row-actions-visible');
            });
        }
        
        /**
         * Get status CSS class
         */
        function getStatusClass(status) {
            switch (status) {
                case 'draft':
                    return 'status-draft';
                case 'private':
                    return 'status-private';
                case 'pending':
                    return 'status-pending';
                default:
                    return 'status-publish';
            }
        }
        
        /**
         * Get post type admin link
         */
        function getPostTypeLink(postType) {
            return `edit.php?post_type=${postType}`;
        }
        
        /**
         * Format date
         */
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('it-IT') + ' ' + date.toLocaleTimeString('it-IT', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        
        /**
         * Hide all result containers
         */
        function hideAllResults() {
            $loading.hide();
            $resultsTable.hide();
            $noResults.hide();
        }
    });
    
})(jQuery);
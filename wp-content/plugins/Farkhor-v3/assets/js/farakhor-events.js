/**
 * Farakhor Events JavaScript
 */
(function($) {
    'use strict';
    
    /**
     * Farakhor Events main object
     */
    var FarakhorEvents = {
        /**
         * Initialize events
         */
        init: function() {
            // Cache DOM elements
            this.$container = $('.farakhor-container');
            this.$cardsContainer = $('#farakhor-cards-container');
            this.$searchInput = $('#farakhor-search-input');
            this.$monthSelect = $('#farakhor-month-select');
            this.$loading = $('#farakhor-loading');
            this.$cardTemplate = $('#farakhor-card-template');
            
            // Set limit from data attribute
            this.limit = this.$container.data('limit') || 12;
            
            // Filter events on load if a month is selected
            if (this.$monthSelect.val() !== 'all') {
                this.filterEvents();
            }
            
            // Bind events
            this.bindEvents();
        },
        
        /**
         * Bind events
         */
        bindEvents: function() {
            var self = this;
            var searchTimer;
            
            // Month select change
            this.$monthSelect.on('change', function() {
                self.filterEvents();
            });
            
            // Search input keyup with debounce
            this.$searchInput.on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    self.filterEvents();
                }, 500);
            });
        },
        
        /**
         * Filter events via AJAX
         */
        filterEvents: function() {
            var self = this;
            var month = this.$monthSelect.val();
            var search = this.$searchInput.val();
            
            // Show loading indicator
            this.$loading.show();
            
            // Make AJAX request
            $.ajax({
                url: farakhorEvents.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'farakhor_filter_events',
                    nonce: farakhorEvents.nonce,
                    month: month,
                    search: search,
                    limit: self.limit
                },
                success: function(response) {
                    if (response.success) {
                        self.renderEvents(response.events);
                    } else {
                        console.error('Error filtering events:', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                },
                complete: function() {
                    // Hide loading indicator
                    self.$loading.hide();
                }
            });
        },
        
        /**
         * Render events to cards container
         * 
         * @param {Array} events Events data.
         */
        renderEvents: function(events) {
            var self = this;
            
            // Clear current cards
            this.$cardsContainer.empty();
            
            if (events.length === 0) {
                // Show no results message
                var noResults = $('<div class="farakhor-no-result">هیچ رویدادی یافت نشد.</div>');
                this.$cardsContainer.append(noResults);
                return;
            }
            
            // Iterate events and add cards
            $.each(events, function(index, event) {
                var $card = self.createCard(event);
                self.$cardsContainer.append($card);
            });
        },
        
        /**
         * Create a card element from event data
         * 
         * @param {Object} event Event data.
         * @return {jQuery} Card element.
         */
        createCard: function(event) {
            // Clone template
            var $template = this.$cardTemplate.html();
            var $card = $($.parseHTML($template));
            
            // Set card data
            $card.attr('href', event.post_link || '#');
            
            // Set background image
            $card.find('.farakhor-card-bg').css('background-image', 'url(' + (event.image || '') + ')');
            
            // Set dates
            $card.find('.farakhor-persian-date').text(event.persian_full_date || '');
            $card.find('.farakhor-gregorian-date').text(event.gregorian_formatted || '');
            
            // Set remaining days
            $card.find('.farakhor-card-remain').text(event.remaining_days || '');
            
            // Set title
            $card.find('.farakhor-card-title').text(event.event_title || '');
            
            // Add categories and tags
            var $bottom = $card.find('.farakhor-card-bottom');
            
            // Add categories
            if (event.filtered_categories && event.filtered_categories.length) {
                $.each(event.filtered_categories, function(i, category) {
                    $bottom.append($('<span class="farakhor-category">').text(category));
                });
            }
            
            // Add tags
            if (event.tags_array && event.tags_array.length) {
                $.each(event.tags_array, function(i, tag) {
                    $bottom.append($('<span class="farakhor-tag">').text(tag));
                });
            }
            
            return $card;
        }
    };
    
    // Initialize on document ready
    $(document).ready(function() {
        FarakhorEvents.init();
    });
    
})(jQuery);
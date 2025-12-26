/**
 * Autocomplete Component
 * A reusable autocomplete/auto-suggesting input component
 * 
 * Usage:
 *   - Via data attributes:
 *     <input type="text" 
 *            data-autocomplete 
 *            data-autocomplete-url="/api/autocomplete/skills"
 *            data-autocomplete-min-length="2">
 * 
 *   - Via JavaScript:
 *     Autocomplete.init('#myInput', {
 *       url: '/api/autocomplete/skills',
 *       minLength: 2,
 *       displayField: 'name',
 *       valueField: 'id'
 *     });
 */

(function ($) {
    'use strict';

    var Autocomplete = {
        instances: {},

        /**
         * Initialize autocomplete on an input element
         * @param {string|jQuery} selector - Input element selector or jQuery object
         * @param {object} options - Configuration options
         */
        init: function (selector, options) {
            var $input = $(selector);
            if (!$input.length) {
                console.warn('Autocomplete: Input element not found', selector);
                return;
            }

            // Get options from data attributes or passed options
            var config = $.extend({
                url: $input.data('autocomplete-url') || '',
                minLength: parseInt($input.data('autocomplete-min-length')) || 2,
                displayField: $input.data('autocomplete-display-field') || 'name',
                valueField: $input.data('autocomplete-value-field') || 'id',
                debounceDelay: parseInt($input.data('autocomplete-debounce-delay')) || 300,
                placeholder: $input.data('autocomplete-placeholder') || 'Start typing...',
                noResultsText: $input.data('autocomplete-no-results') || 'No results found',
                loadingText: $input.data('autocomplete-loading') || 'Loading...',
                allowCustom: $input.data('autocomplete-allow-custom') !== false, // Default true
                onSelect: null, // Callback function when item is selected
                onError: null, // Callback function on error
            }, options || {});

            if (!config.url) {
                console.warn('Autocomplete: URL not specified', selector);
                return;
            }

            // Create unique instance ID
            var instanceId = $input.attr('id') || 'autocomplete-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
            if (!$input.attr('id')) {
                $input.attr('id', instanceId);
            }

            // Store instance
            this.instances[instanceId] = {
                $input: $input,
                config: config,
                $dropdown: null,
                $loading: null,
                $noResults: null,
                selectedIndex: -1,
                results: [],
                debounceTimer: null,
                xhr: null
            };

            // Initialize UI
            this._createUI(instanceId);
            this._attachEvents(instanceId);

            return this.instances[instanceId];
        },

        /**
         * Create the autocomplete UI elements
         */
        _createUI: function (instanceId) {
            var instance = this.instances[instanceId];
            var $input = instance.$input;

            // Create wrapper if it doesn't exist
            if (!$input.parent().hasClass('autocomplete-wrapper')) {
                $input.wrap('<div class="autocomplete-wrapper"></div>');
            }

            var $wrapper = $input.parent('.autocomplete-wrapper');

            // Create dropdown container
            var $dropdown = $('<div class="autocomplete-dropdown"></div>');
            $dropdown.hide();
            $wrapper.append($dropdown);

            // Create loading indicator
            var $loading = $('<div class="autocomplete-loading">' + instance.config.loadingText + '</div>');
            $loading.hide();
            $dropdown.append($loading);

            // Create results container
            var $results = $('<ul class="autocomplete-results"></ul>');
            $dropdown.append($results);

            // Create no results message
            var $noResults = $('<div class="autocomplete-no-results">' + instance.config.noResultsText + '</div>');
            $noResults.hide();
            $dropdown.append($noResults);

            instance.$dropdown = $dropdown;
            instance.$loading = $loading;
            instance.$noResults = $noResults;
            instance.$results = $results;
        },

        /**
         * Attach event handlers
         */
        _attachEvents: function (instanceId) {
            var instance = this.instances[instanceId];
            var $input = instance.$input;
            var self = this;

            // Input event with debouncing
            $input.on('input', function () {
                var query = $(this).val().trim();
                self._handleInput(instanceId, query);
            });

            // Focus event
            $input.on('focus', function () {
                var query = $(this).val().trim();
                if (query.length >= instance.config.minLength) {
                    self._handleInput(instanceId, query);
                }
            });

            // Blur event - close dropdown after a short delay
            $input.on('blur', function () {
                setTimeout(function () {
                    self._closeDropdown(instanceId);
                }, 200);
            });

            // Keyboard navigation
            $input.on('keydown', function (e) {
                self._handleKeydown(instanceId, e);
            });

            // Click on results
            instance.$results.on('click', 'li', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var index = $(this).index();
                self._selectItem(instanceId, index);
            });

            // Prevent dropdown from closing when clicking inside it
            instance.$dropdown.on('mousedown', function (e) {
                e.preventDefault();
            });
        },

        /**
         * Handle input changes with debouncing
         */
        _handleInput: function (instanceId, query) {
            var instance = this.instances[instanceId];

            // Clear previous timer
            if (instance.debounceTimer) {
                clearTimeout(instance.debounceTimer);
            }

            // Check minimum length
            if (query.length < instance.config.minLength) {
                this._closeDropdown(instanceId);
                return;
            }

            // Debounce the API call
            var self = this;
            instance.debounceTimer = setTimeout(function () {
                self._fetchSuggestions(instanceId, query);
            }, instance.config.debounceDelay);
        },

        /**
         * Fetch suggestions from API
         */
        _fetchSuggestions: function (instanceId, query) {
            var instance = this.instances[instanceId];
            var config = instance.config;

            // Cancel previous request if any
            if (instance.xhr && instance.xhr.readyState !== 4) {
                instance.xhr.abort();
            }

            // Show loading
            this._showLoading(instanceId);

            // Make API request
            var self = this;
            instance.xhr = $.ajax({
                url: config.url,
                method: 'GET',
                data: { q: query },
                dataType: 'json',
                success: function (data) {
                    self._handleSuccess(instanceId, data);
                },
                error: function (xhr, status, error) {
                    self._handleError(instanceId, error);
                }
            });
        },

        /**
         * Handle successful API response
         */
        _handleSuccess: function (instanceId, data) {
            var instance = this.instances[instanceId];
            instance.results = Array.isArray(data) ? data : [];
            instance.selectedIndex = -1;

            this._renderResults(instanceId);
        },

        /**
         * Handle API error
         */
        _handleError: function (instanceId, error) {
            var instance = this.instances[instanceId];
            console.error('Autocomplete API error:', error);

            if (instance.config.onError && typeof instance.config.onError === 'function') {
                instance.config.onError(error);
            }

            this._closeDropdown(instanceId);
        },

        /**
         * Render results in dropdown
         */
        _renderResults: function (instanceId) {
            var instance = this.instances[instanceId];
            var results = instance.results;
            var $results = instance.$results;
            var config = instance.config;

            // Hide loading
            instance.$loading.hide();

            // Clear previous results
            $results.empty();

            if (results.length === 0) {
                // Show no results message
                instance.$noResults.show();
                instance.$dropdown.show();
                this._positionDropdown(instanceId);
                return;
            }

            // Hide no results message
            instance.$noResults.hide();

            // Render results
            var self = this;
            results.forEach(function (item, index) {
                var displayValue = item[config.displayField] || item.name || item.label || '';
                var itemValue = item[config.valueField] || item.id || item.value || '';

                var $li = $('<li class="autocomplete-item" data-index="' + index + '" data-value="' + 
                    self._escapeHtml(itemValue) + '">' + self._escapeHtml(displayValue) + '</li>');
                $results.append($li);
            });

            // Show dropdown
            instance.$dropdown.show();
            this._positionDropdown(instanceId);
        },

        /**
         * Show loading indicator
         */
        _showLoading: function (instanceId) {
            var instance = this.instances[instanceId];
            instance.$loading.show();
            instance.$noResults.hide();
            instance.$results.empty();
            instance.$dropdown.show();
            this._positionDropdown(instanceId);
        },

        /**
         * Position dropdown below input
         */
        _positionDropdown: function (instanceId) {
            var instance = this.instances[instanceId];
            var $input = instance.$input;
            var $dropdown = instance.$dropdown;
            var $wrapper = $input.parent('.autocomplete-wrapper');

            var inputHeight = $input.outerHeight();
            var inputWidth = $input.outerWidth();

            // Position relative to wrapper (which has position: relative)
            $dropdown.css({
                top: inputHeight + 'px',
                left: '0px',
                width: inputWidth + 'px'
            });
        },

        /**
         * Handle keyboard navigation
         */
        _handleKeydown: function (instanceId, e) {
            var instance = this.instances[instanceId];
            var results = instance.results;

            if (results.length === 0) {
                return;
            }

            switch (e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    this._navigateDown(instanceId);
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    this._navigateUp(instanceId);
                    break;
                case 'Enter':
                    e.preventDefault();
                    if (instance.selectedIndex >= 0 && instance.selectedIndex < results.length) {
                        this._selectItem(instanceId, instance.selectedIndex);
                    }
                    break;
                case 'Escape':
                    e.preventDefault();
                    this._closeDropdown(instanceId);
                    break;
            }
        },

        /**
         * Navigate down in results
         */
        _navigateDown: function (instanceId) {
            var instance = this.instances[instanceId];
            var maxIndex = instance.results.length - 1;

            if (instance.selectedIndex < maxIndex) {
                instance.selectedIndex++;
                this._updateSelection(instanceId);
            }
        },

        /**
         * Navigate up in results
         */
        _navigateUp: function (instanceId) {
            var instance = this.instances[instanceId];

            if (instance.selectedIndex > 0) {
                instance.selectedIndex--;
                this._updateSelection(instanceId);
            } else if (instance.selectedIndex === 0) {
                instance.selectedIndex = -1;
                this._updateSelection(instanceId);
            }
        },

        /**
         * Update visual selection
         */
        _updateSelection: function (instanceId) {
            var instance = this.instances[instanceId];
            var $items = instance.$results.find('li');

            $items.removeClass('active');

            if (instance.selectedIndex >= 0) {
                $items.eq(instance.selectedIndex).addClass('active');
                // Scroll into view
                var $selected = $items.eq(instance.selectedIndex);
                var dropdownHeight = instance.$dropdown.height();
                var itemTop = $selected.position().top;
                var itemHeight = $selected.outerHeight();

                if (itemTop < 0) {
                    instance.$results.scrollTop(instance.$results.scrollTop() + itemTop);
                } else if (itemTop + itemHeight > dropdownHeight) {
                    instance.$results.scrollTop(instance.$results.scrollTop() + (itemTop + itemHeight - dropdownHeight));
                }
            }
        },

        /**
         * Select an item
         */
        _selectItem: function (instanceId, index) {
            var instance = this.instances[instanceId];
            var results = instance.results;

            if (index < 0 || index >= results.length) {
                return;
            }

            var item = results[index];
            var config = instance.config;
            var displayValue = item[config.displayField] || item.name || item.label || '';

            // Update input value
            instance.$input.val(displayValue);

            // Trigger callback if provided
            if (config.onSelect && typeof config.onSelect === 'function') {
                config.onSelect(item, displayValue);
            }

            // Close dropdown
            this._closeDropdown(instanceId);
        },

        /**
         * Close dropdown
         */
        _closeDropdown: function (instanceId) {
            var instance = this.instances[instanceId];
            instance.$dropdown.hide();
            instance.selectedIndex = -1;
        },

        /**
         * Escape HTML to prevent XSS
         */
        _escapeHtml: function (text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function (m) { return map[m]; });
        },

        /**
         * Destroy autocomplete instance
         */
        destroy: function (instanceId) {
            var instance = this.instances[instanceId];
            if (!instance) {
                return;
            }

            // Cancel pending requests
            if (instance.xhr && instance.xhr.readyState !== 4) {
                instance.xhr.abort();
            }

            // Clear timer
            if (instance.debounceTimer) {
                clearTimeout(instance.debounceTimer);
            }

            // Remove UI
            instance.$dropdown.remove();
            instance.$input.unwrap('.autocomplete-wrapper');

            // Remove instance
            delete this.instances[instanceId];
        }
    };

    // Auto-initialize elements with data-autocomplete attribute
    $(document).ready(function () {
        $('[data-autocomplete]').each(function () {
            Autocomplete.init($(this));
        });
    });

    // Expose to global scope
    window.Autocomplete = Autocomplete;

})(jQuery);


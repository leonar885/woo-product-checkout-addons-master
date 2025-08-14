// WPCAM Frontend JavaScript
(function($){
    $(document).ready(function() {
        // Handle form submission - hook into WooCommerce add to cart form
        $('form.cart').on('submit', function(e) {
            const form = $(this);
            const wpacamContainer = form.find('.wpcam-form-container');
            
            if (wpacamContainer.length === 0) {
                return; // No WPCAM form present
            }
            
            // Validate required fields
            let hasErrors = false;
            wpacamContainer.find('.wpcam-field-required input, .wpcam-field-required textarea, .wpcam-field-required select').each(function() {
                const field = $(this);
                let isValid = true;
                
                if (field.attr('type') === 'checkbox') {
                    isValid = field.is(':checked');
                } else {
                    isValid = field.val().trim() !== '';
                }
                
                if (!isValid) {
                    field.addClass('wpcam-error');
                    hasErrors = true;
                } else {
                    field.removeClass('wpcam-error');
                }
            });
            
            if (hasErrors) {
                e.preventDefault();
                alert(wpcam_frontend.strings.validation_error || 'Please fill in all required fields.');
                return false;
            }
            
            const addonsData = {};
            
            // Collect all field data
            wpacamContainer.find('.wpcam-field input, .wpcam-field textarea, .wpcam-field select').each(function() {
                const field = $(this);
                const fieldContainer = field.closest('.wpcam-field');
                const fieldId = fieldContainer.data('field-id') || field.attr('name');
                
                if (fieldId) {
                    let value = field.val();
                    
                    // Handle checkbox
                    if (field.attr('type') === 'checkbox') {
                        value = field.is(':checked') ? '1' : '0';
                    }
                    
                    // Get field configuration (if available)
                    const fieldConfig = fieldContainer.data('field-config') || {};
                    
                    addonsData[fieldId] = {
                        field: fieldConfig,
                        value: value
                    };
                }
            });
            
            // Store data in hidden field
            let addonsInput = form.find('input[name="wpcam_addons"]');
            if (addonsInput.length === 0) {
                addonsInput = $('<input type="hidden" name="wpcam_addons">').appendTo(form);
            }
            addonsInput.val(JSON.stringify(addonsData));
            
            // Also copy the form ID and nonce if present
            wpacamContainer.find('input[name="wpcam_form_id"], input[name="wpcam_nonce"]').each(function() {
                const hidden = $(this);
                let existingInput = form.find('input[name="' + hidden.attr('name') + '"]');
                if (existingInput.length === 0) {
                    form.append(hidden.clone());
                } else {
                    existingInput.val(hidden.val());
                }
            });
        });

        // Real-time price calculation (if pricing info is available)
        $('.wpcam-form-container input, .wpcam-form-container textarea, .wpcam-form-container select').on('change input', function() {
            calculatePricing();
        });

        function calculatePricing() {
            let totalExtra = 0;
            
            $('.wpcam-field').each(function() {
                const fieldContainer = $(this);
                const input = fieldContainer.find('input, textarea, select').first();
                const pricingConfig = fieldContainer.data('pricing');
                
                if (pricingConfig && pricingConfig.method !== 'none') {
                    const value = input.val() || '';
                    let fieldPrice = 0;
                    
                    switch (pricingConfig.method) {
                        case 'fixed':
                            if (input.attr('type') === 'checkbox') {
                                fieldPrice = input.is(':checked') ? (parseFloat(pricingConfig.value) || 0) : 0;
                            } else {
                                fieldPrice = parseFloat(pricingConfig.value) || 0;
                            }
                            break;
                        case 'per_char':
                            fieldPrice = value.length * (parseFloat(pricingConfig.value) || 0);
                            break;
                        case 'multiply':
                            const numValue = parseFloat(value) || 0;
                            fieldPrice = numValue * (parseFloat(pricingConfig.value) || 0);
                            break;
                    }
                    
                    totalExtra += fieldPrice;
                    
                    // Update field pricing display
                    let priceDisplay = fieldContainer.find('.wpcam-pricing-info');
                    if (priceDisplay.length === 0) {
                        priceDisplay = $('<div class="wpcam-pricing-info"></div>');
                        fieldContainer.append(priceDisplay);
                    }
                    
                    if (fieldPrice > 0) {
                        priceDisplay.text(wpcam_frontend.strings.price_prefix + formatCurrency(fieldPrice)).show();
                    } else {
                        priceDisplay.hide();
                    }
                }
            });
            
            // Update total pricing display if element exists
            const totalDisplay = $('.wpcam-total-extra');
            if (totalDisplay.length && totalExtra > 0) {
                totalDisplay.text(formatCurrency(totalExtra)).show();
            } else if (totalDisplay.length) {
                totalDisplay.hide();
            }
        }

        function formatCurrency(amount) {
            // Use WooCommerce currency symbol from localized script
            const symbol = wpcam_frontend.currency_symbol || '$';
            return symbol + amount.toFixed(2);
        }

        // Initial calculation
        calculatePricing();
    });
})(jQuery);

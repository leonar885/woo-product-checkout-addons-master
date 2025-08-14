// WPCAM Frontend JavaScript
(function($){
    $(document).ready(function() {
        // Handle form submission
        $('.wpcam-form').on('submit', function(e) {
            const form = $(this);
            const addonsData = {};
            
            // Collect all field data
            form.find('.wpcam-field input, .wpcam-field textarea, .wpcam-field select').each(function() {
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
            form.find('input[name="wpcam_addons"]').val(JSON.stringify(addonsData));
        });

        // Real-time price calculation (if pricing info is available)
        $('.wpcam-form input, .wpcam-form textarea, .wpcam-form select').on('change input', function() {
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
                            fieldPrice = parseFloat(pricingConfig.value) || 0;
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
                        priceDisplay.text('+' + formatCurrency(fieldPrice)).show();
                    } else {
                        priceDisplay.hide();
                    }
                }
            });
            
            // Update total pricing display if element exists
            const totalDisplay = $('.wpcam-total-extra');
            if (totalDisplay.length && totalExtra > 0) {
                totalDisplay.text(formatCurrency(totalExtra)).show();
            }
        }

        function formatCurrency(amount) {
            // Simple currency formatting - in real implementation, use WooCommerce settings
            return '$' + amount.toFixed(2);
        }

        // Initial calculation
        calculatePricing();
    });
})(jQuery);

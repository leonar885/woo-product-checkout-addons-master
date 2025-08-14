// WPCAM Admin JavaScript
(function($){
    $(function(){
        let fieldIndex = $('.field-editor').length;

        // Add new field
        $('#add-field').on('click', function() {
            const template = $('#field-template').html();
            const html = template.replace(/\{\{INDEX\}\}/g, fieldIndex);
            $('#form-fields-container').append(html);
            updateFieldNumbers();
            fieldIndex++;
            updateEmptyState();
        });

        // Remove field
        $(document).on('click', '.remove-field', function() {
            if (confirm(wpcam_admin.strings.confirm_delete)) {
                $(this).closest('.field-editor').remove();
                updateFieldNumbers();
                updateEmptyState();
            }
        });

        // Auto-generate field name from label
        $(document).on('blur', 'input[name*="[label]"]', function() {
            const $this = $(this);
            const label = $this.val();
            const $nameInput = $this.closest('.field-editor').find('input[name*="[name]"]');
            
            if (!$nameInput.val() && label) {
                const fieldName = label.toLowerCase()
                    .replace(/[^a-z0-9]/g, '_')
                    .replace(/_+/g, '_')
                    .replace(/^_|_$/g, '');
                $nameInput.val(fieldName);
            }
        });

        // Update field pricing visibility
        $(document).on('change', 'select[name*="[pricing][method]"]', function() {
            const $this = $(this);
            const method = $this.val();
            const $valueInput = $this.closest('.field-editor').find('input[name*="[pricing][value]"]');
            
            if (method === 'none') {
                $valueInput.closest('tr').hide();
            } else {
                $valueInput.closest('tr').show();
            }
        });

        // Initialize pricing visibility
        $('select[name*="[pricing][method]"]').each(function() {
            $(this).trigger('change');
        });

        function updateFieldNumbers() {
            $('.field-editor').each(function(index) {
                $(this).find('h4').first().contents().first().replaceWith(
                    wpcam_admin.strings.field_label.replace('%d', index + 1)
                );
                $(this).attr('data-index', index);
            });
        }

        function updateEmptyState() {
            const container = $('#form-fields-container');
            if (container.children('.field-editor').length === 0) {
                container.addClass('empty');
            } else {
                container.removeClass('empty');
            }
        }

        // Initialize empty state
        updateEmptyState();
    });
})(jQuery);

<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wpcam-form-container">
    <h3><?php echo esc_html( $form['title'] ); ?></h3>
    
    <?php foreach ( $form['fields'] as $index => $field ) : ?>
        <?php
            $type = isset( $field['type'] ) ? $field['type'] : 'text';
            $template = WPCAM_PLUGIN_DIR . 'templates/field-' . $type . '.php';
            $field_local = $field;
            $field_index = $index;
            
            if ( file_exists( $template ) ) {
                include $template;
            } else {
                include WPCAM_PLUGIN_DIR . 'templates/field-text.php';
            }
        ?>
    <?php endforeach; ?>

    <input type="hidden" name="wpcam_addons" value="">
    <input type="hidden" name="wpcam_form_id" value="<?php echo esc_attr( $form['id'] ); ?>">
    
    <?php wp_nonce_field( 'wpcam_add_to_cart', 'wpcam_nonce' ); ?>
</div>

<script type="text/javascript">
// Pass form data to JavaScript
window.wpcam_form_data = <?php echo wp_json_encode( $form ); ?>;
</script>

<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<form class="wpcam-form" method="post" data-form-id="<?php echo esc_attr( $form['id'] ); ?>">
    <?php wp_nonce_field( 'wpcam_add_to_cart', 'wpcam_nonce' ); ?>
    <input type="hidden" name="wpcam_form_id" value="<?php echo esc_attr( $form['id'] ); ?>">

    <?php foreach ( $form['fields'] as $field ) : ?>
        <?php
            $type = isset( $field['type'] ) ? $field['type'] : 'text';
            $template = WPCAM_PLUGIN_DIR . 'templates/field-' . $type . '.php';
            $field_local = $field;
            if ( file_exists( $template ) ) {
                include $template;
            } else {
                include WPCAM_PLUGIN_DIR . 'templates/field-text.php';
            }
        ?>
    <?php endforeach; ?>

    <input type="hidden" name="wpcam_addons" value="">
    <button type="submit" class="single_add_to_cart_button button alt"><?php esc_html_e( 'Add to cart', 'wpcam' ); ?></button>
</form>

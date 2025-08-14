<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$name = isset( $field_local['name'] ) ? $field_local['name'] : 'wpcam_field';
$label = isset( $field_local['label'] ) ? $field_local['label'] : '';
$default = isset( $field_local['default'] ) ? $field_local['default'] : '';
?>
<p class="wpcam-field wpcam-field-text">
    <label><?php echo esc_html( $label ); ?>
        <input type="text" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $default ); ?>">
    </label>
</p>

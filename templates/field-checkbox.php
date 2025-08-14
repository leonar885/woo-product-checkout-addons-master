<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$name = isset( $field_local['name'] ) ? $field_local['name'] : 'wpcam_field_' . $field_index;
$label = isset( $field_local['label'] ) ? $field_local['label'] : '';
$default = isset( $field_local['default'] ) ? $field_local['default'] : '';
$required = isset( $field_local['required'] ) && $field_local['required'];
$pricing = isset( $field_local['pricing'] ) ? $field_local['pricing'] : ['method' => 'none', 'value' => ''];

$field_classes = 'wpcam-field wpcam-field-checkbox';
if ( $required ) {
    $field_classes .= ' wpcam-field-required';
}

$is_checked = ! empty( $default ) && ( $default === '1' || $default === 'yes' || $default === 'true' );
?>
<p class="<?php echo esc_attr( $field_classes ); ?>" 
   data-field-id="<?php echo esc_attr( $name ); ?>" 
   data-field-config="<?php echo esc_attr( wp_json_encode( $field_local ) ); ?>"
   data-pricing="<?php echo esc_attr( wp_json_encode( $pricing ) ); ?>">
   
    <label for="<?php echo esc_attr( $name ); ?>">
        <input type="checkbox" 
               id="<?php echo esc_attr( $name ); ?>" 
               name="<?php echo esc_attr( $name ); ?>" 
               value="1"
               <?php checked( $is_checked ); ?>
               <?php echo $required ? 'required' : ''; ?>>
        <?php echo esc_html( $label ); ?>
        <?php if ( $required ) : ?><span class="required">*</span><?php endif; ?>
    </label>
           
    <?php if ( $pricing['method'] !== 'none' && ! empty( $pricing['value'] ) ) : ?>
        <div class="wpcam-pricing-info" style="display: none;"></div>
    <?php endif; ?>
</p>
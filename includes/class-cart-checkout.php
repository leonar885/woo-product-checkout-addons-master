<?php

namespace Wooqui\WPCAM;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Cart_Checkout {
    public function __construct() {
        add_filter( 'woocommerce_add_cart_item_data', [ $this, 'add_cart_item_data' ], 10, 2 );
        add_action( 'woocommerce_before_calculate_totals', [ $this, 'before_calculate_totals' ], 10, 1 );
        add_filter( 'woocommerce_get_item_data', [ $this, 'display_cart_item_data' ], 10, 2 );
        add_action( 'woocommerce_checkout_create_order_line_item', [ $this, 'add_order_item_meta' ], 10, 4 );
    }

    public function add_cart_item_data( $cart_item_data, $product_id ) {
        if ( isset( $_POST['wpcam_addons'] ) ) {
            // Verify nonce for security
            if ( ! isset( $_POST['wpcam_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['wpcam_nonce'] ), 'wpcam_add_to_cart' ) ) {
                wc_add_notice( __( 'Security check failed. Please try again.', 'wpcam' ), 'error' );
                return $cart_item_data;
            }
            
            $raw = wp_unslash( $_POST['wpcam_addons'] );
            $data = json_decode( $raw, true );
            if ( is_array( $data ) ) {
                // Sanitize and validate each field
                $sanitized_data = [];
                $form_id = isset( $_POST['wpcam_form_id'] ) ? intval( $_POST['wpcam_form_id'] ) : 0;
                $form = Forms::get_form( $form_id );
                
                if ( $form && ! empty( $form['fields'] ) ) {
                    // Create a lookup for form fields by name
                    $form_fields = [];
                    foreach ( $form['fields'] as $field ) {
                        if ( isset( $field['name'] ) ) {
                            $form_fields[$field['name']] = $field;
                        }
                    }
                    
                    foreach ( $data as $field_name => $field_data ) {
                        // Validate field exists in form
                        if ( ! isset( $form_fields[$field_name] ) ) {
                            continue;
                        }
                        
                        $form_field = $form_fields[$field_name];
                        $value = isset( $field_data['value'] ) ? sanitize_text_field( $field_data['value'] ) : '';
                        
                        // Validate required fields
                        if ( isset( $form_field['required'] ) && $form_field['required'] && empty( $value ) ) {
                            wc_add_notice( sprintf( __( 'Field "%s" is required.', 'wpcam' ), esc_html( $form_field['label'] ?? $field_name ) ), 'error' );
                            return $cart_item_data;
                        }
                        
                        // Validate field type
                        $field_type = $form_field['type'] ?? 'text';
                        if ( $field_type === 'number' && ! is_numeric( $value ) && ! empty( $value ) ) {
                            wc_add_notice( sprintf( __( 'Field "%s" must be a number.', 'wpcam' ), esc_html( $form_field['label'] ?? $field_name ) ), 'error' );
                            return $cart_item_data;
                        }
                        
                        $sanitized_data[$field_name] = [
                            'field' => $form_field,
                            'value' => $value
                        ];
                    }
                }
                
                $cart_item_data['wpcam_addons'] = $sanitized_data;
            }
        }
        if ( isset( $_POST['wpcam_form_id'] ) ) {
            $cart_item_data['wpcam_form_id'] = intval( $_POST['wpcam_form_id'] );
        }
        return $cart_item_data;
    }

    public function before_calculate_totals( $cart ) {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            return;
        }
        foreach ( $cart->get_cart() as $key => $item ) {
            if ( isset( $item['wpcam_addons'] ) && is_array( $item['wpcam_addons'] ) ) {
                $product = $item['data'];
                $extra = 0;
                foreach ( $item['wpcam_addons'] as $field_id => $field_data ) {
                    // field_data expected ['field'=>..., 'value'=>...]
                    $field = isset( $field_data['field'] ) ? $field_data['field'] : null;
                    $value = isset( $field_data['value'] ) ? $field_data['value'] : null;
                    if ( $field ) {
                        $context = [
                            'base_price' => floatval( $product->get_price() ),
                            'quantity' => $item['quantity']
                        ];
                        $p = Pricing::compute_field_price( $field, $value, $context );
                        $extra += floatval( $p );
                    }
                }
                if ( $extra > 0 ) {
                    $price = floatval( $product->get_price() );
                    $new_price = $price + $extra;
                    $item['data']->set_price( $new_price );
                }
            }
        }
    }

    public function display_cart_item_data( $item_data, $cart_item ) {
        if ( isset( $cart_item['wpcam_addons'] ) && is_array( $cart_item['wpcam_addons'] ) ) {
            foreach ( $cart_item['wpcam_addons'] as $k => $v ) {
                $label = isset( $v['field']['label'] ) ? $v['field']['label'] : $k;
                $value = isset( $v['value'] ) ? $v['value'] : '';
                $item_data[] = [ 'key' => $label, 'value' => wc_clean( $value ) ];
            }
        }
        return $item_data;
    }

    public function add_order_item_meta( $item, $cart_item_key, $values, $order ) {
        if ( isset( $values['wpcam_addons'] ) && is_array( $values['wpcam_addons'] ) ) {
            $item->add_meta_data( '_wpcam_addons', wp_json_encode( $values['wpcam_addons'] ), true );
        }
    }
}

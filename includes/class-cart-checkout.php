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
            $raw = wp_unslash( $_POST['wpcam_addons'] );
            $data = json_decode( $raw, true );
            if ( is_array( $data ) ) {
                $cart_item_data['wpcam_addons'] = $data;
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

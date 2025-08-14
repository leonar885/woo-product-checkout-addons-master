<?php

namespace Wooqui\WPCAM;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Render {
    public function __construct() {
        add_action( 'woocommerce_before_add_to_cart_button', [ $this, 'render_form' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ] );
    }

    public function enqueue_frontend_assets() {
        // Only enqueue on product pages
        if ( is_product() ) {
            wp_enqueue_style( 'wpcam-frontend', WPCAM_PLUGIN_URL . 'assets/frontend.css', [], WPCAM_VERSION );
            wp_enqueue_script( 'wpcam-frontend', WPCAM_PLUGIN_URL . 'assets/frontend.js', [ 'jquery' ], WPCAM_VERSION, true );
        }
    }

    public function render_form() {
        global $product;
        if ( ! $product instanceof \WC_Product ) {
            return;
        }
        $form_id = get_post_meta( $product->get_id(), 'wpcam_form_id', true );
        if ( ! $form_id ) {
            return;
        }
        $form = Forms::get_form( $form_id );
        if ( ! $form ) {
            return;
        }
        include WPCAM_PLUGIN_DIR . 'templates/form.php';
    }
}

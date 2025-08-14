<?php

namespace Wooqui\WPCAM;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Loader {
    private static $instance;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
            self::$instance->setup();
        }
        return self::$instance;
    }

    public function setup() {
        // register CPT for forms
        add_action( 'init', [ $this, 'register_post_types' ] );

        // include classes
        $files = [
            'includes/class-admin.php',
            'includes/class-forms.php',
            'includes/class-render.php',
            'includes/class-pricing.php',
            'includes/class-conditions.php',
            'includes/class-cart-checkout.php',
            'includes/class-rest.php',
            'includes/helpers.php',
        ];
        foreach ( $files as $f ) {
            $path = WPCAM_PLUGIN_DIR . $f;
            if ( file_exists( $path ) ) {
                require_once $path;
            }
        }

        // instantiate classes that need to be initialized
        if ( is_admin() ) {
            new Admin();
        }
        
        // Initialize forms admin hooks
        Forms::init_admin_metabox();
        
        // Initialize frontend rendering and cart/checkout handling
        new Render();
        new Cart_Checkout();
        new Rest();
    }

    public function register_post_types() {
        register_post_type( 'wpcam_form', [
            'label' => __( 'WPCAM Forms', 'wpcam' ),
            'public' => false,
            'show_ui' => true,
            'supports' => [ 'title' ],
        ] );
    }
}

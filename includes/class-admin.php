<?php

namespace Wooqui\WPCAM;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Admin {
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
    }

    public function menu() {
        add_menu_page( __( 'WPCAM', 'wpcam' ), __( 'WPCAM', 'wpcam' ), 'manage_options', 'wpcam', [ $this, 'page' ], 'dashicons-welcome-widgets-menus' );
    }

    public function page() {
        echo '<div class="wrap"><h1>' . esc_html__( 'Woo Product & Checkout Addons Master', 'wpcam' ) . '</h1>';
        echo '<p>' . esc_html__( 'Manage forms and settings here.', 'wpcam' ) . '</p></div>';
    }

    public function enqueue( $hook ) {
        // only load on plugin pages
        wp_enqueue_style( 'wpcam-admin', WPCAM_PLUGIN_URL . 'assets/admin.css', [], WPCAM_VERSION );
        wp_enqueue_script( 'wpcam-admin', WPCAM_PLUGIN_URL . 'assets/admin.js', [ 'jquery' ], WPCAM_VERSION, true );
    }
}

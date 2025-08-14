<?php
/**
 * Plugin Name: Woo Product & Checkout Addons Master
 * Plugin URI:  https://github.com/leonar885/woo-product-checkout-addons-master
 * Description: Advanced product & checkout addons for WooCommerce — scaffold and core features.
 * Version:     0.1.0
 * Author:      Leonardo Mendoza (Wooqui)
 * Author URI:  https://wooqui.com
 * License:     MIT
 * Text Domain: wpcam
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define some constants
if ( ! defined( 'WPCAM_PLUGIN_FILE' ) ) {
    define( 'WPCAM_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'WPCAM_PLUGIN_DIR' ) ) {
    define( 'WPCAM_PLUGIN_DIR', plugin_dir_path( WPCAM_PLUGIN_FILE ) );
}
if ( ! defined( 'WPCAM_VERSION' ) ) {
    define( 'WPCAM_VERSION', '0.1.0' );
}

require_once __DIR__ . '/includes/class-loader.php';
\Wooqui\WPCAM\Loader::instance();

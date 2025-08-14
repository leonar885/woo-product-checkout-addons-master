<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

// safe cleanup
if ( function_exists( 'delete_option' ) ) {
    global $wpdb;

    // remove transient/options created by plugin
    $options = [
        'wpcam_cache_forms',
        // add others if needed
    ];
    foreach ( $options as $o ) {
        delete_option( $o );
    }

    // remove postmeta for our CPT
    $wpdb->query( "DELETE pm FROM {$wpdb->postmeta} pm JOIN {$wpdb->posts} p ON p.ID = pm.post_id WHERE p.post_type = 'wpcam_form'" );
}

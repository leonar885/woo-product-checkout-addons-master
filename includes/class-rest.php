<?php

namespace Wooqui\WPCAM;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Rest {
    public function __construct() {
        add_action( 'rest_api_init', [ $this, 'routes' ] );
    }

    public function routes() {
        register_rest_route( 'wpcam/v1', '/forms/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [ $this, 'get_form' ],
            'permission_callback' => function() {
                return current_user_can( 'manage_woocommerce' );
            }
        ] );
    }

    public function get_form( $request ) {
        $id = intval( $request['id'] );
        $form = Forms::get_form( $id );
        if ( ! $form ) {
            return new \WP_Error( 'not_found', __( 'Form not found', 'wpcam' ), [ 'status' => 404 ] );
        }
        return rest_ensure_response( $form );
    }
}

<?php

namespace Wooqui\WPCAM;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function sanitize_text_field_strict( $v ) {
    return is_string( $v ) ? sanitize_text_field( $v ) : '';
}

function wpcam_escape_attr( $v ) {
    return esc_attr( $v );
}

function wpcam_intval( $v ) {
    return intval( $v );
}

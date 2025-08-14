<?php

namespace Wooqui\WPCAM;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Forms {
    public static function get_form( $id ) {
        $post = get_post( $id );
        if ( ! $post || 'wpcam_form' !== $post->post_type ) {
            return null;
        }
        $meta = get_post_meta( $id, 'wpcam_fields', true );
        if ( ! $meta ) {
            return [
                'id' => $id,
                'title' => $post->post_title,
                'fields' => [],
            ];
        }
        $data = json_decode( $meta, true );
        if ( ! is_array( $data ) ) {
            return null;
        }
        return [
            'id' => $id,
            'title' => $post->post_title,
            'fields' => $data,
        ];
    }

    public static function init_admin_metabox() {
        add_action( 'add_meta_boxes', function() {
            add_meta_box( 'wpcam_product_forms', __( 'WPCAM Forms', 'wpcam' ), [ __CLASS__, 'render_product_metabox' ], 'product', 'side', 'default' );
        } );

        add_action( 'save_post_product', [ __CLASS__, 'save_product_meta' ], 10, 2 );
    }

    public static function render_product_metabox( $post ) {
        $value = get_post_meta( $post->ID, 'wpcam_form_id', true );
        wp_nonce_field( 'wpcam_save_product', 'wpcam_nonce' );
        $forms = get_posts( [ 'post_type' => 'wpcam_form', 'posts_per_page' => -1 ] );
        echo '<label for="wpcam_form_id">' . esc_html__( 'Attach Form', 'wpcam' ) . '</label>';
        echo '<select name="wpcam_form_id" id="wpcam_form_id">';
        echo '<option value="">' . esc_html__( '— None —', 'wpcam' ) . '</option>';
        foreach ( $forms as $f ) {
            printf( '<option value="%d" %s>%s</option>', $f->ID, selected( $value, $f->ID, false ), esc_html( $f->post_title ) );
        }
        echo '</select>';
    }

    public static function save_product_meta( $post_id, $post ) {
        if ( ! isset( $_POST['wpcam_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['wpcam_nonce'] ), 'wpcam_save_product' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( empty( $_POST['wpcam_form_id'] ) ) {
            delete_post_meta( $post_id, 'wpcam_form_id' );
        } else {
            update_post_meta( $post_id, 'wpcam_form_id', intval( $_POST['wpcam_form_id'] ) );
        }
    }
}

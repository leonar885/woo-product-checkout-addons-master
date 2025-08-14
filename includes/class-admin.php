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
        $action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : 'list';
        $form_id = isset( $_GET['form_id'] ) ? intval( $_GET['form_id'] ) : 0;

        echo '<div class="wrap">';
        echo '<h1>' . esc_html__( 'Woo Product & Checkout Addons Master', 'wpcam' ) . '</h1>';
        
        switch ( $action ) {
            case 'edit':
                $this->edit_form_page( $form_id );
                break;
            case 'new':
                $this->edit_form_page( 0 );
                break;
            default:
                $this->list_forms_page();
                break;
        }
        
        echo '</div>';
    }

    private function list_forms_page() {
        $forms = get_posts( [
            'post_type' => 'wpcam_form',
            'posts_per_page' => -1,
            'post_status' => 'any'
        ] );

        echo '<div class="tablenav top">';
        echo '<a href="' . esc_url( admin_url( 'admin.php?page=wpcam&action=new' ) ) . '" class="button button-primary">' . esc_html__( 'Add New Form', 'wpcam' ) . '</a>';
        echo '</div>';

        if ( empty( $forms ) ) {
            echo '<p>' . esc_html__( 'No forms found. Create your first form to get started.', 'wpcam' ) . '</p>';
            return;
        }

        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead>';
        echo '<tr>';
        echo '<th scope="col">' . esc_html__( 'Title', 'wpcam' ) . '</th>';
        echo '<th scope="col">' . esc_html__( 'Fields', 'wpcam' ) . '</th>';
        echo '<th scope="col">' . esc_html__( 'Actions', 'wpcam' ) . '</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ( $forms as $form ) {
            $form_data = Forms::get_form( $form->ID );
            $field_count = is_array( $form_data['fields'] ) ? count( $form_data['fields'] ) : 0;
            
            echo '<tr>';
            echo '<td><strong><a href="' . esc_url( admin_url( 'admin.php?page=wpcam&action=edit&form_id=' . $form->ID ) ) . '">' . esc_html( $form->post_title ) . '</a></strong></td>';
            echo '<td>' . sprintf( _n( '%d field', '%d fields', $field_count, 'wpcam' ), $field_count ) . '</td>';
            echo '<td>';
            echo '<a href="' . esc_url( admin_url( 'admin.php?page=wpcam&action=edit&form_id=' . $form->ID ) ) . '" class="button">' . esc_html__( 'Edit', 'wpcam' ) . '</a> ';
            echo '<a href="' . esc_url( get_delete_post_link( $form->ID ) ) . '" class="button" onclick="return confirm(\'' . esc_js( __( 'Are you sure you want to delete this form?', 'wpcam' ) ) . '\')">' . esc_html__( 'Delete', 'wpcam' ) . '</a>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    }

    private function edit_form_page( $form_id ) {
        $form_data = $form_id ? Forms::get_form( $form_id ) : [
            'id' => 0,
            'title' => '',
            'fields' => []
        ];

        if ( $form_id && ! $form_data ) {
            echo '<div class="notice notice-error"><p>' . esc_html__( 'Form not found.', 'wpcam' ) . '</p></div>';
            return;
        }

        // Handle form submission
        if ( isset( $_POST['save_form'] ) && check_admin_referer( 'save_wpcam_form' ) ) {
            $this->save_form( $form_id );
            return;
        }

        $title = $form_id ? esc_html__( 'Edit Form', 'wpcam' ) : esc_html__( 'New Form', 'wpcam' );
        echo '<h2>' . $title . '</h2>';

        echo '<form method="post" action="">';
        wp_nonce_field( 'save_wpcam_form' );
        
        echo '<table class="form-table">';
        echo '<tr>';
        echo '<th scope="row"><label for="form_title">' . esc_html__( 'Form Title', 'wpcam' ) . '</label></th>';
        echo '<td><input type="text" id="form_title" name="form_title" value="' . esc_attr( $form_data['title'] ) . '" class="regular-text" required /></td>';
        echo '</tr>';
        echo '</table>';

        echo '<h3>' . esc_html__( 'Form Fields', 'wpcam' ) . '</h3>';
        echo '<div id="form-fields-container">';
        
        if ( ! empty( $form_data['fields'] ) ) {
            foreach ( $form_data['fields'] as $index => $field ) {
                $this->render_field_editor( $index, $field );
            }
        }
        
        echo '</div>';

        echo '<button type="button" id="add-field" class="button">' . esc_html__( 'Add Field', 'wpcam' ) . '</button>';
        echo '<br><br>';
        
        echo '<input type="submit" name="save_form" value="' . esc_attr__( 'Save Form', 'wpcam' ) . '" class="button-primary" />';
        echo ' <a href="' . esc_url( admin_url( 'admin.php?page=wpcam' ) ) . '" class="button">' . esc_html__( 'Cancel', 'wpcam' ) . '</a>';
        echo '</form>';

        // Add the field template for JavaScript
        echo '<script type="text/template" id="field-template">';
        $this->render_field_editor( '{{INDEX}}', [] );
        echo '</script>';
    }

    private function render_field_editor( $index, $field ) {
        $field = wp_parse_args( $field, [
            'type' => 'text',
            'name' => '',
            'label' => '',
            'default' => '',
            'required' => false,
            'pricing' => [
                'method' => 'none',
                'value' => ''
            ]
        ] );

        echo '<div class="field-editor" data-index="' . esc_attr( $index ) . '">';
        echo '<h4>' . sprintf( esc_html__( 'Field #%s', 'wpcam' ), $index + 1 ) . ' <button type="button" class="remove-field button-link-delete">' . esc_html__( 'Remove', 'wpcam' ) . '</button></h4>';
        
        echo '<table class="form-table">';
        
        echo '<tr>';
        echo '<th scope="row"><label>' . esc_html__( 'Field Type', 'wpcam' ) . '</label></th>';
        echo '<td>';
        echo '<select name="fields[' . $index . '][type]">';
        $types = [ 'text' => __( 'Text', 'wpcam' ), 'textarea' => __( 'Textarea', 'wpcam' ), 'number' => __( 'Number', 'wpcam' ), 'checkbox' => __( 'Checkbox', 'wpcam' ) ];
        foreach ( $types as $value => $label ) {
            echo '<option value="' . esc_attr( $value ) . '"' . selected( $field['type'], $value, false ) . '>' . esc_html( $label ) . '</option>';
        }
        echo '</select>';
        echo '</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th scope="row"><label>' . esc_html__( 'Field Name', 'wpcam' ) . '</label></th>';
        echo '<td><input type="text" name="fields[' . $index . '][name]" value="' . esc_attr( $field['name'] ) . '" class="regular-text" /></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th scope="row"><label>' . esc_html__( 'Label', 'wpcam' ) . '</label></th>';
        echo '<td><input type="text" name="fields[' . $index . '][label]" value="' . esc_attr( $field['label'] ) . '" class="regular-text" /></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th scope="row"><label>' . esc_html__( 'Default Value', 'wpcam' ) . '</label></th>';
        echo '<td><input type="text" name="fields[' . $index . '][default]" value="' . esc_attr( $field['default'] ) . '" class="regular-text" /></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th scope="row"><label>' . esc_html__( 'Required', 'wpcam' ) . '</label></th>';
        echo '<td><input type="checkbox" name="fields[' . $index . '][required]" value="1"' . checked( $field['required'], true, false ) . ' /></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th scope="row"><label>' . esc_html__( 'Pricing Method', 'wpcam' ) . '</label></th>';
        echo '<td>';
        echo '<select name="fields[' . $index . '][pricing][method]">';
        $pricing_methods = [
            'none' => __( 'No additional cost', 'wpcam' ),
            'fixed' => __( 'Fixed amount', 'wpcam' ),
            'percent' => __( 'Percentage', 'wpcam' ),
            'per_char' => __( 'Per character', 'wpcam' ),
            'multiply' => __( 'Multiply by value', 'wpcam' ),
            'formula' => __( 'Custom formula', 'wpcam' )
        ];
        foreach ( $pricing_methods as $value => $label ) {
            echo '<option value="' . esc_attr( $value ) . '"' . selected( $field['pricing']['method'], $value, false ) . '>' . esc_html( $label ) . '</option>';
        }
        echo '</select>';
        echo '</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th scope="row"><label>' . esc_html__( 'Pricing Value', 'wpcam' ) . '</label></th>';
        echo '<td><input type="text" name="fields[' . $index . '][pricing][value]" value="' . esc_attr( $field['pricing']['value'] ) . '" class="regular-text" /></td>';
        echo '</tr>';

        echo '</table>';
        echo '</div>';
    }

    private function save_form( $form_id ) {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You do not have permission to perform this action.', 'wpcam' ) );
        }

        $title = sanitize_text_field( $_POST['form_title'] );
        $fields = isset( $_POST['fields'] ) ? $_POST['fields'] : [];

        // Sanitize fields
        $sanitized_fields = [];
        foreach ( $fields as $field ) {
            $sanitized_fields[] = [
                'type' => sanitize_text_field( $field['type'] ?? 'text' ),
                'name' => sanitize_text_field( $field['name'] ?? '' ),
                'label' => sanitize_text_field( $field['label'] ?? '' ),
                'default' => sanitize_text_field( $field['default'] ?? '' ),
                'required' => isset( $field['required'] ),
                'pricing' => [
                    'method' => sanitize_text_field( $field['pricing']['method'] ?? 'none' ),
                    'value' => sanitize_text_field( $field['pricing']['value'] ?? '' )
                ]
            ];
        }

        if ( $form_id ) {
            // Update existing form
            wp_update_post( [
                'ID' => $form_id,
                'post_title' => $title
            ] );
        } else {
            // Create new form
            $form_id = wp_insert_post( [
                'post_type' => 'wpcam_form',
                'post_title' => $title,
                'post_status' => 'publish'
            ] );
        }

        if ( $form_id ) {
            update_post_meta( $form_id, 'wpcam_fields', wp_json_encode( $sanitized_fields ) );
            
            echo '<div class="notice notice-success"><p>' . esc_html__( 'Form saved successfully!', 'wpcam' ) . '</p></div>';
            echo '<script>setTimeout(function(){ window.location.href = "' . esc_js( admin_url( 'admin.php?page=wpcam' ) ) . '"; }, 1500);</script>';
        } else {
            echo '<div class="notice notice-error"><p>' . esc_html__( 'Error saving form. Please try again.', 'wpcam' ) . '</p></div>';
        }
    }

    public function enqueue( $hook ) {
        // only load on plugin pages
        if ( strpos( $hook, 'wpcam' ) === false ) {
            return;
        }
        
        wp_enqueue_style( 'wpcam-admin', WPCAM_PLUGIN_URL . 'assets/admin.css', [], WPCAM_VERSION );
        wp_enqueue_script( 'wpcam-admin', WPCAM_PLUGIN_URL . 'assets/admin.js', [ 'jquery' ], WPCAM_VERSION, true );
        
        // Localize script for AJAX and translations
        wp_localize_script( 'wpcam-admin', 'wpcam_admin', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'wpcam_admin' ),
            'strings' => [
                'confirm_delete' => __( 'Are you sure you want to delete this field?', 'wpcam' ),
                'field_name_placeholder' => __( 'field_name', 'wpcam' ),
                'field_label' => __( 'Field #%d', 'wpcam' )
            ]
        ] );
    }
}

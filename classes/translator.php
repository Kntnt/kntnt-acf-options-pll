<?php

namespace Kntnt\ACF_Options_Pll;

class Translator {

    private $slug = '';

    public function run() {

        // Makes ACF language aware.
        add_filter( 'acf/settings/current_language', [ $this, 'current_language' ] );

        // Stores the slug of the current option page.
        add_filter( 'acf/pre_load_value', [ $this, 'pre_load_value' ], 10, 3 );     // Frontend
        add_filter( 'acf/get_options_page', [ $this, 'get_options_page' ], 10, 2 ); // Backend

        // Ensures the option pages for current language is used.
        add_filter( 'acf/pre_load_post_id', [ $this, 'pre_load_post_id' ], 10, 2 );   // Frontend (and partly also backend)
        add_filter( 'acf/pre_load_metadata', [ $this, 'pre_load_metadata' ], 10, 4 ); // Backend

    }

    // Returns the current language if set.
    public function current_language() {
        return pll_current_language( 'locale' ) ?: '';
    }

    // Is used in the frontend to save slug of the option page.
    public function pre_load_value( $value, $post_id, $field ) {
        $this->slug = '';
        if ( 'options' == $post_id ) {
            $slug = $this->get_slug( $field );
            if ( $this->is_translatable( $slug ) ) {
                $this->slug = $slug;
                Plugin::log( "Translation is enabled for %s.", $field['name'] );
            }
            else {
                Plugin::log( "Translation is disabled for %s.", $field['name'] );
            }
        }
        return $value;
    }

    // Is used on the backend to save slug of the option page.
    public function get_options_page( $page, $slug ) {
        $this->slug = $slug;
        return $page;
    }

    // Short-circuit acf_get_valid_post_id() for option pages and enforce
    // `options` as page id. Adds language code as prefix in the backend.
    public function pre_load_post_id( $post_id, $original_post_id ) {
        $_post_id = $original_post_id;
        if ( is_string( $_post_id ) && 'option' == substr( $_post_id, 0, 6 ) ) {
            $_post_id = 'options';
            if ( $this->is_translatable( $this->slug ) ) {
                $_post_id = $this->translate( $_post_id );
            }
            Plugin::log( 'Before filter "acf/validate_post_id": %s => %s', $original_post_id, $_post_id );
            $post_id = apply_filters( 'acf/validate_post_id', $_post_id, $original_post_id );
            Plugin::log( 'After filter "acf/validate_post_id":  %s => %s', $original_post_id, $post_id );
        }
        return $post_id;
    }

    // Returns the value of the translated option page.
    public function pre_load_metadata( $metadata, $post_id, $name, $hidden ) {
        if ( $this->slug ) {
            $post_id = $this->translate( $post_id );
            $key = ( $hidden ? '_' : '' ) . "{$post_id}_{$name}";
            $metadata = get_option( $key, null );
            Plugin::log( "Option %s holds the value of %s at %s: %s", $key, $name, $post_id, $metadata );
        }
        return $metadata;
    }

    // Extract the slug of the option page defining $field.
    private function get_slug( $field ) {
        $post = get_post( $field['parent'] );
        $content = unserialize( $post->post_content );
        return $content['location'][0][0]['value'];
    }

    // Returns true iff option page with $slug is translatable.
    private function is_translatable( $slug ) {
        return $slug && in_array( $slug, Plugin::option( 'translatable_option_pages' ) );
    }

    // Translate post_id.
    private function translate( $post_id ) {
        $dl = acf_get_setting( 'default_language' );
        $cl = acf_get_setting( 'current_language' );
        if ( $cl && $cl !== $dl ) {
            $post_id .= '_' . $cl;
        }
        return $post_id;
    }

}

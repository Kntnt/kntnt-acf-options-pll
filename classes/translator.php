<?php

namespace Kntnt\ACF_Options_Pll;

class Translator {

    public function __construct() {
        add_filter( 'acf/validate_options_page', [ $this, 'add_slug_to_acf_post_options_page_id' ] );
    }

    public function run() {
        add_filter( 'acf/settings/current_language', [ $this, 'current_language' ] );
        add_filter( 'acf/pre_load_post_id', [ $this, 'pre_load_post_id' ], 10, 2 );
        add_filter( 'acf/pre_load_metadata', [ $this, 'pre_load_metadata' ], 10, 4 );
        add_filter( 'acf/pre_load_value', [ $this, 'pre_load_value' ], 10, 3 );
    }

    public function current_language() {
        return pll_current_language( 'locale' );
    }

    public function add_slug_to_acf_post_options_page_id( $page ) {
        $slug = $page['menu_slug'];
        $translatable_option_pages = Plugin::option( 'translatable_option_pages' ) ?: [];
        $page['post_id'] = in_array( $slug, $translatable_option_pages ) ? "options-$slug" : 'options';
        return $page;
    }

    public function pre_load_post_id( $post_id, $original_post_id ) {

        // Save `$original_post_id` for the below application of
        // 'acf/validate_post_id' filters.
        $_post_id = $original_post_id;

        // Short-circuit `acf_get_valid_post_id()` if `$post_id` is `option` or
        // `options` or `options-…`. This will only happens on the option pages
        // themselves.
        if ( 'option' == substr( $_post_id, 0, 6 ) ) {

            // If if `$post_id` is `options-…`, $slug is assigned … which is
            // the menu slug of the option page.
            $slug = substr( $_post_id, 8 );

            // ACF uses `options` irrespective of the initial value of
            // `$_post_id`.
            $_post_id = 'options';

            // If the original `$post_id` is `options-$slug` and if the option
            // page identified by $slug is translatable, allow translation.
            if ( $this->is_translatable( $slug ) ) {
                $_post_id = $this->translate( $_post_id );
            }

            Plugin::log( 'Before filter "acf/validate_post_id": %s => %s', $original_post_id, $_post_id );

            // Mimic the behaviour of the short-circuited
            // `acf_get_valid_post_id()`.
            $post_id = apply_filters( 'acf/validate_post_id', $_post_id, $original_post_id );

            Plugin::log( 'After filter "acf/validate_post_id":  %s => %s', $original_post_id, $post_id );

        }

        return $post_id;

    }

    public function pre_load_metadata( $metadata, $post_id, $name, $hidden ) {
        if ( isset( $this->slug ) && $this->slug ) {
            $post_id = $this->translate( $post_id );
            $key = ( $hidden ? '_' : '' ) . "{$post_id}_{$name}";
            $metadata = get_option( $key, null );
            Plugin::log( "Option %s holds the value of %s at %s: %s", $key, $name, $post_id, $metadata );
        }
        return $metadata;
    }

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

    private function is_translatable( $slug ) {
        return $slug && in_array( $slug, Plugin::option( 'translatable_option_pages' ) );
    }

    private function get_slug( $field ) {
        $post = get_post( $field['parent'] );
        $content = unserialize( $post->post_content );
        return $content['location'][0][0]['value'];
    }

    private function translate( $post_id ) {
        $dl = acf_get_setting( 'default_language' );
        $cl = acf_get_setting( 'current_language' );
        if ( $cl && $cl !== $dl ) {
            $post_id .= '_' . $cl;
        }
        return $post_id;
    }

}
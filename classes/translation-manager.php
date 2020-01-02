<?php

namespace Kntnt\ACF_Options_Pll;

class Translation_Manager {

    private $translate = true;

    private $slug = '';

    public function run() {
        add_filter( 'acf/get_options_page', [ $this, 'get_options_page' ], 10, 2 );
        add_filter( 'acf/pre_load_post_id', [ $this, 'pre_load_post_id' ], 10, 2 );
        add_filter( 'acf/settings/current_language', [ $this, 'current_language' ] );
    }

    public function get_options_page( $page, $slug ) {
        Plugin::log( 'Option page %s.', $slug );
        $this->slug = $slug;
        return $page;
    }

    public function pre_load_post_id( $post_id, $original_post_id ) {
        if ( $this->translate ) {
            if ( ! in_array( $this->slug, Plugin::option( 'translateable_option_pages', [] ) ) ) {
                Plugin::log( 'Deny translation of %s.', $this->slug );
                $this->translate = false;
                $post_id = acf_get_valid_post_id( $original_post_id );
                $this->translate = true;
            }
            else {
                Plugin::log( 'Allow translation of %s.', $this->slug );
            }
        }
        return $post_id;
    }

    public function current_language( $lang ) {
        if ( $this->translate ) {
            $lang = pll_current_language( 'locale' );
        }
        Plugin::log( 'Language code: %s', $lang );
        return $lang;
    }

}
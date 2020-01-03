<?php

defined( 'WP_UNINSTALL_PLUGIN' ) || die;

delete_option( 'kntnt-acf-options-pll' );

// Restore the post_id field of ACF Option Pages to 'options'.
if ( function_exists( 'acf_options_page' ) ) {
    foreach ( acf_options_page()->get_pages() as $slug => $page ) {
        acf_options_page()->update_page( 'acf-options', [
            'post_id' => 'options',
        ] );
    }
}
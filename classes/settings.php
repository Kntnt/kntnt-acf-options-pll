<?php

namespace Kntnt\ACF_Options_Pll;

class Settings extends Abstract_Settings {

    /**
     * Returns the settings menu title.
     */
    protected function menu_title() {
        return __( 'Polylang for ACF Options', 'kntnt-acf-options-pll' );
    }

    /**
     * Returns the settings page title.
     */
    protected function page_title() {
        return __( "Kntnt Compatibility plugin for Polylang and Advanced Custom Field Option Pages", 'kntnt-acf-options-pll' );
    }

    /**
     * Returns all fields used on the settings page.
     */
    protected function fields() {

        $fields['translatable_option_pages'] = [
            'type' => 'checkbox group',
            'label' => __( 'ACF Option Pages', 'kntnt-acf-options-pll' ),
            'description' => __( 'Check ACF Option pages that should be translatable.', 'kntnt-acf-options-pll' ),
            'options' => wp_list_pluck( acf_get_options_pages(), 'page_title' ),
        ];

        $fields['submit'] = [
            'type' => 'submit',
        ];

        return $fields;

    }

}

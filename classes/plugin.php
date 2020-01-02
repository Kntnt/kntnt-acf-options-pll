<?php

namespace Kntnt\ACF_Options_Pll;

class Plugin extends Abstract_Plugin {

    static protected function dependencies() {
        return [
            'advanced-custom-fields-pro/acf.php' => __( 'Advanced Custom Fields Pro', 'kntnt-acf-options-pll' ),
            'polylang-pro/polylang.php' => __( 'Polylang Pro', 'kntnt-acf-options-pll' ),
        ];
    }

    public function classes_to_load() {
        return [
            'public' => [
                'init' => [
                    'Translation_Manager',
                ],
            ],
            'admin' => [
                'init' => [
                    'Translation_Manager',
                    'Settings',
                ],
            ],
        ];
    }

}

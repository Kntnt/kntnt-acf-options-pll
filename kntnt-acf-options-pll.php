<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Kntnt Polylang for Advanced Custom Field Option Pages
 * Plugin URI:        https://github.com/kntnt/kntnt-acf-options-pll
 * GitHub Plugin URI: https://github.com/kntnt/kntnt-acf-options-pll
 * Description:       Makes Advanced Custom Field option pages translatable by Polylang
 * Version:           1.1.0
 * Author:            Thomas Barregren
 * Author URI:        https://www.kntnt.com/
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       kntnt-acf-options-pll
 * Domain Path:       /languages
 */

namespace Kntnt\ACF_Options_Pll;

defined( 'WPINC' ) || die;

// To debug this plugin, set both WP_DEBUG and following constant to true.
// define( 'KNTNT_ACF_OPTIONS_PLL_DEBUG', true );

spl_autoload_register( function ( $class ) {
    $ns_len = strlen( __NAMESPACE__ );
    if ( 0 == substr_compare( $class, __NAMESPACE__, 0, $ns_len ) ) {
        require_once __DIR__ . '/classes/' . strtr( strtolower( substr( $class, $ns_len + 1 ) ), '_', '-' ) . '.php';
    }
} );

new Plugin();

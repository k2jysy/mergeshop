<?php
/**
* Plugin Name: YITH WooCommerce Zoom Magnifier
* Plugin URI: http://yithemes.com/
* Description: YITH WooCommerce Zoom Magnifier enables you to add a zoom effect to product images.
* Version: 1.1.3
* Author: Your Inspiration Themes
* Author URI: http://yithemes.com/
* Text Domain: yit
* Domain Path: /languages/
* 
* @author Your Inspiration Themes
* @package YITH WooCommerce Magnifier
* @version 1.1.2
*/
/*  Copyright 2013  Your Inspiration Themes  (email : plugins@yithemes.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

function yith_magnifier_constructor() {
    global $woocommerce;
    if ( ! isset( $woocommerce ) ) return;

    /**
     * Required functions
     */
    if( !defined('YITH_FUNCTIONS') ) {
        require_once( 'yit-common/yit-functions.php' );
    }

    load_plugin_textdomain( 'yit', false, dirname( plugin_basename( __FILE__ ) ). '/languages/' );


    add_option( 'yith_wcmg_slider_direction', apply_filters( 'yith_wcmg_slider_direction', 'left' ) );

    define( 'YITH_WCMG', true );
    define( 'YITH_WCMG_URL', plugin_dir_url( __FILE__ ) );
    define( 'YITH_WCMG_DIR', plugin_dir_path( __FILE__ ) );

    // Load required classes and functions
    require_once('functions.yith-wcmg.php');
    require_once('class.yith-wcmg-admin.php');
    require_once('class.yith-wcmg-frontend.php');
    require_once('class.yith-wcmg.php');

    // Let's start the game!
    global $yith_wcmg;
    $yith_wcmg = new YITH_WCMG();
}
add_action( 'plugins_loaded', 'yith_magnifier_constructor' );
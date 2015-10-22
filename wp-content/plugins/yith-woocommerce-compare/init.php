<?php
/**
 * Plugin Name: YITH Woocommerce Compare
 * Plugin URI: http://yithemes.com/
 * Description: YITH Woocommerce Compare allows you to compare more products with woocommerce plugin, through product attributes.
 * Version: 1.2.1
 * Author: Your Inspiration Themes
 * Author URI: http://yithemes.com/
 * Text Domain: yit
 * Domain Path: /languages/
 *
 * @author Your Inspiration Themes
 * @package YITH Woocommerce Compare
 * @version 1.1.4
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

/* Include common functions */
if( !defined('YITH_FUNCTIONS') ) {
    require_once( 'yit-common/yit-functions.php' );
}

function yith_woocompare_constructor() {
    global $woocommerce;
    if ( ! isset( $woocommerce ) ) return;

    load_plugin_textdomain( 'yit', false, dirname( plugin_basename( __FILE__ ) ). '/languages/' );

    define( 'YITH_WOOCOMPARE', true );
    define( 'YITH_WOOCOMPARE_VERSION', '1.2.1' );
    define( 'YITH_WOOCOMPARE_URL', plugin_dir_url( __FILE__ ) );
    define( 'YITH_WOOCOMPARE_DIR', plugin_dir_path( __FILE__ ) );

    // Load required classes and functions
    require_once('class.yith-woocompare-helper.php');
    require_once('functions.yith-woocompare.php');
    require_once('class.yith-woocompare-admin.php');
    require_once('class.yith-woocompare-frontend.php');
    require_once('widgets/class.yith-woocompare-widget.php');
    require_once('class.yith-woocompare.php');

    // Let's start the game!
    global $yith_woocompare;
    $yith_woocompare = new YITH_Woocompare();
}
add_action( 'plugins_loaded', 'yith_woocompare_constructor' );

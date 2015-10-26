<?php
/**
 * Plugin Name: YITH WooCommerce Brands Add-on
 * Plugin URI: http://yithemes.com/themes/plugins/yith-woocommerce-brands-add-on/
 * Description: YITH WooCommerce Brands Add-on allows you to add brands functionality to default WooCommerce plugin
 * Version: 1.0.5
 * Author: Yithemes
 * Author URI: http://yithemes.com/
 * Text Domain: yith-wcbr
 * Domain Path: /languages/
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Brands Add-on
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

if ( ! defined( 'YITH_WCBR' ) ) {
	define( 'YITH_WCBR', true );
}

if ( ! defined( 'YITH_WCBR_URL' ) ) {
	define( 'YITH_WCBR_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'YITH_WCBR_DIR' ) ) {
	define( 'YITH_WCBR_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YITH_WCBR_INC' ) ) {
	define( 'YITH_WCBR_INC', YITH_WCBR_DIR . 'includes/' );
}

if ( ! defined( 'YITH_WCBR_INIT' ) ) {
	define( 'YITH_WCBR_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_WCBR_FREE_INIT' ) ) {
	define( 'YITH_WCBR_FREE_INIT', plugin_basename( __FILE__ ) );
}

/* Plugin Framework Version Check */
! function_exists( 'yit_maybe_plugin_fw_loader' ) && require_once( YITH_WCBR_DIR . '/plugin-fw/init.php' );
yit_maybe_plugin_fw_loader( YITH_WCBR_DIR  );

if( ! function_exists( 'yith_brands_constructor' ) ) {
	function yith_brands_constructor() {
		load_plugin_textdomain( 'yith-wcbr', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		require_once( YITH_WCBR_INC . 'functions.yith-wcbr.php' );
		require_once( YITH_WCBR_INC . 'class.yith-wcbr.php' );

		// Let's start the game
		YITH_WCBR();

		if( is_admin() ){
			require_once( YITH_WCBR_INC . 'class.yith-wcbr-admin.php' );

			YITH_WCBR_Admin();
		}
	}
}
add_action( 'yith_wcbr_init', 'yith_brands_constructor' );

if( ! function_exists( 'yith_brands_install' ) ) {
	function yith_brands_install() {

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'yith_wcbr_install_woocommerce_admin_notice' );
		}
		elseif( defined( 'YITH_WCBR_PREMIUM' ) ) {
			add_action( 'admin_notices', 'yith_wcbr_install_free_admin_notice' );
			deactivate_plugins( plugin_basename( __FILE__ ) );
		}
		else {
			do_action( 'yith_wcbr_init' );
		}
	}
}
add_action( 'plugins_loaded', 'yith_brands_install', 11 );

if( ! function_exists( 'yith_wcbr_install_woocommerce_admin_notice' ) ) {
	function yith_wcbr_install_woocommerce_admin_notice() {
		?>
		<div class="error">
			<p><?php _e( 'YITH WooCommerce Brands is enabled but not effective. It requires WooCommerce in order to work.', 'yith-wcbr' ); ?></p>
		</div>
	<?php
	}
}

if( ! function_exists( 'yith_wcbr_install_free_admin_notice' ) ){
	function yith_wcbr_install_free_admin_notice() {
		?>
		<div class="error">
			<p><?php _e( 'You can\'t activate the free version of YITH WooCommerce Brands while you are using the premium one.', 'yith-wcbr' ); ?></p>
		</div>
	<?php
	}
}
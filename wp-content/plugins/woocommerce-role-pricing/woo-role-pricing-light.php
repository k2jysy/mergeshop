<?php
/**
 * woo-role-pricing-light.php
 *
 * Copyright (c) 2011,2012 Antonio Blanco http://www.blancoleon.com
 *
 * This code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This header and all notices must be kept intact.
 *
 * @author Antonio Blanco	
 * @package woorolepricinglight
 * @since woorolepricinglight 1.0.0
 *
 * Plugin Name: Woocommerce Role Pricing Light
 * Plugin URI: http://www.eggemplo.com/plugins/woocommerce-role-pricing
 * Description: Shows different prices according to the user's role
 * Version: 1.0
 * Author: eggemplo
 * Author URI: http://www.eggemplo.com
 * License: GPLv3
 */

define( 'WOO_ROLE_PRICING_LIGHT_DOMAIN', 'woorolepricing' );
define( 'WOO_ROLE_PRICING_LIGHT_PLUGIN_NAME', 'woo-role-pricing-light' );

define( 'WOO_ROLE_PRICING_LIGHT_FILE', __FILE__ );

if ( !defined( 'WOO_ROLE_PRICING_LIGHT_CORE_DIR' ) ) {
	define( 'WOO_ROLE_PRICING_LIGHT_CORE_DIR', WP_PLUGIN_DIR . '/woocommerce-role-pricing/core' );
}

define ( 'WOO_ROLE_PRICING_LIGHT_DECIMALS', apply_filters( 'woo_role_pricing_num_decimals', 2 ) );

class WooRolePricingLight_Plugin {
	
	private static $notices = array();
	
	public static function init() {
			
		load_plugin_textdomain( WOO_ROLE_PRICING_LIGHT_DOMAIN, null, WOO_ROLE_PRICING_LIGHT_PLUGIN_NAME . '/languages' );
		
		register_activation_hook( WOO_ROLE_PRICING_LIGHT_FILE, array( __CLASS__, 'activate' ) );
		register_deactivation_hook( WOO_ROLE_PRICING_LIGHT_FILE, array( __CLASS__, 'deactivate' ) );
		
		register_uninstall_hook( WOO_ROLE_PRICING_LIGHT_FILE, array( __CLASS__, 'uninstall' ) );
		
		add_action( 'init', array( __CLASS__, 'wp_init' ) );
		add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
		
		
	}
	
	public static function wp_init() {
		
		if ( is_multisite() ) {
			$active_plugins = get_site_option( 'active_sitewide_plugins', array() );
			$active_plugins = array_keys( $active_plugins );
		} else {
			$active_plugins = get_option( 'active_plugins', array() );
		}
		$woo_is_active = in_array( 'woocommerce/woocommerce.php', $active_plugins );
		
		if ( !$woo_is_active ) {
			self::$notices[] = "<div class='error'>" . __( 'The <strong>Woocommerce Role Pricing Light</strong> plugin requires the <a href="http://wordpress.org/extend/plugins/woocommerce" target="_blank">Woocommerce</a> plugin to be activated.', WOO_ROLE_PRICING_LIGHT_DOMAIN ) . "</div>";

			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			deactivate_plugins( array( __FILE__ ) );
		} else {
				
			add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ), 40 );
				
			//call register settings function
			add_action( 'admin_init', array( __CLASS__, 'register_woorolepricinglight_settings' ) );
			
			if ( !class_exists( "WooRolePricingLight" ) ) {
				include_once 'core/class-woorolepricinglight.php';
			}

		}
		
	}
	
	
	public static function register_woorolepricinglight_settings() {
		register_setting( 'woorolepricinglight', 'wrp-method' );
		add_option( 'wrp-method','rate' ); // by default rate
		
		register_setting( 'woorolepricinglight', 'wrp-baseprice' );
		add_option( 'wrp-baseprice','regular' ); // by default regular
		
	}
	
	public static function admin_notices() { 
		if ( !empty( self::$notices ) ) {
			foreach ( self::$notices as $notice ) {
				echo $notice;
			}
		}
	}
	
	/**
	 * Adds the admin section.
	 */
	public static function admin_menu() {
		$admin_page = add_submenu_page(
				'woocommerce',
				__( 'Role Pricing Light' ),
				__( 'Role Pricing Light' ),
				'manage_options',
				'woorolepricinglight',
				array( __CLASS__, 'woorolepricinglight_settings' )
		);
		
	}
	
	public static function woorolepricinglight_settings () {
	?>
	<div class="wrap">
	<h2><?php echo __( 'Woocommerce Role Pricing Light', WOO_ROLE_PRICING_LIGHT_DOMAIN ); ?></h2>
	<?php 
	$alert = "";
	
	global $wp_roles;
	
	if ( class_exists( 'WP_Roles' ) ) {
		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}
	}
		
	if ( isset( $_POST['submit'] ) ) {
		$alert = __("Saved", WOO_ROLE_PRICING_LIGHT_DOMAIN);
		
		add_option( "wrp-method",$_POST[ "wrp-method" ] );
		update_option( "wrp-method", $_POST[ "wrp-method" ] );
		
		add_option( "wrp-baseprice",$_POST[ "wrp-baseprice" ] );
		update_option( "wrp-baseprice", $_POST[ "wrp-baseprice" ] );
			
		foreach ( $wp_roles->role_objects as $role ) {
			
			if ( isset( $_POST[ "wrp-" . $role->name ] ) && ( $_POST[ "wrp-" . $role->name ] !== "" ) ) {
				add_option( "wrp-" . $role->name,$_POST[ "wrp-" . $role->name ] );
				update_option( "wrp-" . $role->name, $_POST[ "wrp-" . $role->name ] );
			} else {
				delete_option( "wrp-" . $role->name );
			}
			
		}
	}
	
	if ($alert != "")
		echo '<div style="background-color: #ffffe0;border: 1px solid #993;padding: 1em;margin-right: 1em;">' . $alert . '</div>';
	
	?>
	<div class="wrap" style="border: 1px solid #ccc; padding:10px;">
	<form method="post" action="">
	    <table class="form-table">
	        <tr valign="top">
	        <th scope="row"><strong><?php echo __( 'Products discount method:', WOO_ROLE_PRICING_LIGHT_DOMAIN ); ?></strong></th>
	        <td>
	        	<select name="wrp-method">
	        	<?php 
	        	if ( get_option("wrp-method") == "amount" ) {
	        	?>
	        		<option value="rate">Rate</option>
	        		<option value="amount" selected="selected">Amount</option>
	        	<?php 
	        	} else {
	        	?>
	        		<option value="rate" selected="selected">Rate</option>
	        		<option value="amount">Amount</option>
	        	<?php 
	        	}
	        	?>
	        	</select>
	        </tr>
	        
	        <tr valign="top">
	        <th scope="row"><strong><?php echo __( 'Apply to:', WOO_ROLE_PRICING_LIGHT_DOMAIN ); ?></strong></th>
	        <td>
	        	<select name="wrp-baseprice">
	        	<?php 
	        	if ( get_option("wrp-baseprice") == "sale" ) {
	        	?>
	        		<option value="regular">Regular price</option>
	        		<option value="sale" selected="selected">Sale price</option>
	        	<?php 
	        	} else {
	        	?>
	        		<option value="regular" selected="selected">Regular price</option>
	        		<option value="sale">Sale price</option>
	        	<?php 
	        	}
	        	?>
	        	</select>
	        </tr>
	    </table>
	    <h3><?php echo __( 'Roles:', WOO_ROLE_PRICING_LIGHT_DOMAIN ); ?></h3>
	    <div class="description">Leave empty if no role discount should be applied (default setting).<br>
	    Example with rate method: Indicate 0.1 for 10% discounts on every product.
	    </div>
		
		<table class="form-table">
	    <?php 
	    	foreach ( $wp_roles->role_objects as $role ) {
			        ?>
			        <tr valign="top">
			        <th scope="row"><?php echo ucwords($role->name) . ':'; ?></th>
			        <td>
			        	<input type="text" name="wrp-<?php echo $role->name;?>" value="<?php echo get_option( "wrp-" . $role->name ); ?>" />
			        </td>
			        </tr>
			        <?php 
			}
		?>
	    </table>
	    
	    <?php submit_button( __( "Save", WOO_ROLE_PRICING_LIGHT_DOMAIN ) ); ?>
	    
	    <?php settings_fields( 'woorolepricinglight' ); ?>
	    
	</form>
	
	</div>
	</div>
	<?php 
	}
	
	
	/**
	 * Plugin activation work.
	 * 
	 */
	public static function activate() {
	
	}
	
	/**
	 * Plugin deactivation.
	 *
	 */
	public static function deactivate() {
	
	}

	/**
	 * Plugin uninstall. Delete database table.
	 *
	 */
	public static function uninstall() {
	
	}
	
}
WooRolePricingLight_Plugin::init();


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
			self::$notices[] = "<div class='error'>" . __( 'The <strong>价格策略工具</strong> plugin requires the <a href="http://wordpress.org/extend/plugins/woocommerce" target="_blank">Woocommerce</a> plugin to be activated.', WOO_ROLE_PRICING_LIGHT_DOMAIN ) . "</div>";

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
	<h2><?php echo __( '价格策略工具', WOO_ROLE_PRICING_LIGHT_DOMAIN ); ?></h2>
	<?php 
	$alert = "";
	
	global $wp_roles;
	
	if ( class_exists( 'WP_Roles' ) ) {
		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}
	}
		
	if ( isset( $_POST['submit'] ) ) {
		$alert = __("成功保存", WOO_ROLE_PRICING_LIGHT_DOMAIN);
		
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
	        <th scope="row"><strong><?php echo __( '商品折扣方法:', WOO_ROLE_PRICING_LIGHT_DOMAIN ); ?></strong></th>
	        <td>
	        	<select name="wrp-method">
	        	<?php 
	        	if ( get_option("wrp-method") == "amount" ) {
	        	?>
	        		<option value="rate">比例</option>
	        		<option value="amount" selected="selected">金额</option>
	        	<?php 
	        	} else {
	        	?>
	        		<option value="rate" selected="selected">比例</option>
	        		<option value="amount">金额</option>
	        	<?php 
	        	}
	        	?>
	        	</select>
	        </tr>
	        
	        <tr valign="top">
	        <th scope="row"><strong><?php echo __( '应用在：', WOO_ROLE_PRICING_LIGHT_DOMAIN ); ?></strong></th>
	        <td>
	        	<select name="wrp-baseprice">
	        	<?php 
	        	if ( get_option("wrp-baseprice") == "sale" ) {
	        	?>
	        		<option value="regular">原价</option>
	        		<option value="sale" selected="selected">网售价</option>
	        	<?php 
	        	} else {
	        	?>
	        		<option value="regular" selected="selected">原价</option>
	        		<option value="sale">网售价</option>
	        	<?php 
	        	}
	        	?>
	        	</select>
	        </tr>
	    </table>
	    <h3><?php echo __( '角色:', WOO_ROLE_PRICING_LIGHT_DOMAIN ); ?></h3>
	    <div class="description">缺省设置为空，该角色没有特殊价格策略.<br>
	    比例设置为小数: 例如 0.1 即 10% 折扣（针对所有商品）.
	    </div>
		
		<table class="form-table">
	    <?php
		    
			$amount = count($wp_roles->role_objects); //取得角色数量
			$roles_title = array($amount);            //设定存储角色标题的数组变量
			$roles_name  = array($amount);            //设定存储角色名称的数组变量
			
			//调用系统对象$wp_roles->roles,把所有角色标题赋值于数组变量中
            $i=0;			
			foreach ( $wp_roles->roles as $roleTitle ) {
				$roles_title[$i] = $roleTitle['name'];
				//echo $roles_title[$i];
				$i++;				
			}
			
			//调用系统对象$wp_roles->role_objects,把所有角色名称赋值于数组变量中
            $j=0;			
			foreach ( $wp_roles->role_objects as $roleName ) {
				$roles_name[$j] = $roleName->name;
				//echo $roles_name[$j];
				$j++;				
			}
			
			for($n=0; $n < $amount ;$n++) {
  			
			        ?>
			        <tr valign="top">
			        <th scope="row">
					<?php 
					echo  translate_user_role($roles_title[$n]) . ':'; ?></th>
			        <td>
			        	<input type="text" name="wrp-<?php echo $roles_name[$n];?>" value="<?php echo get_option( "wrp-" . $roles_name[$n] ); ?>" />
			        </td>
			        </tr>
			        <?php 
			}
			
		?>
	    </table>
	    
	    <?php submit_button( __( "保存", WOO_ROLE_PRICING_LIGHT_DOMAIN ) ); ?>
	    
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


<?php
/**
 * Plugin Name: 在线聊天
 * Plugin URI: http://k2j.cc
 * Description: 客户聊天，轻松支持。
 * Version: 1.4.3
 * Author: 思远工作室
 * Author URI: http://k2j.cc
 * Requires at least: 3.4
 * Tested up to: 3.6
 *
 * Text Domain: sc_chat
 * Domain Path: /languages/
 *
 * @package SC_Chat
 * @category Core
 * @author Screets
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Define Constants
define( 'SC_CHAT_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'SC_CHAT_PLUGIN_URL', plugins_url( basename( plugin_dir_path(__FILE__) ), basename( __FILE__ ) ) );

// Prepare chat plugin
require SC_CHAT_PLUGIN_PATH . '/core/prepare.php';


if ( ! class_exists( 'SC_Chat' ) ) {

/**
 * Main Screets Chat Class
 *
 * Contains the main functions for SC Chat, stores variables, and handles error messages
 *
 * @class SC_Chat
 * @package	SC_Chat
 * @author Screets
 */
class SC_Chat {

	/**
	 * @var string
	 */
	var $version = '1.4.3';
	
	/**
	 * @var string
	 */
	var $skin_url;
	
	/**
	 * @var array
	 */
	var $default_opts = array();
	
	/**
	 * @var array
	 */
	var $opts = array();
	
	
	/**
	 * Chat Constructor
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {
		
		// Include required files
		$this->includes();
		
		// Installation
		if ( is_admin() && ! defined('DOING_AJAX') ) $this->install();
		
		// Actions
		add_action( 'init', array( &$this, 'init' ), 0 );
		add_action( 'plugins_loaded', array( &$this, 'load_plugin_textdomain' ) );
		add_action( 'admin_init', array( &$this, 'admin_init' ), 0 );
		add_action( 'after_setup_theme', array( &$this, 'compatibility' ) );
		add_action( 'wp_print_styles', 'sc_chat_custom_frontend_styles' );
		
		// Loaded action
		do_action( 'sc_chat_loaded' );

	}
	
	
	/**
	 * Init Screets Chat when WordPress Initialises
	 *
	 * @access public
	 * @return void
	 */
	function init() {
		global $sc_chat_default_opts;

		// Start session
		sc_chat_session_start();
		
		// Get default options
		$this->default_opts = $sc_chat_default_opts;
		
		// Get options
		$this->opts = sc_chat_get_options();
		
		// WPML support
		if( function_exists( 'icl_register_string' ) )
			$this->WPML();
		
		// Variables
		$this->skin_url = apply_filters( 'sc_chat_skin_url', 'skins/basic' );
		
		// Classes/actions loaded for the Frontend and for Ajax requests
		if ( ! is_admin() || defined('DOING_AJAX') ) {
			
			add_action( 'wp_enqueue_scripts', array(&$this, 'init_frontend_scripts') );
			
		}
		
		if( is_admin() || defined( 'DOING_AJAX' ) ) {
			
			// Register admin styles and scripts
			add_action('admin_print_scripts', array(&$this, 'init_backend_scripts') );
		
		}
		
		// Session control on user data
		add_action( 'wp_login', array(&$this, 'destroy_session') );
		add_action( 'wp_logout', array(&$this, 'destroy_session') );
		
		// Add operator name to user fields
		if( current_user_can( 'chat_with_users' ) ) {
			
			add_action( 'show_user_profile', array(&$this, 'xtra_profile_fields'), 10 );
			add_action( 'edit_user_profile', array(&$this, 'xtra_profile_fields'), 10 );
			add_action( 'personal_options_update', array(&$this, 'save_xtra_profile_fields') );
			add_action( 'edit_user_profile_update', array(&$this, 'save_xtra_profile_fields') );
		
		}
		
		
		// Show Conversation Box
		add_action( 'wp_footer', array(&$this, 'show_chatbox') );
		
		// Shortcodes
		add_shortcode( 'chat_online', 'sc_chat_shortcode_online' );
		add_shortcode( 'chat_offline', 'sc_chat_shortcode_offline' );
		
		// Init action
		do_action( 'sc_chat_init' );
		
	}
	
	
	/**
	 * Init Screets Chat for back-end
	 *
	 * @access public
	 * @return void
	 */
	function admin_init() {
		global $wpdb;
		
		$current_page = '';
		$current_action = '';
		$logs = '';

		// Add meta boxes
		add_action( 'add_meta_boxes', array( &$this, 'add_meta_boxes' ), 0 );
		add_action( 'save_post', 'sc_chat_save_chat_opts', 0 );
		
		/**
		 * Check logs
		 */
		if( !empty( $_REQUEST['page'] ) )
			$current_page = $_REQUEST['page'];
		
		if( $current_page == 'sc_chat_m_chat_logs' ) {
			
			// Get current action
			if( !empty( $_REQUEST['action'] ) )
				$current_action = $_REQUEST['action'];
			
			if( 'delete' === $current_action ) {
				
				// Prepare logs for deleting
				if( !empty( $_REQUEST['log'] ) )
					$logs = $_REQUEST['log'];
				else
					$logs = array( $_REQUEST['visitor_ID'] );
				
				// Delete logs one by one
				foreach( $logs as $visitor_ID ) {
				
					$deleted = $wpdb->query(
						$wpdb->prepare(
							'DELETE FROM ' . $wpdb->prefix . 'chat_logs
							WHERE visitor_ID = %d',
							$visitor_ID
						)
					);
					
				}
				
				function sc_chat_log_notice(){
					echo '<div class="updated">
					   <p>' . __( 'Chat Log has been deleted successfully', 'sc_chat' ) . '</p>
					</div>';
				}
					
				if( $deleted )
					add_action('admin_notices', 'sc_chat_log_notice');
							
			}
		}
	}
	
	/**
	 * Destroy Session
	 *
	 * @access public
	 * @return void
	 */
	function destroy_session() {
		
		// Destroy session
		$_SESSION['sc_chat'] = array(); // Clears the $_SESSION variable
		
	}
	
	/**
	 * Include required core files
	 *
	 * @access public
	 * @return void
	 */
	function includes() {
		
		// Back-end includes
		if(  is_admin() ) $this->admin_includes();
		
		// Include core files
		require SC_CHAT_PLUGIN_PATH . '/core/class.chat.php';
		require SC_CHAT_PLUGIN_PATH . '/core/class.chat_base.php';
		require SC_CHAT_PLUGIN_PATH . '/core/class.chat_user.php';
		require SC_CHAT_PLUGIN_PATH . '/core/class.chat_line.php';
		require SC_CHAT_PLUGIN_PATH . '/core/fn.init.php';
		require SC_CHAT_PLUGIN_PATH . '/core/fn.options.php';
		
	}
	
	/**
	 * Include required admin files
	 *
	 * @access public
	 * @return void
	 */
	function admin_includes() {
		
		// Include admin files
		require SC_CHAT_PLUGIN_PATH . '/core/fn.admin.php';
		require SC_CHAT_PLUGIN_PATH . '/core/class.logs.php';
		
	}
	
	/**
	 * Get Ajax URL
	 *
	 * @access public
	 * @return string
	 */
	function ajax_url() {
	
		return str_replace( array('https:', 'http:'), '', admin_url( 'admin-ajax.php' ) );
		
	}
	
	/**
	 * Return the URL with https if SSL is on
	 *
	 * @access public
	 * @param string/array $content
	 * @return string/array
	 */
	function force_ssl( $content ) {
	
		if ( is_ssl() ) {
			if ( is_array($content) )
				$content = array_map( array( &$this, 'force_ssl' ) , $content );
			else
				$content = str_replace( 'http:', 'https:', $content );
		}
		return $content;
		
	}
	
	/**
	 * Localisation
	 *
	 * @access public
	 * @return void
	 */
	function load_plugin_textdomain() {
		
		load_plugin_textdomain( 'sc_chat', false, dirname( plugin_basename( __FILE__ ) ).'/languages/' );
		
	}
	
	/**
	 * Show Chat Box
	 *
	 * @access public
	 * @return void
	 */
	function show_chatbox( $force = false ) {
		global $wpdb;
		
		// Disable in mobile devices
		if( ( wp_is_mobile() and $this->opts['disable_in_mobile'] and $force == false ) or !function_exists( 'scchatvlk' ) )
			return false;
		
		// How many operators are online?
		$this->online_op_num = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'chat_online WHERE `type` = 1 AND `status` = 1' );
		
		// How many visitors are online?
		$this->online_visitors = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'chat_online WHERE `type` = 2 AND `status` = 1' );
				
		// User is operator already?
		$this->is_user_op = ( current_user_can('chat_with_users') ) ? true : false;
		
		// Show skin file
		if(
			$force or 
			( $this->opts['hide_chat_when_offline'] == 0 or $this->online_op_num ) 
			and	( 
				$this->opts['display_chatbox'] == 1 
				or ( 
					$this->opts['display_chatbox'] == 0 
					and get_post_meta( get_the_ID(), 'sc_chat_opt_show_chatbox', true ) == 'on' 
					and !is_home() 
					and !is_front_page() 
				)
			)
			
			or ( $this->opts['always_show_homepage'] == 1 and ( $this->opts['hide_chat_when_offline'] == 0 or $this->online_op_num ) and ( is_home() or is_front_page() ) )
			
		)
			require SC_CHAT_PLUGIN_PATH . '/' . $this->skin_url . '/chatbox.php';
			
	}

	
	/**
	 * Add meta boxes
	 *
	 * @access public
	 * @return void
	 */
	function add_meta_boxes() {
		
		$screens = array( 'post', 'page' );
		
		foreach ($screens as $screen) {
			add_meta_box(
				'sc_chat_opts',
				__( 'Chat Options', 'sc_chat' ),
				'sc_chat_chat_render_opts_meta',
				$screen,
				'side'
			);
		}
		
	}
	
	
	/**
	 * Add xtra profile fields
	 *
	 * @access public
	 * @return void
	 */
	function xtra_profile_fields( $user ) { ?>
		
		<h3><?php _e( 'Chat Options', 'sc_chat' ); ?></h3>
 
		<table class="form-table">
			<tr>
				<th><label for="f_chat_op_name"><?php _e( 'Operator Name', 'sc_chat' ); ?></label></th>
				<td>
					<input type="text" name="sc_chat_op_name" id="f_chat_op_name" value="<?php echo esc_attr( get_the_author_meta( 'sc_chat_op_name', $user->ID ) ); ?>" class="regular-text" />
				</td>
			</tr>
		</table>

		
	<?php }
	
	
	/**
	 * Save xtra profile fields
	 *
	 * @access public
	 * @return void
	 */
	function save_xtra_profile_fields( $user_id ) {
		
		if ( !current_user_can( 'edit_user', $user_id ) )
			return false;
			
		// Op name isn't defined yet, create new one for user
		if( empty( $_POST['sc_chat_op_name'] ) ) {
			
			global $current_user;
			
			// Get currently logged user info
			get_currentuserinfo();
			
			$op_name = sanitize_key( $current_user->display_name );
		
		
		// Sanitize OP name
		} else
			$op_name = sanitize_key( $_POST['sc_chat_op_name'] );
		
		
		// Update user meta now
		update_user_meta( $user_id, 'sc_chat_op_name', $op_name );
		
	}
	
	
	/**
	 * Installation
	 *
	 * @access public
	 * @return void
	 */
	function install() {
		
		// Install the plugin on activation
		register_activation_hook( __FILE__, 'sc_chat_activate' ); 
		
	}
	
	/**
	 * Upgrade the plugin to new version
	 *
	 * @access public
	 * @return void
	 */
	function upgrade( $current_version ) {
        global $sc_chat_default_opts, $wpdb;
		
        /**
         * Update options to be sure if new options also added
		 *
         */
        $old_opts = get_option( 'sc_chat_opts' );
		
		if( !empty( $old_opts ) )
			update_option( 'sc_chat_opts', array_merge( $sc_chat_default_opts, $old_opts ) );
		else
			update_option( 'sc_chat_opts', $sc_chat_default_opts );
		
		
		/**
		 * Update database tables
		 *
		 */
		
		// Update tables for older than 1.2
		if( version_compare( $current_version, '1.2', '<') and !empty( $current_version )) {
			
			$wpdb->query( 'ALTER TABLE `' . $wpdb->prefix . 'chat_visitors` ADD COLUMN `ip_address` INT(10) UNSIGNED NULL AFTER `gravatar`' );
			$wpdb->query( 'ALTER TABLE `' . $wpdb->prefix . 'chat_online` ADD COLUMN `ip_address` INT UNSIGNED NULL AFTER `last_activity`' );
			$wpdb->query( 'ALTER TABLE `' . $wpdb->prefix . 'chat_online` ADD COLUMN `user_agent` VARCHAR(120) NULL DEFAULT NULL AFTER `ip_address`' );
			$wpdb->query( 'ALTER TABLE `' . $wpdb->prefix . 'chat_visitors` ADD COLUMN `user_agent` VARCHAR(120) NULL DEFAULT NULL AFTER `ip_address`' );
			$wpdb->query( 'ALTER TABLE `' . $wpdb->prefix . 'chat_lines`
			CHANGE COLUMN `chat_line` `chat_line` VARCHAR(700) NOT NULL AFTER `receiver_ID`' );
			$wpdb->query( 'ALTER TABLE `' . $wpdb->prefix . 'chat_logs`
			CHANGE COLUMN `chat_line` `chat_line` VARCHAR(700) NOT NULL AFTER `sender_email`' );
			
		}
		
    }
    
    
    /**
	 * Add Compatibility for various bits
	 *
	 * @access public
	 * @return void
	 */
	function compatibility() {
		global $sc_chat_default_opts;
		
		// First we check if our default plugin have been applied.
		$the_plugin_status = get_option( 'sc_chat_setup_status' );
        
        // Get current plugin version
		$current_version = get_option( 'sc_chat_plugin_version' );
		
		
        // If new version installed, upgrade the plugin
        if( empty( $current_version ) or version_compare( $current_version, $this->version, '<') ) {
            
            $this->upgrade( $current_version );
            
            // Save new version into DB
            update_option( 'sc_chat_plugin_version', $this->version );
            
        }
		
		// If the settings has not yet been used we want to run our default settings.
		if ( $the_plugin_status != '1' ) {
			
			// Setup default theme settings
			add_option( 'sc_chat_opts', sc_chat_get_default_options( $sc_chat_default_opts ) );
			
			// Once done, we register our setting to make sure we don't duplicate everytime we activate.
			update_option( 'sc_chat_setup_status', '1' );
			
		}
			
	}
	
	
	/**
	 * Backend styles and scripts
	 *
	 * @access public
	 * @return void
	 */
	function init_backend_scripts() {
		global $current_user;
		
		$page = '';
		
		// Get currently logged user info
		get_currentuserinfo();
		
		// Get current page
		if( !empty( $_REQUEST['page'] ) )
			$page = $_REQUEST['page'];
			
		// Load spectrum stylesheet for only options page
		if( $page == 'sc_chat_m_chat_opts' ) {
			
			wp_register_style(
				'jquery_spectrum_css', 
				SC_CHAT_PLUGIN_URL . '/assets/js/jquery.spectrum/spectrum.css'
			);
			wp_enqueue_style( 'jquery_spectrum_css' );
			
		}
		
		// Main admin stylesheet
		wp_register_style( 
			'sc_chat_admin_styles', 
			SC_CHAT_PLUGIN_URL . '/assets/css/admin-style.css'
		);
		wp_enqueue_style( 'sc_chat_admin_styles' );
		
		// Load spectrum script for only options page
		if( $page == 'sc_chat_m_chat_opts' ) {
			wp_register_script( 
				'jquery_spectrum', 
				plugins_url('assets/js/jquery.spectrum/spectrum.js', __FILE__,
				array( 'jquery' ) )
			);
			wp_enqueue_script( 'jquery_spectrum' );
			
		}
		
		// Chat application script
		if( $page == 'sc_opt_pg_a' or $page == 'sc_opt_pg'  ) {
		
			wp_register_script( 
				'sc_chat_app_script', 
				plugins_url('assets/js/App.js', __FILE__,
				array( 'jquery' ) ),
				null,
				$this->version
			);
			wp_enqueue_script( 'sc_chat_app_script' );
			
		}
		
		// Options page script
		if( $page == 'sc_chat_m_chat_opts' ) {
			wp_register_script( 
				'sc_chat_opts', 
				plugins_url('assets/js/options.js', __FILE__,
				array( 'jquery' ) )
			);
			wp_enqueue_script( 'sc_chat_opts' );
		}
	
		// Custom Data
		$ajax_vars = array(
			'ajaxurl'   			=> $this->ajax_url(),
			'plugin_url'   			=> SC_CHAT_PLUGIN_URL,
			'tr_no_one_online'		=> __( 'No one is online', 'sc_chat' ),
			'tr_1_person_online'	=> __( '1 person online', 'sc_chat' ),
			'tr_x_people_online'	=> __( '%s people online', 'sc_chat' ),
			'tr_write_a_reply'		=> __( 'Write a reply', 'sc_chat' ),
			'tr_click_to_chat'		=> __( 'Click ENTER to chat', 'sc_chat' ),
			'tr_logout'				=> __( 'Logout', 'sc_chat' ),
			'tr_online'				=> __( 'Online', 'sc_chat' ),
			'tr_offline'			=> __( 'Offline', 'sc_chat' ),
			'tr_cnv_ended'			=> __( 'This conversation has ended', 'sc_chat' ),
			'tr_sending'			=> __( 'Sending', 'sc_chat' ),
			'tr_chat_logs'			=> __( 'Chat Logs', 'sc_chat' ),
			'tr_loading'			=> __( 'Loading', 'sc_chat' ),
			'tr_connecting'			=> __( 'Connecting', 'sc_chat' ),
			'tr_general_error'		=> str_replace( '%s', 'javascript:location.reload();', __( 'Something went wrong! Please <a href="%s">refresh the page</a> and try again', 'sc_chat' ) ),
			'user_ID'				=> $current_user->user_ID,
			'username'				=> sc_chat_get_operator_name(),
			'email'					=> $current_user->user_email,
			'is_admin'   			=> true,
			'is_op'	   				=> current_user_can('chat_with_users') // Only for appearance fx
		);

		wp_localize_script( 'sc_chat_app_script', 'sc_chat', $ajax_vars );
	
	}
	
	/**
	 * WPML Support
	 *
	 * @access public
	 * @return void
	 */
	function WPML() {
		global $sc_chat_translations;
		
		foreach( $sc_chat_translations as $name => $value ) {
			
			// Register strings to WPML
			icl_register_string( 'Screets Chat', $name, $value );
			
			// Update translations in options
			$this->opts[ $name ] = icl_t( 'Screets Chat', $name, $this->opts[ $name ] );
		
		}
			
				
	}
	
	/**
	 * Frontend styles and scripts
	 *
	 * @access public
	 * @return void
	 */
	function init_frontend_scripts() {
			
		$suffix_css = ( $this->opts['compress_css'] == 1 ) ? '.min' : '';
		$suffix_js = ( $this->opts['compress_js'] == 1 ) ? '.min' : '';
		
		// Skin stylesheet
		if( $this->opts['load_skin_css'] == 1 ) {
			wp_register_style(
				'sc_chat_skin', 
				plugins_url( 'skins/basic', __FILE__ )  . '/style' . $suffix_css . '.css'
			);
			wp_enqueue_style( 'sc_chat_skin' );
		}
		
		// Use jquery on front-end
		wp_enqueue_script( 'jquery' );
		
		// Easing Plugin
		if( !$this->opts['use_css_anim'] ) {
			wp_register_script( 
				'jquery-easing', 
				plugins_url( 'assets/js/jquery.easing.min.js', __FILE__, array( 'jquery' ) ),
				null,
				'1.3',
				true
			);
			wp_enqueue_script( 'jquery_easing' );
		}
		
		// Autosize Plugin
		wp_register_script( 
			'jquery-autosize', 
			plugins_url( 'assets/js/jquery.autosize.min.js', __FILE__, array( 'jquery' ) ),
			null,
			'1.17.1',
			true
		);
		wp_enqueue_script( 'jquery-autosize' );
		
		// Cookie Plugin
		wp_register_script( 
			'jquery-cookie', 
			plugins_url( 'assets/js/cookie.min.js', __FILE__, array( 'jquery' ) ),
			null,
			'1.3.1',
			true
		);
		wp_enqueue_script( 'jquery-cookie' );
		
		// Application script
		wp_register_script( 
			'sc_chat_app_script', 
			plugins_url( 'assets/js/App' . $suffix_js . '.js', __FILE__, array( 'jquery' ) ),
			null,
			$this->version,
			true
		);
		wp_enqueue_script( 'sc_chat_app_script' );
		
		
		// Custom Data
		$ajax_vars = array(
			'ajaxurl'   			=> $this->ajax_url(),
            'plugin_url'   			=> SC_CHAT_PLUGIN_URL,
			'tr_no_one_online'		=> __( 'No one is online', 'sc_chat' ),
			'tr_logout'				=> __( 'Logout', 'sc_chat' ),
			'tr_sending'			=> __( 'Sending', 'sc_chat' ),
			'tr_in_chat_header'		=> $this->opts['in_chat_header'],
			'tr_offline_header'		=> $this->opts['offline_header'],
			'tr_connecting'			=> __( 'Connecting', 'sc_chat' ),
			'tr_general_error'		=> str_replace( '%s', 'javascript:location.reload();', __( 'Something went wrong! Please <a href="%s">refresh the page</a> and try again', 'sc_chat' ) ),
			'use_css_anim'			=> $this->opts['use_css_anim'],
			'sound_package'  		=> $this->opts['sound_package'],
			'delay'					=> $this->opts['delay'],
			'is_admin'   			=> false,
			'is_op'	   				=> current_user_can('chat_with_users'), // Only for appearance fx
		);

		wp_localize_script( 'sc_chat_app_script', 'sc_chat', $ajax_vars );
		
	}
	
	
	
}

// Init Chat class
$GLOBALS['SC_Chat'] = new SC_Chat();


} // class_exists

/**
 * Check for updates
 *
 */
 
	$sc_chat_api_url = 'http://screets.com/update/wp/';
	$sc_chat_plugin_slug = basename(dirname(__FILE__));

	// Take over the update check
	add_filter('pre_set_site_transient_update_plugins', 'sc_chat_check_for_plugin_update');

	function sc_chat_check_for_plugin_update($checked_data) {
		global $sc_chat_api_url, $sc_chat_plugin_slug, $wp_version;
		
		// Comment out these two lines during testing.
		if (empty($checked_data->checked))
			return $checked_data;
		
		$args = array(
			'slug' => $sc_chat_plugin_slug,
			'version' => @$checked_data->checked[$sc_chat_plugin_slug .'/'. $sc_chat_plugin_slug .'.php'],
		);
		$request_string = array(
				'body' => array(
					'action' => 'basic_check', 
					'request' => serialize($args),
					'api-key' => md5(get_bloginfo('url'))
				),
				'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
			);
		
		// Start checking for an update
		$raw_response = wp_remote_post($sc_chat_api_url, $request_string);
		
		if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200))
			$response = unserialize($raw_response['body']);
		
		if (is_object($response) && !empty($response)) // Feed the update data into WP updater
			$checked_data->response[$sc_chat_plugin_slug .'/'. $sc_chat_plugin_slug .'.php'] = $response;
		
		return $checked_data;
	}


	// Take over the Plugin info screen
	add_filter('plugins_api', 'sc_chat_plugin_api_call', 10, 3);

	function sc_chat_plugin_api_call($def, $action, $args) {
		global $sc_chat_plugin_slug, $sc_chat_api_url, $wp_version;
		
		if (!isset($args->slug) || ($args->slug != $sc_chat_plugin_slug))
			return false;
		
		// Get the current version
		$plugin_info = get_site_transient('update_plugins');
		$current_version = $plugin_info->checked[$sc_chat_plugin_slug .'/'. $sc_chat_plugin_slug .'.php'];
		$args->version = $current_version;
		
		$request_string = array(
				'body' => array(
					'action' => $action, 
					'request' => serialize($args),
					'api-key' => md5(get_bloginfo('url'))
				),
				'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
			);
		
		$request = wp_remote_post($sc_chat_api_url, $request_string);
		
		if (is_wp_error($request)) {
			$res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'), $request->get_error_message());
		} else {
			$res = unserialize($request['body']);
			
			if ($res === false)
				$res = new WP_Error('plugins_api_failed', __('An unknown error occurred'), $request['body']);
		}
		
		return $res;
	}
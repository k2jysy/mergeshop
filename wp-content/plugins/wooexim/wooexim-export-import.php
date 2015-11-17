<?php 
/*
Plugin Name: WOOEXIM - WooCommerce Product Export Import Plugin
Plugin URI: http://aladinsoft.com
Description: Export products with all the meta informations of your store out of WooCommerce into a CSV Spreadsheet file. And Import products from any CSV file.
Version: 1.0.0
Author: Aladin Soft
Author URI: http://aladinsoft.com
License: GPL2
*/

define ( 'WOOEXIM_PATH' , plugin_dir_url( __FILE__ ) );
define ( 'WOOEXIM_INC_APP_PATH' , plugin_dir_url( __FILE__ )."inc/" );
define ( 'WOOEXIM_INC_PATH' , "inc/" );
define ( 'WOOEXIM_EXPORT_DIR' , '' );
$url = get_admin_url().'admin.php?page=wooexim-export';
define ( 'WOOEXIM_EXPORT_ADMIN_URL' , $url );
$path = wp_upload_dir();
$wooexim_export = substr($path['path'], 0, -7) . "WOOEXIM_EXPORT";
if( ! is_dir( $wooexim_export ) )
	mkdir( $wooexim_export );
define ( 'WOOEXIM_EXPORT_PATH' , $wooexim_export );


define ( 'WOOEXIM_DOWNLOAD_PATH' ,substr($path['url'], 0, -7) . "WOOEXIM_EXPORT/" );

class WOOEXIM_Import {
	
	public function __construct() {
		add_action( 'init', array( 'WOOEXIM_Import', 'translations' ), 1 );
		add_action('admin_menu', array('WOOEXIM_Import', 'admin_menu'));
		add_action('wp_ajax_wooexim-import-ajax', array('WOOEXIM_Import', 'render_ajax_action'));
	}

	public function translations() {
		load_plugin_textdomain( 'wooexim-import', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	public function admin_menu() {
		add_menu_page( 'WOOEXIM', 'WOOEXIM', 'manage_options', 'wooexim-import', '', '', 56 );
		add_submenu_page( 'wooexim-import', 'WOOEXIM Import Product', 'Import', 'manage_options', 'wooexim-import', array('WOOEXIM_Import', 'render_admin_action'));
	}
	
	public function render_admin_action() {
		$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'upload';
		require_once(plugin_dir_path(__FILE__).'inc/wooexim-import-common.php');
		require_once(plugin_dir_path(__FILE__)."inc/wooexim-import-{$action}.php");
	}
	
	public function render_ajax_action() {
		require_once(plugin_dir_path(__FILE__)."inc/wooexim-import-ajax.php");
		die(); // this is required to return a proper result
	}
}
	
$wooexim_import = new WOOEXIM_Import();	

require_once( WOOEXIM_INC_PATH.'wooexim-export.php' );
require_once( WOOEXIM_INC_PATH.'wooexim-save-settings.php' );
require_once( WOOEXIM_INC_PATH.'wooexim-spreadsheet.php' );
require_once( WOOEXIM_EXPORT_DIR.'lib/PHPExcel.php' );

$wooexim_export  = new Woo_wooexim_export();
?>
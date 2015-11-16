<?php
/*
Plugin Name: VarkTech Pricing Deals PRO for WooCommerce
Plugin URI: http://varktech.com
Description: An e-commerce add-on for WooCommerce, supplying Pricing Deals functionality.
Version: 1.1.1.2
Author: VarkTech
Author URI: http://varktech.com
*/

   
   //initial setup only, overriden later in function vtprd_debug_options


 error_reporting(E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR); //1.0.7.7
      
      
      
      
class VTPRD_Pro_Controller{
	
	public function __construct(){    
    global $wpdb;
    add_action('init', array( &$this, 'vtprd_pro_controller_init' ));    
    define('VTPRD_PRO_VERSION',                           '1.1.1.2');       
    define('VTPRD_PRO_MINIMUM_REQUIRED_FREE_VERSION',     '1.1.1.2');  //required version of
    define('VTPRD_PRO_LAST_UPDATE_DATE',                  '2015-11-07');
	  define('VTPRD_PRO_PLUGIN_NAME2',                      'Pricing Deals Pro for WooCommerce');
    define('VTPRD_PRO_FREE_PLUGIN_NAME',                  'Pricing Deals for WooCommerce');
    define('VTPRD_PRO_DIRNAME',                           ( dirname( __FILE__ ) ));
	  define('VTPRD_PRO_URL',                                plugins_url( '', __FILE__ ) );
    define('VTPRD_PRO_BASE_NAME',                          basename(VTPRD_PRO_DIRNAME));
    define('VTPRD_PRO_REMOTE_VERSION_FILE',               'http://www.varktech.com/pro/vtprd-pro-for-woocommerce-version.txt');
    define('VTPRD_PRO_DOWNLOAD_FREE_VERSION_BY_PARENT',   'http://wordpress.org/extend/plugins/pricing-deals-for-woocommerce/');
    define('VTPRD_PRO_PLUGIN_SLUG',                        plugin_basename(__FILE__)); 
	  define('VTPRD_LIFETIME_LIMITS_PURCHASER',              $wpdb->prefix.'vtprd_lifetime_limits_purchaser'); 
    define('VTPRD_LIFETIME_LIMITS_PURCHASER_RULE',         $wpdb->prefix.'vtprd_lifetime_limits_purchaser_rule');      
    define('VTPRD_LIFETIME_LIMITS_PURCHASER_LOGID_RULE',   $wpdb->prefix.'vtprd_lifetime_limits_purchaser_logid_rule');
	}   //end constructor

	                                                             
 /* ************************************************
 **   Overhead and Init
 *************************************************** */
	public function vtprd_pro_controller_init(){
     if (is_admin()){
        add_action('after_plugin_row', array( &$this, 'vtprd_pro_check_plugin_version' ));
        add_action('admin_init', array( &$this, 'vtprd_pro_housekeeping' )); //v1.1.1 changed to run housekeeping
        add_filter( 'plugin_action_links_' . VTPRD_PRO_PLUGIN_SLUG , array( $this, 'vtprd_custom_action_links' ) );
     }
  }
     
  //v1.1.1  New Function
	public function vtprd_pro_housekeeping() {
  			
    $this->vtprd_pro_check_for_free_version();
    
    wp_register_style ('vtprd-pro-admin-style', VTPRD_PRO_URL.'/admin/css/vtprd-admin-style2.css' ); 
    wp_enqueue_style  ('vtprd-pro-admin-style');

		return;
}   

	public function vtprd_pro_activation_hook() {
  
//mwntest  
//ob_start();
	  			
    $this->vtprd_pro_create_lifetime_tables();
	  $this->vtprd_pro_check_for_free_version();

//mwntest     
//$contents = ob_get_contents();    
//echo '$contents= <pre>'.print_r($contents, true).'</pre>' ; 
//wp_die( __('<strong>Looks like</strong>', 'vtmin'), __('VT Minimum Purchase not compatible - WP', 'vtmin'), array('back_link' => true));  

		return;
}
	
	public function vtprd_pro_check_for_free_version() {	
  
    global $wp_version;
	

	//***************************
	//CHECK FOR FREE VERSION
	//***************************
    $plugin = VTPRD_PRO_PLUGIN_SLUG;
    $free_plugin_download = '<a  href="' . VTPRD_PRO_DOWNLOAD_FREE_VERSION_BY_PARENT . '"  title="Download from wordpress.org"> WordPress.org </a>';
    $plugin_name = VTPRD_PRO_PLUGIN_NAME2;
    $free_plugin_name = VTPRD_PRO_FREE_PLUGIN_NAME;
    
    if(!defined('VTPRD_VERSION')) { 
  			if( is_plugin_active($plugin) ) {
  			   deactivate_plugins( $plugin );
        }
        //v1.0.9.3 begin
        $message  =  '<strong>' .$free_plugin_name . __(' PRO version ' , 'vtprdpro') .$plugin_name . __(' Deactivated during update process. ' , 'vtprdpro') . '</strong>' ;
        
        //v1.0.9.3 end
        $message .=  '<br><br>' . __('FREE Version (' , 'vtprdpro') .$free_plugin_name  . __(') must be installed and active, **before** the Pro version can be activated. ' , 'vtprdpro') ;
        $message .=  '<br><br>' . __('The Free version can be downloaded from ' , 'vtprdpro')  . $free_plugin_download ;
        $admin_notices = '<div id="message" class="error fade" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
        add_action( 'admin_notices', create_function( '', "echo '$admin_notices';" ) );

        return;
    }                                        
  
    $new_version =      VTPRD_PRO_MINIMUM_REQUIRED_FREE_VERSION;
    $current_version =  VTPRD_VERSION;
    if( (version_compare(strval($new_version), strval($current_version), '>') == 1) ) {   //'==1' = 2nd value is lower  
        if( is_plugin_active($plugin) ) {
  			   deactivate_plugins( $plugin );
        }
        $message  =  '<strong>' . __('Please Update ' , 'vtprdpro') .$free_plugin_name. '</strong>' ;
        $message .=  '<br>' . __('It must be current, before the Pro version can be activated.  The Free version can be downloaded from ' , 'vtprdpro')  . $free_plugin_download ;
        $message .=  '<br>' . __('The Current Free program version = ', 'vtprdpro')  .VTPRD_VERSION.  __(' , while the PRO required Free version = ', 'vtprdpro') . VTPRD_PRO_MINIMUM_REQUIRED_FREE_VERSION  ;
        
        $admin_notices = '<div id="message" class="error fade" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
        add_action( 'admin_notices', create_function( '', "echo '$admin_notices';" ) );
        return;
    } 
    
  
    
    //*********************
    //v1.1.0.9 check existing installations for auto_add - only if free is ACTIVE!  Option now used in apply_rules to improve processing efficiency
    global $vtprd_rules_set;  
    $vtprd_rules_set = get_option( 'vtprd_rules_set' );
    
    $ruleset_contains_auto_add_free_product = 'no';
    $sizeof_rules_set = sizeof($vtprd_rules_set);
    for($i=0; $i < $sizeof_rules_set; $i++) { 
      if ( ($vtprd_rules_set[$i]->rule_status == 'publish') && 
           ($vtprd_rules_set[$i]->rule_contains_auto_add_free_product  == 'yes') ) {
          $i =  $sizeof_rules_set;
          $ruleset_contains_auto_add_free_product = 'yes'; 
       }
    }
    $option = (get_option('vtprd_ruleset_contains_auto_add_free_product'));
    if ($option > '') {  
      update_option( 'vtprd_ruleset_contains_auto_add_free_product',$ruleset_contains_auto_add_free_product );
    } else {
      add_option( 'vtprd_ruleset_contains_auto_add_free_product',$ruleset_contains_auto_add_free_product );
    }
    //v1.1.0.9 end 
    //*********************  
  
    return; 
       
  }
 
  /* ************************************************
  **   Admin - Uninstall Hook and cleanup
  *************************************************** */ 
  function vtprd_pro_uninstall_hook() {
      if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
      	exit ();
      }

  }
   
    //Add Custom Links to PLUGIN page action links                     ///wp-admin/edit.php?post_type=vtmam-rule&page=vtmam_setup_options_page
  public function vtprd_custom_action_links( $links ) {                 
		$plugin_links = array(
			'<a href="' . admin_url( 'edit.php?post_type=vtprd-rule&page=vtprd_setup_options_page' ) . '">' . __( 'Settings', 'vtprd' ) . '</a>',
			'<a href="http://www.varktech.com">' . __( 'Docs', 'vtprd' ) . '</a>'
		);
		return array_merge( $plugin_links, $links );
	}

function vtprd_pro_check_plugin_version( $plugin ) {
  /*
    $plugin is system supplied if 
    add_action('after_plugin_row', array( &$this, 'vtprd_pro_check_plugin_version' ));
    is used.  
    
    $plugin = 'vt-minimum-purchase-pro/vt-minimum-purchase-pro.php';
  */  
 // echo '<br>plugin name= '  .$plugin;  //mwn
  
  if( strpos( VTPRD_PRO_BASE_NAME.'/'.__FILE__,$plugin ) !== false ) {  
  
    
    $new_version = wp_remote_fopen(VTPRD_PRO_REMOTE_VERSION_FILE, 'r');
    
 //   echo '<br>External file version= ' .$new_version;  //mwn
 
    if( $new_version ) {      
      $current_version = VTPRD_PRO_VERSION;
      $installation_location = VTPRD_PRO_INSTALLATION_INSTRUCTIONS_BY_PARENT;
      if( (version_compare(strval($new_version), strval($current_version), '>') == 1) ) {   //'==1' = 2nd value is lower 
        
 //    echo '<br>new version found, current version= ' .$current_version; //mwn
    
        $update_msg = __('There is a new version of ', 'vtprd') . VTPRD_PRO_PLUGIN_NAME . __(' available.', 'vtprd') ;
        echo ' <td colspan="5" class="plugin-update" style="line-height:1.2em; font-size:11px; padding:1px;">
                <div style="color:#000; font-weight:bold; margin:4px; padding:6px 5px; background-color:#fffbe4; border-color:#dfdfdf; border-width:1px; border-style:solid; -moz-border-radius:5px; -khtml-border-radius:5px; -webkit-border-radius:5px; border-radius:5px;">'.  strip_tags( $update_msg ) .' <a href="'.$installation_location.'" target="_blank">View version ' . $new_version . ' for details</a>.</div	>
              </td>';
      } else {
        return;
      }
    }
  }
}
 
/* 

=============================
BEST PRACTICE create table:
=============================
http://pastebin.com/e31EaBsm

other example using dbdelta:
http://wp.tutsplus.com/tutorials/plugins/custom-database-tables-creating-the-table/

*/

	public function vtprd_pro_create_lifetime_tables() {
    //CREATE Tables for Lifetime Maximum tracking
    global $wpdb, $vtprd_setup_options;
	
    //LIFETIME Tables

    //$rule_purchaser_table = $wpdb->prefix.'vtprd_lifetime_rule_purchaser';
    //$rule_product_table = $wpdb->prefix.'vtprd_rule_product'; 
/*    
    if ($wpdb->get_var("SHOW TABLES LIKE `".VTPRD_LIFETIME_LIMITS_PURCHASER."` ") == VTPRD_LIFETIME_LIMITS_PURCHASER) {
      //table already defined, exit stage left
      return;
    }
 */   
    $table_name =  VTPRD_LIFETIME_LIMITS_PURCHASER;
    $is_this_purchLog = $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" );
    if ( $is_this_purchLog  == VTPRD_LIFETIME_LIMITS_PURCHASER) {
      return;
    }    
    
   /* not necessary, done before function exec...    
    if ( $vtprd_setup_options['use_lifetime_max_limits'] != 'yes' ){ 
      return;
    }
     */
  	$wpdb->hide_errors();    
  	$collate = '';
    if ( $wpdb->supports_collation() ) {
  		if( ! empty($wpdb->charset ) ) $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
  		if( ! empty($wpdb->collate ) ) $collate .= " COLLATE $wpdb->collate";
    }


    $sql = "
        CREATE TABLE  `".VTPRD_LIFETIME_LIMITS_PURCHASER."` (
              purchaser_table_id bigint NOT NULL AUTO_INCREMENT,
              purchaser_ip_address VARCHAR(50),                 
              purchaser_email VARCHAR(100),
              billto_name VARCHAR(50),
              billto_address VARCHAR(100),
              billto_city VARCHAR(100),
              billto_state VARCHAR(100),
              billto_postcode VARCHAR(100),
              billto_country VARCHAR(100),
              shipto_name VARCHAR(50),
              shipto_address VARCHAR(100),
              shipto_city VARCHAR(100),
              shipto_state VARCHAR(100),
              shipto_postcode VARCHAR(100),
              shipto_country VARCHAR(100),
          KEY id (purchaser_table_id)
        ) $collate ;      
        ";
 
     $this->vtprd_pro_create_table( $sql );

    //Rule totals by Purchaser 
    $sql = "
        CREATE TABLE  `".VTPRD_LIFETIME_LIMITS_PURCHASER_RULE."` (
              rule_table_id bigint NOT NULL AUTO_INCREMENT,
              rule_id bigint,
              purchaser_table_id bigint,              
              rule_currency_total DECIMAL(11,2),
              rule_units_total DECIMAL(11,2),
              rule_percent_total DECIMAL(11,2),     
              orig_rule_object VARCHAR(1000),
              orig_rule_maximum_type VARCHAR(10),
              orig_rule_maximum_amt DECIMAL(11,2),
          KEY id (rule_table_id, rule_id, purchaser_table_id)
        ) $collate ;      
        ";
 
     $this->vtprd_pro_create_table( $sql );
 
    //Rule totals by Purchase Log ID (purchaser as data) 
    $sql = "             
        CREATE TABLE  `".VTPRD_LIFETIME_LIMITS_PURCHASER_LOGID_RULE."`   (
              logid_table_id bigint NOT NULL AUTO_INCREMENT,             
              purchase_log_id bigint,
              rule_id bigint,
            	rule_table_id bigint,              
              purchaser_table_id bigint,
              logid_status VARCHAR(20),
              rule_currency_total DECIMAL(11,2),
              rule_units_total DECIMAL(11,2),
        KEY id (logid_table_id, purchaser_table_id, purchase_log_id)
        ) $collate;
      ";
      
     $this->vtprd_pro_create_table( $sql );

     return;
  }
 
   
  
	public function vtprd_pro_create_table( $sql ) {   
      global $wpdb;
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');	        
      dbDelta($sql);
      return; 
   } 
                            

 
} //end class



//****************************************
//V1.0.5.4 BEGIN
//FOR SOME HOSTS, WARNINGS ARE GENERATED **BEFORE** ACTIVATION... 
//----------------------------------------
//Not currently necessary for all, use as needed 
//---------------------------------------- 
/*
** define Globals 
*/
/*  
 $vtprd_setup_options;  //from FREE version
 vtprd_pro_debug_options();

  function vtprd_pro_debug_options(){   
    global $vtprd_setup_options;
    if ( ( isset( $vtprd_setup_options['debugging_mode_on'] )) &&
         ( $vtprd_setup_options['debugging_mode_on'] == 'yes' ) ) {  
      error_reporting(E_ALL);  
    }  else {
      error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED ^ E_STRICT ^ E_USER_DEPRECATED ^ E_USER_NOTICE ^ E_USER_WARNING ^ E_RECOVERABLE_ERROR );    //only allow FATAL error types
    }
  }
  */
//V1.0.5.4 END  
//****************************************     

$vtprd_pro_controller = new VTPRD_Pro_Controller;

//has to be out here, accessing the plugin instance
if (is_admin()){
  register_activation_hook(__FILE__, array( $vtprd_pro_controller, 'vtprd_pro_activation_hook'));
}

<?php

/* $Desc
 *
 * @version    $Id$
 * @package    wpbase
 * @author     Opal  Team <opalwordpressl@gmail.com >
 * @copyright  Copyright (C) 2014 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */

if(!class_exists('WPO_MegamenuEditor')){
class WPO_MegamenuEditor{

	public static function getInstance(){
		static $_instance;
		if( !$_instance ){
			$_instance = new WPO_MegamenuEditor();
		}
		return $_instance;
	}

	public function init(){
		add_action( 'admin_enqueue_scripts',array($this,'initScripts') );
		add_action( 'admin_init', array( $this,'register_megamenu_setting') );
		add_action('wp_update_nav_menu_item', array($this,'fields_nav_update'),10, 3);
		add_filter( 'wp_setup_nav_menu_item',array($this,'fields_nav_item') );

		$this->initAjaxMegamenu();
	}

	/*
	 * Saves new field to postmeta for navigation
	 */
	public function fields_nav_update($menu_id, $menu_item_db_id, $args ) {
		$configs = apply_filters( 'wpo_menu_custom_configs',array() );
		foreach ($configs as $config) {
			if ( is_array($_REQUEST['menu-item-'.$config['value']]) ) {
		        $value = $_REQUEST['menu-item-'.$config['value']][$menu_item_db_id];
		        update_post_meta( $menu_item_db_id, '_menu_item_'.$config['value'], $value );
		    }
		}
	}

	/*
	 * Adds value of new field to $item object that will be passed to     Walker_Nav_Menu_Edit_Custom
	 */
	public function fields_nav_item($menu_item) {
		$configs = apply_filters( 'wpo_menu_custom_configs',array() );
		foreach ($configs as $config) {
			$menu_item->$config['value'] = get_post_meta( $menu_item->ID, '_menu_item_'.$config['value'], true );
		}
	    return $menu_item;
	}

	public function initAjaxMegamenu(){
		add_action( 'wp_ajax_wpo_shortcode_button', array($this,'ajax_shortcode_button') );
		add_action( 'wp_ajax_wpo_shortcode_save', array($this,'ajax_shortcode_save') );
		add_action( 'wp_ajax_wpo_shortcode_delete', array($this,'ajax_shortcode_delete') );
		add_action( 'wp_ajax_wpo_list_shortcodes', array($this,'showListShortCodes') );
	}

	public function initScripts(){
		if( isset($_GET['page']) && ( $_GET['page']=='wpo_megamenu' || $_GET['page']=='wpo_megamenu_vertical' ) ){
			wp_enqueue_style('megamenu_bootstrap_css',FRAMEWORK_ADMIN_STYLE_URI.'css/bootstrap.min.css');

			wp_enqueue_script('base_bootstrap_js',FRAMEWORK_STYLE_URI.'js/bootstrap.min.js');
	 		wp_enqueue_script('jquery-ui-draggable');

	 		wp_enqueue_script('media-models');
			
			wp_enqueue_style( 'wpo_shortcode_css',get_template_directory_uri().'/framework/admin/assets/css/shortcode.css');
			if($_GET['page']=='wpo_megamenu'){
				wp_enqueue_script('wpo_megamenu_js',get_template_directory_uri().'/framework/admin/assets/js/megamenu.js',array(),false,true);
				wp_enqueue_script('wpo_upload_js',get_template_directory_uri().'/framework/admin/assets/js/upload.js',array(),false,true);
				wp_enqueue_style( 'wpo_megamenu_css',get_template_directory_uri().'/framework/admin/assets/css/megamenu.css');
			}else if($_GET['page']=='wpo_megamenu_vertical'){
				wp_enqueue_script('wpo_megamenu_js',get_template_directory_uri().'/framework/admin/assets/js/megamenu-vertical.js',array(),false,true);
				wp_enqueue_style( 'wpo_megamenu_css',get_template_directory_uri().'/framework/admin/assets/css/megamenu-vertical.css');
				wp_enqueue_script('wpo_upload_js',get_template_directory_uri().'/framework/admin/assets/js/upload.js',array(),false,true);
			}
			//if ( function_exists( 'wp_enqueue_media' ) )
			wp_enqueue_media();
			wp_enqueue_script('wpo_shortcode_js',get_template_directory_uri().'/framework/admin/assets/js/shortcode.js',array(),false,true);

//			wp_enqueue_script('WPO_googlemap_admin_js', 'http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places' ,array(),false,true);
			wp_enqueue_script('WPO_googlemap_geocomplete_js', FRAMEWORK_ADMIN_STYLE_URI.'js/jquery.geocomplete.min.js',array(),false,true);
		}
	}

	public function register_megamenu_setting() {
		if( isset($_GET['page']) && $_GET['page'] == 'wpo_megamenu'&& isset($_POST) ){
			if( isset($_GET['renderwidget']) && $_GET['renderwidget'] ){
			// 	var_dump( $_POST, 1 );die;

				if( isset($_POST['widgets']) ){

					$widgets =$_POST['widgets'];
					$widgets = explode( '|wid-', '|'.$widgets );
					if( !empty($widgets) ){
						$dwidgets = WPO_Megamenu_Widget::getInstance()->loadWidgets();
						$shortcode =   WPO_Shortcodes::getInstance();
						unset( $widgets[0] );
						$output = '';
						foreach( $widgets as $wid ){
							$o = $dwidgets->getWidgetById( $wid );

							if( $o ){
								$output .= '<div class="wpo-widget" id="wid-'.$wid.'">';
								$output .= $shortcode->getButtonByType( $o->type, $o );
								$output .= '</div>';
							}
						}

						echo $output;
					}
				}

				exit();
			}
			if( isset($_POST['params']) && !empty($_POST['params']) ) {
				$params =  $_POST['params'];
				$params = str_replace( "/","",stripslashes(trim(html_entity_decode( $params ))) );
				if( $params ){
					$a = json_decode(($params));
					$output = array();
					foreach( $a as $d ){
						$output[$d->id] = $d;
					}
					$a = serialize( $output );
					update_option( "WPO_MEGAMENU_DATA-".of_get_option('magemenu-menu',''), $a );
				}
				exit();
			}
			if( isset($_POST['reset']) && $_POST['reset'] ){
				update_option( "WPO_MEGAMENU_DATA-".of_get_option('magemenu-menu',''), null );
				exit();
			}

			if( isset($_POST['params_vertical']) && !empty($_POST['params_vertical']) ) {
				$params =  $_POST['params_vertical'];
				$params = stripslashes(trim(html_entity_decode( $params ))) ;
				if( $params ){
					$a = json_decode(($params));
					$output = array();
					foreach( $a as $d ){
						$output[$d->id] = $d;
					}
					$a = serialize( $output );
					update_option( "WPO_MEGAMENU_VERTICAL_DATA-".of_get_option('magemenu-menu-vertical',''), $a );
				}
				exit();
			}
			if( isset($_POST['reset_vertical']) && $_POST['reset_vertical'] ){
				update_option( "WPO_MEGAMENU_VERTICAL_DATA-".of_get_option('magemenu-menu-vertical',''), null );
				exit();
			}
		}
	}


	public function ajax_shortcode_delete(){
		if(isset($_POST['id']) && $_POST['id']!=''){
			$obj = WPO_Megamenu_Widget::getInstance();
			$obj->delete($_POST['id']);
			echo $_POST['id'];
		}else{
			echo false;
		}
		exit();
	}

	public function ajax_shortcode_button(){
		$obj = WPO_Shortcodes::getInstance();
		if(isset($_GET['id'])){
			$obj->getShortcode($_REQUEST['type'])->renderForm($_REQUEST['type'],$_GET['id']);
		}else{
			$obj->getShortcode($_REQUEST['type'])->renderForm($_REQUEST['type']);
		}
		exit();
	}

	public function ajax_shortcode_save(){
		$id = (int)$_POST['shortcodeid'];
		$obj = WPO_Shortcodes::getInstance();
		$type = $_POST['shortcodetype'];
		$name = $_POST['shortcode_name'];
		$inputs = serialize($_POST['wpo_input']);

		$response = array();
		if($id==0){
			$response['type']='insert';
			$response['id']= $this->insertMegaMenuTable($name,$type,$inputs);
			$response['title']=$name;
			$response['message'] = $this->l('Widgets published');
			$response['type_widget'] = $type;
		}else{
			$response['type']='update';
			$response['message'] = $this->l('Widgets updated');
			$response['title']=$name;
			$response['id']=$id;
			$this->updateMegaMenuTable($id,$name,$type,$inputs);
		}
		echo json_encode($response);
		exit();
	}

	public function updateMegaMenuTable($id,$name,$type,$shortcode){
		global $wpdb;
		$table_name = $wpdb->prefix . "megamenu_widgets";
		$wpdb->update(
			$table_name,
			array(
                'name' => $name,
				'type' => $type,
				'params' => $shortcode,
			),
			array( 'id' => $id ),
			array(
				'%s',
				'%s',
				'%s'
			),
			array( '%d' )
		);
	}

	public function insertMegaMenuTable($name,$type,$shortcode){
		global $wpdb;
		$table_name = $wpdb->prefix . "megamenu_widgets";
		$wpdb->replace(
			$table_name,
			array(
                'name' => $name,
				'type' => $type,
				'params' => $shortcode,
			),
			array(
		        '%s',
				'%s',
				'%s'
			)
		);
		return $wpdb->insert_id;
	}

	public function showListShortCodes(){
		$obj =   WPO_Shortcodes::getInstance();
		$shortcodes =$obj->getButtons();
		require( FRAMEWORK_ADMIN_TEMPLATE_PATH. 'megamenu/shortcodes.php' );
	 	exit();
	}

	static function megamenu_uploader() {
	?>
		<div class="screenshot"></div>
		<input type="hidden" class="menu_image" name="menu_image">
		<div class="menu_image_action">
			<input type="button" class="button add-image" value="Add">
			<input type="button" class="button remove-image" value="Remove">
		</div>
	<?php
	}

	/**
	 * Translate Languages Follow Actived Theme
	 */
	public function l($text){
		return __($text,TEXTDOMAIN);
	}
}
}
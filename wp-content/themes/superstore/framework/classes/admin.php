<?php
	/**
	 * $Desc
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
if(!class_exists('WPO_AdminTheme')){
	class WPO_AdminTheme{

		private $themeName;

		private $livetheme;

		public function __construct( $theme='wpbase' ){
			$this->themeName = $theme;
			$this->livetheme = WPO_LiveTheme::getInstance();
		}

		public function init(){
			add_action('admin_menu', array( $this, 'adminLoadMenu') );
			add_action( 'admin_enqueue_scripts', array( $this, 'initScripts' ) );

		 	$this->initAjaxAdmin();
		}

		public function initAjaxAdmin(){
			add_action( 'wp_ajax_wpo_post_embed', array($this,'initAjaxPostEmbed') );
		}

		public function adminLoadMenu(){
			$is_vertical_menu = apply_filters( 'WPO_Enable_Vertical_Megamenu',false );
			add_theme_page( $this->themeName.' | Megamenu', $this->l("Megamenu Editor"), 'switch_themes', 'wpo_megamenu', array($this,'megaMenuPage') );
			if($is_vertical_menu){
				add_theme_page( $this->themeName.' | Megamenu', $this->l("Megamenu Vertical"), 'switch_themes', 'wpo_megamenu_vertical', array($this,'megaMenuVerticalPage') );
			}
			add_theme_page( $this->themeName.' | Live Theme Editor ', $this->l("Live Theme Editor"), 'switch_themes', 'wpo_livethemeedit', array($this,'liveThemePage') );
		}

		/**
		 *
		 */
		public function initScripts(){
			wp_enqueue_style( 'WPO_admin_meta_css', FRAMEWORK_ADMIN_STYLE_URI.'css/meta.css');
			wp_enqueue_style( 'WPO_admin_style_css', FRAMEWORK_ADMIN_STYLE_URI.'css/admin-style.css');
			wp_enqueue_script('WPO_option_framework_js', FRAMEWORK_ADMIN_STYLE_URI.'js/admin_plugins.js');
		}

		public function initAjaxPostEmbed(){
			if ( !$_REQUEST['oembed_url'] )
				die();
			// sanitize our search string
			global $wp_embed;
			$oembed_string = sanitize_text_field( $_REQUEST['oembed_url'] );
			$oembed_url = esc_url( $oembed_string );
			$check_embed = wp_oembed_get(  $oembed_url  );
			if($check_embed==false){
				$check = false;
				$result ='not found';
			}else{
				$check = true;
				$result = $check_embed;
			}
			echo json_encode( array( 'check' => $check,'video'=>$result ) );
			die();
		}


		public function megaMenuPage(){
  			$wpos = WPO_Shortcodes::getInstance();
  			$widgets = WPO_Megamenu_Widget::getInstance()->getWidgets();
			require( FRAMEWORK_ADMIN_TEMPLATE_PATH. 'megamenu/editor.php' );
		}

		public function megaMenuVerticalPage(){
  			$wpos = WPO_Shortcodes::getInstance();
  			$widgets = WPO_Megamenu_Widget::getInstance()->getWidgets();
			require( FRAMEWORK_ADMIN_TEMPLATE_PATH. 'megamenu/editor-vertical.php' );
		}




		public function liveThemePage(){

		}

		/**
		 * Translate Languages Follow Actived Theme
		 */
		public function l( $text){
			return __( $text, $this->themeName );
		}
	}
}
?>
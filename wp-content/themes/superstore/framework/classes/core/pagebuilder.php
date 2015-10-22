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
if (class_exists('WPBakeryVisualComposerSetup') || class_exists('Vc_Manager')) {
	abstract class WPO_PageBuilder_Base{

		protected $cssAnimation;
		protected $textdomain;
		
		public function __construct(){
			add_filter( 'vc_shortcodes_css_class',array($this,'customColumnBuilder'),10,2);
			$this->textdomain = get_option( 'stylesheet' );
        	$this->textdomain = preg_replace("/\W/", "_", strtolower($this->textdomain) );
			// Add Parram Goole Map
			add_shortcode_param('googlemap',array($this,'parramGoogleMap'),FRAMEWORK_ADMIN_STYLE_URI.'js/pagebuilder/googlemap.js');
			add_shortcode_param('hidden',array($this,'parramHidden'));

			//init Script and Css
			add_action( 'admin_enqueue_scripts', array( $this, 'initScripts' ) );
			add_action( 'wp_enqueue_scripts',array($this,'initScriptsPagebuilder') ,100);

			$this->setValueCssAnimationInput();
			$this->updateCssAnimationInput();

		}

		public function initScriptsPagebuilder(){
			wp_deregister_script('wpb_composer_front_js');
			wp_enqueue_script('wpb_composer_front_js',FRAMEWORK_STYLE_URI.'js/js_composer_front.js',array(),true);
			wp_enqueue_script( 'wpb_composer_front_js' );
			
			wp_enqueue_style( 'js_composer_front' );
			wp_enqueue_style('js_composer_custom_css');
		}

		public function initScripts(){
//			wp_enqueue_script('WPO_googlemap_admin_js', 'http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places' );
			wp_enqueue_script('WPO_googlemap_geocomplete_js', FRAMEWORK_ADMIN_STYLE_URI.'js/jquery.geocomplete.min.js');
		}



		protected function deleteParam($name,$element){
			if(is_array($element)){
				foreach ($element as $value) {
					vc_remove_param($name,$value);
				}
			}else{
				vc_remove_param($name,$element);
			}
		}

		public function customColumnBuilder($class_string,$tag){
			if ($tag=='vc_row' || $tag=='vc_row_inner') {
				$class_string = str_replace('vc_row-fluid', 'row', $class_string);
				$class_string = str_replace('wpb_row ', '', $class_string);
			}
			if ($tag=='vc_column' || $tag=='vc_column_inner') {
				$class_string = preg_replace('/vc_span(\d{1,2})/', 'col-md-$1', $class_string);
                $class_string = preg_replace('/vc_col-(\w)/', 'col-$1', $class_string);
                $class_string = preg_replace('/vc_hidden-(\w)/', 'hidden-$1', $class_string);
				$class_string = str_replace(' wpb_column column_container', '', $class_string);
			}
			return $class_string;
		}

		/**
		 * Translate Languages Follow Actived Theme
		 */
		public function l( $text){
			return __( $text, $this->textdomain );
		}

		/**
		 * Parram Google map
		 */
		public function parramGoogleMap($settings, $value) {
		    $dependency = vc_generate_dependencies_attributes($settings);
   			return '
				<div id="wpo-element-map">
					<div class="map_canvas" style="height:200px;"></div>

					<div class="vc_row-fluid googlefind">
						<input id="geocomplete" type="text" class="wpo-location" placeholder="Type in an address" size="90" />
						<button class="button-primary find">'.$this->l("Find").'</button>
					</div>

					<div class="row-fluid mapdetail">
						<div class="span6">
							<div class="wpb_element_label">'.$this->l('Latitude').'</div>
							<input name="lat" class="wpo-latgmap" type="text" value="">
						</div>
						
						<div class="span6">
							<div class="wpb_element_label">'.$this->l('Longitude').'</div>
							<input name="lng" class="wpo-lnggmap" type="text" value="">
						</div>
					</div>
				</div>
   			';
		}

		/**
		 * Parram input hidden
		 */
		public function parramHidden($settings, $value) {
		    $dependency = vc_generate_dependencies_attributes($settings);
		    return '<input name="'.$settings['param_name']
					.'" class="wpb_vc_param_value wpb-textinput '
					.$settings['param_name'].' '.$settings['type'].'_field" type="text" value="'
					.$value.'" ' . $dependency . '/>';
		}



		/**
		 * override input CSS Animation
		 */
		private function updateCssAnimationInput(){
			$elements = array(
							'vc_column_text',
							'vc_single_image',
							'vc_message',
							'vc_toggle',
							'vc_single_image'
						);
			foreach ($elements as $value) {
				$param = WPBMap::getParam( $value , 'css_animation');
				$param['value']=$this->cssAnimation;
				WPBMap::mutateParam( $value , $param);
			}
		}

		private function setValueCssAnimationInput(){
			$this->cssAnimation = array(
									__("No", "js_composer") => '',
									__("Top to bottom", "js_composer") => "top-to-bottom",
									__("Bottom to top", "js_composer") => "bottom-to-top",
									__("Left to right", "js_composer") => "left-to-right",
									__("Right to left", "js_composer") => "right-to-left",
									__("Appear from center", "js_composer") => "appear",
									'bounce' => 'bounce',
									'flash' => 'flash',
									'pulse' => 'pulse',
									'rubberBand' => 'rubberBand',
									'shake' => 'shake',
									'swing' => 'swing',
									'tada' => 'tada',
									'wobble' => 'wobble',
									'bounceIn' => 'bounceIn',
									'fadeIn' => 'fadeIn',
									'fadeInDown' => 'fadeInDown',
									'fadeInDownBig' => 'fadeInDownBig',
									'fadeInLeft' => 'fadeInLeft',
									'fadeInLeftBig' => 'fadeInLeftBig',
									'fadeInRight' => 'fadeInRight',
									'fadeInRightBig' => 'fadeInRightBig',
									'fadeInUp' => 'fadeInUp',
									'fadeInUpBig' => 'fadeInUpBig',
									'flip' => 'flip',
									'flipInX' => 'flipInX',
									'flipInY' => 'flipInY',
									'lightSpeedIn' => 'lightSpeedIn',
									'rotateInrotateIn' => 'rotateIn',
									'rotateInDownLeft' => 'rotateInDownLeft',
									'rotateInDownRight' => 'rotateInDownRight',
									'rotateInUpLeft' => 'rotateInUpLeft',
									'rotateInUpRight' => 'rotateInUpRight',
									'slideInDown' => 'slideInDown',
									'slideInLeft' => 'slideInLeft',
									'slideInRight' => 'slideInRight',
									'rollIn' => 'rollIn'
								);
		}
	}
}
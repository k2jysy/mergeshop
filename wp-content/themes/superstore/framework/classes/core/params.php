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

if(!class_exists('WPO_Params')){
	class WPO_Params{

		public static function getInstance(){
			static $_instance;
			if( !$_instance ){
				$_instance = new WPO_Params();
			}
			return $_instance;
		}

		public function getParam($option){
			$output = '<div class="form-group">';
				$output.='<label for="wpo_input_'.$option['id'].'">'.$option["label"].'</label>';
				switch ($option['type']) {
					case 'text':
						$output .= '<input value="'.$option['default'].'" class="form-control" type="text" id="wpo_input_'.$option['id'].'" name="wpo_input['.$option['id'].']" >';
						break;
					case 'select':
						$output .= $this->renderSelect($option);
						break;
					case 'textarea':
						$output .='<textarea class="form-control" rows="4" id="wpo_input_'.$option['id'].'" name="wpo_input['.$option['id'].']">'.stripslashes($option['default']).'</textarea>';
						break;
					case 'radio':
						foreach($option['options'] as $key => $value){
									$output .= '
									<div class="radio">
										<label>
											<input type="radio" '.checked($option['default'],$key,false).' name="wpo_input['.$option['id'].']" id="wpo_input['.$option['id'].']_'.$key.'" value="'.$key.'">
											'.$value.'
										</label>
									</div>';
								}
						break;
					case 'sidebars':
						$sidebars = $GLOBALS['wp_registered_sidebars'];

			 			$values = array();

			 			$values['none'] = $this->l('Select A Sidebar');
			 			foreach( $sidebars as $key => $sidebar ){
							$values[$sidebar['id']] = $this->l( $sidebar["name"] );
			 			}
			 			$output.=$this->renderSelect($option,$values);
						break;
					case 'posttypes':
						$output .= $this->renderPosttypes($option);
						break;
					case 'editor':
						$output.=$this->renderEditor($option);
						break;
					case 'category_parent':
						$output.=$this->renderCategoryParent($option);
						break;
					case 'hidden_id':
						$output.=$this->renderHiddenID($option);
						break;
					case 'gmap':
						$output.=$this->renderGmap($option);
						break;
					case 'embed':
						$output.=$this->renderEmbed($option);
						break;
					default:
						$output .= '<input value="'.$option['default'].'" class="form-control" type="text" id="wpo_input_'.$option['id'].'" name="wpo_input['.$option['id'].']" >';
						break;
				}
			$output.='</div>';
			echo $output;
		}

		private function renderEmbed($option){
			$output = '<div id="wpo-embed-'.$option["id"].'" class="wpo-embed">';
			$output .= '<input value="'.$option['default'].'" class="form-control" type="text" id="wpo_input_'.$option['id'].'" name="wpo_input['.$option['id'].']" >';
			ob_start();
		?>
			<div class="wpo_embed_view">
		        <span class="spinner" style="float:none;"></span>
		        <div class="result"></div>
		    </div>
		    <script>
		    	!function($) {
		    		WPO_Admin.params_Embed('#wpo_input_<?php echo $option['id']; ?>','#wpo-embed-<?php echo $option['id']; ?>');
	    		}(window.jQuery);
		    </script>
		<?php
			$output.=ob_get_clean();
			$output.='</div>';
			return $output;
		}

		/**
		 *
		 */
		private function renderGmap($option){
			$output ='<input value="'.$option['default'].'" type="hidden" id="wpo_input_'.$option['id'].'" name="wpo_input['.$option['id'].']" >';
			$output.='
				<div id="wpo-element-map">
					<div class="map_canvas" style="height:200px;"></div>

					<div class="vc_row-fluid googlefind">
						<input id="geocomplete_'.$option["id"].'" type="text" class="wpo-location" placeholder="Type in an address" size="90" />
						<button class="button-primary find">'.$this->l("Find").'</button>
					</div>

					<div class="row-fluid mapdetail">
						<div class="form-group">
							<label for="wpo_input_class">'.$this->l('Latitude').'</label>
							<input name="lat" class="form-control wpo-latgmap" type="text" value="">
						</div>
						<div class="form-group">
							<label for="wpo_input_class">'.$this->l('Longitude').'</label>
							<input name="lng" class="form-control wpo-lnggmap" type="text" value="">
						</div>
					</div>
				</div>
			';
			ob_start();
			?>
			<script>
				!function($) {
					if($('#geocomplete_<?php echo $option['id']; ?>').length>0){
						var _lat_lng = $('#wpo_input_<?php echo $option['id']; ?>').val();
						console.log(_lat_lng);
						var loca = _lat_lng;
						_lat_lng = _lat_lng.split(',');
						var center = new google.maps.LatLng(_lat_lng[0],_lat_lng[1]);
					    $("#geocomplete_<?php echo $option['id']; ?>").geocomplete({
							map: ".map_canvas",
							types: ["establishment"],
							country: "de",
							details: ".mapdetail",
							markerOptions: {
								draggable: true
							},
							location:loca,
							mapOptions: {
								scrollwheel :true,
								zoom:15,
								center:center
							}
					    });
					    $(".googlefind button.find").click(function(){
							$("#geocomplete_<?php echo $option['id']; ?>").trigger("geocode");
						});
					    $("#geocomplete_<?php echo $option['id']; ?>").bind("geocode:dragged", function(event, latLng){
							$("input[name=lat]").val(latLng.lat());
							$("input[name=lng]").val(latLng.lng());
							$("#wpo_input_<?php echo $option['id']; ?>").val(latLng.lat()+','+latLng.lng());
					    }).bind("geocode:result",function(event, result){
					    	$("#wpo_input_<?php echo $option['id']; ?>").val(result.geometry.location.d+','+result.geometry.location.e);
					    });

					    $('.wpo-latgmap,.wpo-lnggmap').keyup(function(event) {
					    	var value = $('.wpo-latgmap').val()+','+$('.wpo-lnggmap').val();
					    	$("#wpo_input_<?php echo $option['id']; ?>").val(value);
					    });
					}
				}(window.jQuery);
			</script>
			<?php
			$output.=ob_get_clean();
			return $output;
		}

		/**
		 *
		 */
		private function renderHiddenID($option){
			$value = (esc_attr( $option['default'] )=='')?time():esc_attr($option['default']);
			$output = '<input value="'.$value.'" type="hidden" id="wpo_input_'.$option['id'].'" name="wpo_input['.$option['id'].']" >';
			return $output;
		}

		/**
		 *
		 */
		private function renderCategoryParent($option){
			$terms = get_categories(array('parent'=>0,'hide_empty'=>0));
			ob_start();
		?>
			<select class="form-control" name="wpo_input[<?php echo $option['id']; ?>]" id="wpo_input_<?php echo $option['id'] ?>">
				<?php foreach($terms as $key => $value){ ?>
					<option value="<?php echo $value->term_id; ?>" <?php selected( $option['default'],$value->term_id ) ?>><?php echo $value->name; ?></option>
				<?php } ?>
			</select>
		<?php
			$output = ob_get_clean();
			return $output;
		}

		/**
		 *
		 */
		private function renderSelect($option,$values=array()){
			ob_start();
			echo '<select class="form-control" id="wpo_input_'.$option['id'].'" name="wpo_input['.$option['id'].']">';
				if(count($values)<=0){
					foreach($option['options'] as $key => $value){
						echo '<option value="'.$key.'" '.selected( $option['default'],$key ).'>'.$value.'</option>';
					}
				}else{
					foreach($values as $key => $value){
						echo '<option value="'.$key.'" '.selected( $option['default'],$key ).'>'.$value.'</option>';
					}
				}
			echo '</select>';
			$output = ob_get_clean();
			return $output;
		}

		/**
		 *
		 */
		private function renderPosttypes($option){
			$output = '';
			$args = array(
	                    'public'   => true
	                );
	    	$post_types = get_post_types($args);
	    	foreach ($post_types as $key => $value) {
	    		$checked="";
	    		if ( $value != 'attachment' ) {
	    			if ( in_array($value, explode(",", $option['default'])) ) $checked = ' checked="checked"';
		    		$output.='
				    	<div class="checkbox">
							<label>
								<input class="form-control" value="'.$value.'" type="checkbox" name="wpo_input['.$option['id'].'][]" '.$checked.'>'.$value.'
							</label>
						</div>';
				}
	    	}
			return $output;
		}

		/**
		 *
		 */
		private function renderEditor($option){
			$output = '';
	        wp_editor( '' , 'wpo_input['.$option['id'].']', array('editor_class' => 'textarea_html','textarea_rows'=>'20', 'media_buttons' => true, 'wpautop' => false ) );
	        $output = ob_get_contents();
	        ob_end_clean();
		    return $output;
		}

		/**
		 * Translate Languages Follow Actived Theme
		 */
		protected function l($text){
			return __($text,TEXTDOMAIN);
		}

	}
}
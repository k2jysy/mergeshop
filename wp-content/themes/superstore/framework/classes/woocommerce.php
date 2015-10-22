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

if(class_exists('WooCommerce')){

	class WPO_Woocommerce{
		public static function getInstance(){
			static $_instance;
			if( !$_instance ){
				$_instance = new WPO_Woocommerce();
			}
			return $_instance;
		}

		public function __construct(){
			if(of_get_option('is-quickview',true)){
				// Ajax Quickview
				add_action( 'wp_ajax_wpo_quickview', array($this,'QuickView') );
				add_action( 'wp_ajax_nopriv_wpo_quickview', array($this,'QuickView') );
				add_action( 'wp_footer',array($this,'printFooter') );
			}

			// Ajax Display Layout
			add_action( 'wp_ajax_wpo_display_layout', array($this,'DisplayLayout') );
			add_action( 'wp_ajax_nopriv_wpo_display_layout', array($this,'DisplayLayout') );

			add_action( 'wpo_button_display',array($this,'renderButton'),20 );

			
			add_action( 'widgets_init', array($this,'override_woocommerce_widgets'), 15 );

			if( of_get_option( 'is-swap-effect',true ) ){
				remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
				add_action('woocommerce_before_shop_loop_item_title',array($this,'woocommerce_template_loop_product_thumbnail'),10);
			}

			// Change Column number
			add_filter( 'loop_shop_columns', array($this,'wc_loop_shop_columns'), 1, 10 );
			add_filter( 'loop_shop_per_page', array($this,'wc_product_per_page'), 20 );

			// Add Script
			add_action( 'wp_enqueue_scripts',array($this,'initScripts') );
		}

		public function initScripts(){
			wp_enqueue_script('WPO_quickview_js', FRAMEWORK_STYLE_URI.'js/woocommerce.js',array(),false,true);
		}

		public function wc_product_per_page($cols){
			return of_get_option('woo-number-page',12);
		}

		public function wc_loop_shop_columns(){
			return of_get_option('woo-number-columns',3);
		}

		public function woocommerce_template_loop_product_thumbnail(){
			global $post, $product, $woocommerce;
			$placeholder_width = get_option('shop_catalog_image_size');
			$placeholder_width = $placeholder_width['width'];
			
			$placeholder_height = get_option('shop_catalog_image_size');
			$placeholder_height = $placeholder_height['height'];
			
			$output='';
			$class = 'image-no-effect';
			if(has_post_thumbnail()){
				$attachment_ids = $product->get_gallery_attachment_ids();
				if($attachment_ids) {
					$class = 'image-effect';
					$output.=wp_get_attachment_image($attachment_ids[0],'shop_catalog',false,array('class'=>"attachment-shop_catalog image-hover"));
				}
				$output.=get_the_post_thumbnail( $post->ID,'shop_catalog',array('class'=>$class) );
			}else{
				$output .= '<img src="'.woocommerce_placeholder_img_src().'" alt="'.__('Placeholder' , 'gp_lang').'" class="'.$class.'" width="'.$placeholder_width.'" height="'.$placeholder_height.'" />';
			}
			echo $output;
		}


		public function renderButton(){
			global $wp_query;
			$stringquery = base64_encode(json_encode($wp_query->query_vars)) ;
            if (isset($_COOKIE['wpo_cookie_layout']) && $_COOKIE['wpo_cookie_layout']=='list') {
                $layout = 'list';
            }else{
                $layout = 'grid';
            }
		?>
			<ul class="display pull-left">
                <li><span><?php echo __('View',TEXTDOMAIN); ?></span></li>
                <li>
                	<a style="position:relative;" data-query='<?php echo $stringquery; ?>' data-type="list" <?php echo (($layout=='list')?'class="active"':''); ?> href="#">
                		<i class="fa fa-th-list"></i><span><?php echo __('List',TEXTDOMAIN); ?></span>
                	</a>
                </li>
                <li>
                	<a style="position:relative;" data-query='<?php echo $stringquery; ?>' data-type="grid" <?php echo (($layout=='grid')?'class="active"':''); ?> href="#">
            			<i class="fa fa-th"></i><span><?php echo __('Grid',TEXTDOMAIN); ?></span>
            		</a>
            	</li>
            </ul>
		<?php
		}

		public function DisplayLayout(){
			$args = json_decode(base64_decode($_POST['query']));

			$type = $_POST['type'];
			$query = new WP_Query($args);
			$this->setCookieDisplay($type);
			query_posts( $query_string );
			while ( $query->have_posts() ) : $query->the_post();
				if($type=='list'){
					wc_get_template_part( 'content', 'product-list' );
				}else{
					wc_get_template_part( 'content', 'product' );
				}
			endwhile;
			die();
		}

		private function setCookieDisplay($value){
			setcookie('wpo_cookie_layout', $value , time()+3600*24*100,'/');
		}

		public function override_woocommerce_widgets() {
			$args = array(
				'WC_Widget_Cart',
				'WC_Widget_Layered_Nav',
				'WC_Widget_Layered_Nav_Filters',
				'WC_Widget_Price_Filter',
				'WC_Widget_Product_Categories',
				'WC_Widget_Products',
				'WC_Widget_Product_Search',
				'WC_Widget_Product_Tag_Cloud',
				'WC_Widget_Recently_Viewed',
				'WC_Widget_Recent_Reviews',
				'WC_Widget_Top_Rated_Products'
			);
			foreach ($args as $c) {
				if ( class_exists( $c ) ) {
					unregister_widget( $c );
					$file = FRAMEWORK_WOOCOMMERCE_WIDGETS.str_replace('_', '-', str_replace( 'wc_' , '', strtolower($c) )).'.php';
					if(is_file($file)){
						include_once( $file );
					}
				}
			}
		}

		public function printFooter(){
		?>
			<div class="modal fade" id="wpo_modal_quickview" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close btn btn-close" data-dismiss="modal" aria-hidden="true">
								<i class="fa fa-times"></i>
							</button>
						</div>
						<div class="modal-body"><span class="spinner"></span></div>
					</div>
				</div>
			</div>
		<?php
		}

		public function QuickView(){
			$args = array(
					'post_type'=>'product',
					'product'=>$_POST['productslug']
				);
			$query = new WP_Query($args);
			if($query->have_posts()){
				while($query->have_posts()): $query->the_post(); global $product;
					if(is_file(get_template_directory().'/woocommerce/quickview.php')){
						require(get_template_directory().'/woocommerce/quickview.php');
					}
				endwhile;
			}
			die;
		}
	}
	WPO_Woocommerce::getInstance();
}
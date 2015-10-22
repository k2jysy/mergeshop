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
	class WPO_Option{

		public static function getInstance(){
			static $_instance;
			if( !$_instance ){
				$_instance = new WPO_Option();
			}
			return $_instance;
		}

		private function getSkins(){
			$path = get_template_directory().'/css/skins/*.css';
			$files = glob($path);
			$arr = array('template.css'=>'default');
			if(count($files)>0){
				foreach ($files as $key => $file) {
					$fn = str_replace( '.css', '', basename($file) );
					$arr['skins/'.$fn.'.css']=$fn;
				}
			}
			return $arr;
		}

		public function __construct(){
			add_action( 'optionsframework_after',array($this,'form_backup') );

			//Ajax Backup
			add_action( "wp_ajax_wpo_option_download_options", array( $this, "download_options" ) );
			add_action( "wp_ajax_wpo_themeoption_import", array( $this, "ajax_import_option" ) );

			// add Scripts
			add_action( 'optionsframework_custom_scripts', array($this,'init_themeoption_scripts') );

			// Add Sample
			add_action("optionsframework_custom_scripts", array($this,'import_sample_demo'), 10, 2);

		}

		public function import_sample_demo(){
			if( !get_option( TEXTDOMAIN ) ){
				$option = json_decode(file_get_contents( FRAMEWORK_XMLPATH . 'config.json'));
			    
			    if(function_exists('json_last_error')){
			    	if(!json_last_error() == JSON_ERROR_NONE)
						return false;
			    }
			    
				add_option( TEXTDOMAIN, get_object_vars($option) );
			}
		}

		public function ajax_import_option(){
			$json = json_decode(stripslashes($_POST['json']));
			
			$json = get_object_vars($json);
			//var_dump($json);
			if( !get_option( TEXTDOMAIN ) )
				add_option( TEXTDOMAIN, $json );
			else
				update_option( TEXTDOMAIN, $json );

			echo true;
			die;
		}

		public function init_themeoption_scripts(){
			wp_enqueue_script('wpo_themeoption_js',FRAMEWORK_ADMIN_STYLE_URI . 'js/themeoption.js' ,array(),false,true );
		}

		public function form_backup(){
			$secret = md5( md5( AUTH_KEY . SECURE_AUTH_KEY ) . '- theme_option'  );
			$link_download = admin_url( 'admin-ajax.php?action=wpo_option_download_options&secret=' . $secret );
		?>
			<div class="wp-core-ui wpo-themeoption-action">
				<p>
					<a href="<?php echo $link_download; ?>" class="button-primary">Backup</a>
					<input type="button" value="Import" class="button button-import">
				</p>
				<p class="upload" style="display:none;">
					<span>Input your backup file below and hit Import to restore your sites options from a backup.</span>
					<textarea id="import_option" style="width:100%;" rows="15"></textarea>
					<a href="#" class="button-primary button-upload">Upload</a>
				</p>
			</div>
		<?php
		}

		public function download_options() {
            if ( ! isset( $_GET['secret'] ) || $_GET['secret'] != md5( md5( AUTH_KEY . SECURE_AUTH_KEY ) . '- theme_option'  ) ) {
                wp_die( 'Invalid Secret for options use' );
                exit;
            }
            $themename = get_option( 'stylesheet' );
	        $backup_options = preg_replace("/\W/", "_", strtolower($themename) );

	        $backup_options = get_option(TEXTDOMAIN);
            

            $content = json_encode( $backup_options );
            
            header( 'Content-Description: File Transfer' );
            header( 'Content-type: application/txt' );
            header( 'Content-Disposition: attachment; filename="wpo_options_' . TEXTDOMAIN . '_backup_' . date( 'd-m-Y' ) . '.json"' );
            header( 'Content-Transfer-Encoding: binary' );
            header( 'Expires: 0' );
            header( 'Cache-Control: must-revalidate' );
            header( 'Pragma: public' );

            echo $content;

            exit;
        }

		/**
		 *
		 */
		public function getOption( $suboptions=array() ){
			// If using image radio buttons, define a directory path
		    $imagepath =  FRAMEWORK_ADMIN_IMAGE_URI;
		    $general = array();
		    $general[] = array(
		            'name' => __('General', TEXTDOMAIN),
		            'type' => 'heading');

		    $general[] = array(
		        'name' => __( 'Logo', TEXTDOMAIN ),
		        'desc' => '',
		        'id' => 'logo',
		        'type' => 'upload'
		    );

		    $general[] = array(
		        'name' => __('Default Theme', TEXTDOMAIN),
		        'desc' => '',
		        'id' => 'skin',
		        'type' => 'select',
		        'std' =>'template.css',
		        'options' => $this->getSkins()
		    );

		    $wp_editor_settings = array(
		        'wpautop' => true, // Default
		        'textarea_rows' => 5,
		        'media_buttons' => true
		    );

		    $general[] = array(
		        'name' => __('404 text', TEXTDOMAIN),
		        'id' => '404',
		        'type' => 'editor',
		        'std' =>'Can\'t find what you need? Take a moment and do a search below!',
		        'settings' => $wp_editor_settings );

		    $general[] = array(
		        'name' => __('Copyright', TEXTDOMAIN),
		        'id' => 'copyright',
		        'type' => 'editor',
		        'std' =>'© 2014 <a href="http://pavodemo.com/wpopal/superstore/">SuperStore Theme</a> POWERED BY <a href="http://themeforest.net/user/Opal_WP/?ref=dancof">OpalTheme</a>/ Buy it on <a href="http://themeforest.net/user/Opal_WP/portfolio?ref=dancof">ThemeForest</a>.',
		        'settings' => $wp_editor_settings );

		    /**
		    *  Page Setting
		    */
		    $blogs = array(); 
		    $blogs[] = array(
		            'name' => __('Blog Post', TEXTDOMAIN),
		            'type' => 'heading');

		    $blogs[] = array(
		        'name'  => __('Layout Type', TEXTDOMAIN),
		        'desc'  => __("Images for layout.", TEXTDOMAIN),
		        'id'    => "single-layout",
		        'std'   => "0-1-1",
		        'type'  => "images",
		        'options' => array(
		            '0-1-0'    => $imagepath . '1col.png',
		            '1-1-0'  => $imagepath . '2cl.png',
		            '0-1-1'  => $imagepath . '2cr.png',
		            '1-1-1'    => $imagepath . '3c.png',
		            '1-1-m'    => $imagepath . '3c-l-l.png',
		            'm-1-1'    => $imagepath . '3c-r-r.png'
		        )
		    );

		    $blogs[] = array(
		        'name' => __('Right Sidebar', TEXTDOMAIN),
		        'desc' => '',
		        'id' => 'right-sidebar',
		        'type' => 'select',
		        'options' => $this->getSidebar()
		    );

		    $blogs[] = array(
		        'name' => __('Left Sidebar', TEXTDOMAIN),
		        'desc' => '',
		        'id' => 'left-sidebar',
		        'type' => 'select',
		        'options' => $this->getSidebar()
		    );

		    $blogs[] = array(
		        'name' => __('Show Title', TEXTDOMAIN),
		        'desc' => '',
		        'id' => 'post-title',
		        'type' => 'select',
		        'std' =>'0',
		        'options' => array(
		                        '0'=>'No',
		                        '1'   =>'Yes'
		    ));
		   
		    /**
		    *  Social Setting
		    */
		    $social = array();
		    $social[] = array(
		            'name' => __('Social Setting ', TEXTDOMAIN),
		            'type' => 'heading');

		    $social[] = array(
		        'name' => __('Facebook', TEXTDOMAIN),
		        'desc' => __('Check the box to show the facebook sharing icon in blog posts.', TEXTDOMAIN),
		        'id' => 'sharing-facebook',
		        'std' => '1',
		        'type' => 'checkbox');

		    $social[] = array(
		        'name' => __('Twitter', TEXTDOMAIN),
		        'desc' => __('Check the box to show the twitter sharing icon in blog posts.', TEXTDOMAIN),
		        'id' => 'sharing-twitter',
		        'std' => '1',
		        'type' => 'checkbox');

		    $social[] = array(
		        'name' => __('LinkedIn', TEXTDOMAIN),
		        'desc' => __('Check the box to show the linkedin sharing icon in blog posts.', TEXTDOMAIN),
		        'id' => 'sharing-linkedin',
		        'std' => '1',
		        'type' => 'checkbox');

		    $social[] = array(
		        'name' => __('Google Plus', TEXTDOMAIN),
		        'desc' => __('Check the box to show the g+ sharing icon in blog posts.', TEXTDOMAIN),
		        'id' => 'sharing-google',
		        'std' => '1',
		        'type' => 'checkbox');

		    $social[] = array(
		        'name' => __('Tumblr', TEXTDOMAIN),
		        'desc' => __('Check the box to show the tumblr sharing icon in blog posts.', TEXTDOMAIN),
		        'id' => 'sharing-tumblr',
		        'std' => '1',
		        'type' => 'checkbox');

		    $social[] = array(
		        'name' => __('Pinterest', TEXTDOMAIN),
		        'desc' => __('Check the box to show the pinterest sharing icon in blog posts.', TEXTDOMAIN),
		        'id' => 'sharing-pinterest',
		        'std' => '1',
		        'type' => 'checkbox');

		    $social[] = array(
		        'name' => __('Email', TEXTDOMAIN),
		        'desc' => __('Check the box to show the email sharing icon in blog posts.', TEXTDOMAIN),
		        'id' => 'sharing-email',
		        'std' => '1',
		        'type' => 'checkbox');
		   /**
		    *  SEO OPTION
		    */
		    $seo = array();
		    $seo[] = array(
		            'name' => __('SEO Option', TEXTDOMAIN),
		            'type' => 'heading');

		    $seo[] = array(
		        'name' => __('Enable SEO', TEXTDOMAIN),
		        'desc' => __('Check the box to enable the SEO options.', TEXTDOMAIN),
		        'id' => 'is-seo',
		        'std' => '1',
		        'type' => 'checkbox');

		    $seo[] = array(
		        'name' => __('SEO Keywords', TEXTDOMAIN),
		        'desc' => __('Paste your SEO Keywords. This will be added into the meta tag keywords in header.', TEXTDOMAIN),
		        'id' => 'seo-keywords',
		        'std' => '',
		        'type' => 'textarea');

		    $seo[] = array(
		        'name' => __('SEO Description', TEXTDOMAIN),
		        'desc' => __('Paste your SEO Description. This will be added into the meta tag description in header.', TEXTDOMAIN),
		        'id' => 'seo-description',
		        'std' => '',
		        'type' => 'textarea');

		    $seo[] = array(
		        'name' => __('Google Analytics Account ID', TEXTDOMAIN),
		        'desc' => __('Type your Google Analytics Account ID here. This will be added into the footer template of your theme.', TEXTDOMAIN),
		        'id' => 'google-analytics',
		        'std' => '',
		        'type' => 'text');
		   /**
		    *  Custom Code
		    */
		    $customize = array();
		    $customize[] = array(
		            'name' => __('Customization', TEXTDOMAIN),
		            'type' => 'heading');

		    $customize[] = array(
		        'name' => __('Live Tools Customizing Theme', TEXTDOMAIN),
		        'desc' => __('<a href="'.admin_url( 'themes.php?page=wpo_livethemeedit' ).'" class="button">Live Customizing Theme</a>',TEXTDOMAIN),
		        'id' => 'customize-theme',
		        'type' => 'select',
		        'std' => '',
		        'options' => $this->getCustomzimeCss()
		    );

		    $customize[] = array(
		        'name' => __('Before CSS </body>', TEXTDOMAIN),
		        'desc' => __('Before CSS </body> description.', TEXTDOMAIN),
		        'id' => 'snippet-close-body',
		        'std' => '',
		        'type' => 'textarea');

		    $customize[] = array(
		        'name' => __('Before JS </body>', TEXTDOMAIN),
		        'desc' => __('Before JS </body> description.', TEXTDOMAIN),
		        'id' => 'snippet-js-body',
		        'std' => '',
		        'type' => 'textarea');

		   /**
		    *  Megamenu
		    */
		    $megamenu[] = array(
		            'name' => __('Megamenu', TEXTDOMAIN),
		            'type' => 'heading');

		    $menus = wp_get_nav_menus( array( 'orderby' => 'name' ) );
	        $option_menu = array(''=>'---Select Menu---');
	        foreach ($menus as $menu) {
	        	$option_menu[$menu->term_id]=$menu->name;
	        }

	        $megamenu[] = array(
		        'name' => __('menu', TEXTDOMAIN),
		        'desc' => __('Select a menu to configure Megamenu for the menu items in the selected menu. <a href="'.admin_url( 'themes.php?page=wpo_megamenu' ).'" class="button">Megamenu Editor</a>',TEXTDOMAIN),
		        'id' => 'magemenu-menu',
		        'type' => 'select',
		        'std' =>'',
		        'options' => $option_menu
		    );

		    $megamenu[] = array(
		        'name' => __('Animation', TEXTDOMAIN),
		        'desc' => __('Select animation for Megamenu.',TEXTDOMAIN),
		        'id' => 'magemenu-animation',
		        'type' => 'select',
		        'std' =>'',
		        'options' => array(
		                        ''=>'None',
		                        'slide'   =>'Slide',
		                        'zoom' =>'Zoom',
		                        'elastic'=>'Elastic',
		                        'fading'=>'Fading'
		    ));

		    $megamenu[] = array(
		        'name' => __('Duration', TEXTDOMAIN),
		        'desc' => __('Animation effect duration for dropdown of Megamenu (in miliseconds)', TEXTDOMAIN),
		        'id' => 'megamenu-duration',
		        'std' => '400',
		        'type' => 'text');

		    $megamenu[] = array(
		        'name' => __('Animation', TEXTDOMAIN),
		        'desc' => __('Sidebar transition effect for Off-canvas menu',TEXTDOMAIN),
		        'id' => 'magemenu-offcanvas-effect',
		        'type' => 'select',
		        'std' =>'off-canvas-effect-1',
		        'options' => array(
		                        'off-canvas-effect-1'=>'Slide in on top',
		                        'off-canvas-effect-2'=>'Reveal',
		                        'off-canvas-effect-3'=>'Push',
		                        'off-canvas-effect-4'=>'Slide along',
		                        'off-canvas-effect-5'=>'Reverse slide out',
		                        'off-canvas-effect-6'=>'Rotate pusher',
		                        'off-canvas-effect-7'=>'3D rotate in',
		                        'off-canvas-effect-8'=>'3D rotate out',
		                        'off-canvas-effect-9'=>'Scale down pusher',
		                        'off-canvas-effect-10'=>'Scale up',
		                        'off-canvas-effect-11'=>'Scale & Rotate pusher',
		                        'off-canvas-effect-12'=>'Open door',
		                        'off-canvas-effect-13'=>'Fall down',
		                        'off-canvas-effect-14'=>'Delayed 3D rotate'
		    ));

			$megamenu[] = array(
		        'name' => __('Menu Vertical', TEXTDOMAIN),
		        'desc' => __('Select a menu to configure Megamenu Vertical for the menu items in the selected menu. <a href="'.admin_url( 'themes.php?page=wpo_megamenu_vertical' ).'" class="button">Megamenu Editor</a>',TEXTDOMAIN),
		        'id' => 'magemenu-menu-vertical',
		        'type' => 'select',
		        'std' =>'',
		        'options' => $option_menu
		    );

		    

		   /**
		    *  Woocommerce
		    */
		   if(class_exists('WooCommerce')){
		   		$woocommerce = array();

		   		$woocommerce[] = array(
		            'name' => __('Woocommerce', TEXTDOMAIN),
		            'type' => 'heading');

		   		$woocommerce[] = array(
			    	'name' => __('Total number of products per page', TEXTDOMAIN),
			        'desc' => 'To Change number of products displayed per page',
			        'id' => 'woo-number-page',
			        'type' => 'text',
			        'std' =>'12'
			    );

		   		$woocommerce[] = array(
			        'name' => __('Show number of related products on single product page', TEXTDOMAIN),
			        'desc' => 'To change the number of related products',
			        'id' => 'woo-number-product',
			        'type' => 'text',
			        'std' =>'4'
			    );

		   		$woocommerce[] = array(
			        'name' => __('Number of Product per row', TEXTDOMAIN),
			        'desc' => 'To change the number related products,archive products per row',
			        'id' => 'woo-number-columns',
			        'type' => 'select',
			        'std' =>'4',
			        'options' => array(
			                        '2'=>'2',
			                        '3'=>'3',
			                        '4'=>'4',
			                        '6'=>'6'
			    ));

			    $woocommerce[] = array(
			        'name' => __('Enable Quick View', TEXTDOMAIN),
			        'desc' => __('Check the box to enable Quick View button.', TEXTDOMAIN),
			        'id' => 'is-quickview',
			        'std' => '1',
			        'type' => 'checkbox');

			    $woocommerce[] = array(
			        'name' => __('Enable Effect Image', TEXTDOMAIN),
			        'desc' => __('Check the box to enable swap effect image product.', TEXTDOMAIN),
			        'id' => 'is-swap-effect',
			        'std' => '1',
			        'type' => 'checkbox');

			    $woocommerce[] = array(
			        'name'  => __('Layout Archive Product', TEXTDOMAIN),
			        'desc'  => __("Display your sidebar like image on Archive pages.", TEXTDOMAIN),
			        'id'    => "woocommerce-archive-layout",
			        'std'   => "0-1-1",
			        'type'  => "images",
			        'options' => array(
			            '0-1-0'     => 	$imagepath . '1col.png',
			            '1-1-0'  	=> 	$imagepath . '2cl.png',
			            '0-1-1'  	=> 	$imagepath . '2cr.png',
			            '1-1-1'    	=> 	$imagepath . '3c.png'
			        )
			    );
			    $woocommerce[] = array(
			        'name' => __('Right Sidebar', TEXTDOMAIN),
			        'desc' => '',
			        'id' => 'woocommerce-archive-right-sidebar',
			        'type' => 'select',
			        'options' => $this->getSidebar()
			    );

			    $woocommerce[] = array(
			        'name' => __('Left Sidebar', TEXTDOMAIN),
			        'desc' => '',
			        'id' => 'woocommerce-archive-left-sidebar',
			        'type' => 'select',
			        'options' => $this->getSidebar()
			    );

			    $woocommerce[] = array(
			        'name'  => __('Layout Single Product', TEXTDOMAIN),
			        'desc'  => __("Display your sidebar like image on Single page.", TEXTDOMAIN),
			        'id'    => "woocommerce-single-layout",
			        'std'   => "0-1-1",
			        'type'  => "images",
			        'options' => array(
			            '0-1-0'    => $imagepath . '1col.png',
			            '1-1-0'  => $imagepath . '2cl.png',
			            '0-1-1'  => $imagepath . '2cr.png',
			            '1-1-1'    => $imagepath . '3c.png'
			        )
			    );
			    $woocommerce[] = array(
			        'name' => __('Right Sidebar', TEXTDOMAIN),
			        'desc' => '',
			        'id' => 'woocommerce-single-right-sidebar',
			        'type' => 'select',
			        'options' => $this->getSidebar()
			    );

			    $woocommerce[] = array(
			        'name' => __('Left Sidebar', TEXTDOMAIN),
			        'desc' => '',
			        'id' => 'woocommerce-single-left-sidebar',
			        'type' => 'select',
			        'options' => $this->getSidebar()
			    );

			    $woocommerce[] = array(
					'name'    => __('Show notification when add to cart success', TEXTDOMAIN),
					'desc'    => __('Show notification when add to cart success', TEXTDOMAIN),
					'id'      => 'woo-show-noty',
					'std' 		=> '1',
					'type' => 'checkbox'
			    );

			    $woocommerce[] = array(
					'name'    => __('Text add to cart success: ', TEXTDOMAIN),
					'desc'    => '',
					'id'      => 'woo-show-text',
					'std' 		=> 'Success: Your item has been added to cart!',
					'type' => 'text'
					
			    );
		   }
		   // /**
		   //  *  Data Sample
		   //  */
		   //  $sample = array();
		   //  $sample[] = array(
		   //          'name' => __('Data Sample', TEXTDOMAIN),
		   //          'type' => 'heading');


		    // merge all list of group options here and combine options from subthemes
		    $goptions = array( 'general',  'blogs' , 'seo', 'social', 'customize','megamenu' );
		    if(class_exists('WooCommerce')){
		    	$goptions[]='woocommerce';
		    }
		    $options = array();
		    foreach( $goptions as $gopt  ){
		   		$options = array_merge_recursive( $options, $$gopt );
		   		if( isset($suboptions[$gopt]) ){
		   			$options = array_merge_recursive( $options, $suboptions[$gopt] );
		   		}
		    }
		    return $options;
		}

		private function getSidebar(){
			// Sidebar
		    global $wp_registered_sidebars;
		    $sidebar = array();
		    foreach($wp_registered_sidebars as $key=>$value){
		        $sidebar[$value['id']] = $value['name'];
		    }
		    return $sidebar;
		}

		private function getCustomzimeCss(){
			 // Get Option Livetheme Customize
		    $customize = array(''=>'Select A Custom Theme');
		    $directories = glob( FRAMEWORK_CUSTOMZIME_STYLE.'*.css');
		    foreach( $directories as $dir ){
		        $file = str_replace( ".css","", basename( $dir ));
		        $customize[$file] = $file;
		    }
		    return $customize;
		}
	}
?>
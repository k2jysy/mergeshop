<?php
class WPO_SubTheme extends WPO_Framework {

	public function __construct(){
		parent::__construct();
		// Add default Sidebar
		$this->setSidebarDefault();

		// Require Plugin
		$this->initRequirePlugin();
		
 		/* This theme uses post thumbnails */
		$this->addThemeSupport( 'post-thumbnails' );
		// Add default posts and comments RSS feed links to head*/
		$this->addThemeSupport( 'automatic-feed-links' );
		
		$this->addImagesSize('blog-thumbnails',377,172,true);
		$this->addImagesSize('brand-logo',160,80,true);
		$this->addImagesSize('category-image',873,294,true);
		$this->addImagesSize('image-blog',1110,468,true);

		// Register Post type support
		$this->addPostTypeSuport( array( 'brands','testimonials') );

		/**
		* Register  Metabox
		*/
		$this->initMetaBox();

		/**
		* Register  list of widgets supported inside framework
		*/
		$this->addWidgetsSuport( array( 'recent_post','tabs','menu_vertical') );

		// Wishlist
		add_filter( 'yith_wcwl_button_label',array($this,'iconWishlist') );
		add_filter( 'yith-wcwl-browse-wishlist-label',array($this,'iconWishlistAdd') );


		add_filter('add_to_cart_fragments', array($this,'woocommerce_header_add_to_cart_fragment'));

		add_filter( 'woocommerce_breadcrumb_defaults', array($this,'wpo_woocommerce_breadcrumbs') );
		add_filter( 'woocommerce_breadcrumb_defaults', array($this,'wpo_change_breadcrumb_delimiter') );

		add_filter('WPO_Enable_Vertical_Megamenu',array($this,'enable_vertical_menu'));
		
		add_action( 'wp_enqueue_scripts' , array($this,'fix_VC_frontend_editor'),999 );
	}

	public function fix_VC_frontend_editor(){
		wp_enqueue_script( 'vc_inline_iframe1_js', get_template_directory_uri() . '/js/vc_page_editable_custom.js' , array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-draggable','vc_inline_iframe_js' ), WPB_VC_VERSION, true );
	}


	public function enable_vertical_menu(){
		return true;
	}

	/*Customise the WooCommerce breadcrumb*/
	public function wpo_woocommerce_breadcrumbs() {
		return array(
			'delimiter' => ' &#47; ',
			'wrap_before' => '<nav class="breadcrumb" itemprop="breadcrumb">',
			'wrap_after' => '</nav>',
			'before' => '',
			'after' => '',
			'home' => _x( '<span class="fa fa-home"></span> Home', 'breadcrumb', 'woocommerce' )
			);
	}

	public function wpo_change_breadcrumb_delimiter( $defaults ) {
		// Change the breadcrumb delimeter from '/' to '>'
		$defaults['delimiter'] = '<span> &#47</span> ';
		return $defaults;
	}


	// Display 12 products per page. Goes in functions.php
	//add_filter( 'loop_shop_per_page', create_function( '$cols', 'return 12;' ), 20 );

    public function woocommerce_header_add_to_cart_fragment( $fragments ) {
        global $woocommerce;
        ob_start();
        wpo_cartdropdown();
        $fragments['.cart-header'] = ob_get_clean();
        return $fragments;
    }

	/**
	 * Init Meta Box fields
	 */
	private function initMetaBox(){
		$path = get_template_directory() . '/sub/customfield/';
		if(function_exists('of_get_option')){
			if(of_get_option('is-seo',true)){
				new WPO_MetaBox(array(
				    'id' => 'wpo_seo',
				    'title' => $this->l('SEO Fields'),
				    'types' => array('page','portfolio','post','video'),
				    'priority' => 'high',
				    'template' => $path . 'seo.php',
				));
			}
		}

		new WPO_MetaBox(array(
		    'id' => 'wpo_template',
		    'title' => $this->l('Advanced Configuration'),
		    'types' => array('page'),
		    'priority' => 'high',
		    'template' => $path . 'page-advanced.php'
		));

		new WPO_MetaBox(array(
		    'id' => 'wpo_pageconfig',
		    'title' => $this->l('Page Configuration'),
		    'types' => array('page'),
		    'priority' => 'high',
		    'template' => $path . 'page.php',
		));

		new WPO_MetaBox(array(
		    'id' => 'wpo_post',
		    'title' => $this->l('Embed Options'),
		    'types' => array('post','video'),
		    'priority' => 'high',
		    'template' => $path . 'post.php',
		));
	}

	/**
	 *
	 */


	public function iconWishlist($value){
		return '<i class="fa fa-heart"></i><span>添加预定</span>';
	}

	public function iconWishlistAdd(){
		return '<i class="fa fa-check"></i><span>添加预定</span>';
	}

	private function initRequirePlugin(){
		// Add default Required Plugin
		$this->addRequiredPlugin(array(
			'name'                     => 'Options Framework', // The plugin name
		    'slug'                     => 'options-framework', // The plugin slug (typically the folder name)
		    'required'                 => true, // If false, the plugin is only 'recommended' instead of required
		));

		$this->addRequiredPlugin(array(
			'name'                     => 'WooCommerce', // The plugin name
		    'slug'                     => 'woocommerce', // The plugin slug (typically the folder name)
		    'required'                 => true, // If false, the plugin is only 'recommended' instead of required
		));

		$this->addRequiredPlugin(array(
			'name'                     => 'Contact Form 7', // The plugin name
		    'slug'                     => 'contact-form-7', // The plugin slug (typically the folder name)
		    'required'                 => true, // If false, the plugin is only 'recommended' instead of required
		    'source'				   => get_stylesheet_directory_uri() . '/sub/plugins/contact-form-7.zip', // The plugin source
		));

		$this->addRequiredPlugin(array(
			'name'                     => 'WPBakery Visual Composer', // The plugin name
		    'slug'                     => 'js_composer', // The plugin slug (typically the folder name)
		    'required'                 => true,
		    'source'                   => get_stylesheet_directory_uri() . '/sub/plugins/js_composer.zip', // The plugin source
		));

		$this->addRequiredPlugin(array(
			'name'                     => 'Revolution Slider', // The plugin name
            'slug'                     => 'revslider', // The plugin slug (typically the folder name)
            'required'                 => true, // If false, the plugin is only 'recommended' instead of required
            'source'                   => get_stylesheet_directory_uri() . '/sub/plugins/revslider.zip', // The plugin source
		));

		$this->addRequiredPlugin(array(
			'name'                     => 'YITH WooCommerce Wishlist', // The plugin name
            'slug'                     => 'yith-woocommerce-wishlist', // The plugin slug (typically the folder name)
            'required'                 => true
		));

		$this->addRequiredPlugin(array(
			'name'                     => 'YITH Woocommerce Compare', // The plugin name
            'slug'                     => 'yith-woocommerce-compare', // The plugin slug (typically the folder name)
            'required'                 => true
		));

		$this->addRequiredPlugin(array(
			'name'                     => 'YITH WooCommerce Zoom Magnifier', // The plugin name
		    'slug'                     => 'yith-woocommerce-zoom-magnifier', // The plugin slug (typically the folder name)
		    'required'                 =>  true
		));

		$this->addRequiredPlugin(array(
			'name'                     => 'MailChimp', // The plugin name
		    'slug'                     => 'mailchimp-for-wp', // The plugin slug (typically the folder name)
		    'required'                 =>  true
		));
	}

	//override
	public function configLayout($layout,$config=array()){
		switch ($layout) {
			// Two Sidebar
			case '1-1-1':
				$config['left-sidebar']['show'] 	= true;
				$config['left-sidebar']['class'] 	='col-md-3 col-md-pull-6';
				$config['right-sidebar']['class']	='col-md-3';
				$config['right-sidebar']['show'] 	= true;
				$config['main']['class'] 			='col-xs-12 col-md-6 col-md-push-3';
				break;
			//One Sidebar Right
			case '0-1-1':
				$config['left-sidebar']['show'] 	= false;
				$config['right-sidebar']['show'] 	= true;
				$config['main']['class']  			='col-xs-12 col-md-9 no-sidebar-left';
				$config['right-sidebar']['class'] 	='col-xs-12 col-md-3';
				break;
			// One Sidebar Left
			case '1-1-0':
				$config['left-sidebar']['show'] 	= true;
				$config['right-sidebar']['show'] 	= false;
				$config['left-sidebar']['class'] 	='col-md-3 col-sm-12 col-md-pull-9 col-sm-pull-0 ';
				$config['main']['class'] 			='col-md-9 col-sm-12 col-md-push-3 col-sm-push-0 no-sidebar-right clearfix';
				break;

			case 'm-1-1':
				$config['left-sidebar']['show'] 	= true;
				$config['left-sidebar']['class'] 	='col-md-3 sidebar-main';
				$config['right-sidebar']['class']	='col-md-3';
				$config['right-sidebar']['show'] 	= true;
				$config['main']['class'] 			='col-xs-12 col-md-6';
				break;

			case '1-1-m':
				$config['left-sidebar']['show'] 		= true;
				$config['right-sidebar']['show'] 	= true;
				$config['left-sidebar']['class'] 	='col-md-3 col-md-pull-6';
				$config['right-sidebar']['class']	='col-md-3 col-md-pull-6';
				$config['main']['class'] 			='col-xs-12 col-md-6 col-md-push-6';
				break;
			// Fullwidth
			default:
				$config['left-sidebar']['show'] 	= false;
				$config['right-sidebar']['show'] 	= false;
				$config['main']['class'] 			='col-xs-12 no-sidebar';
				break;
		}
		return $config;
	}

   /**
	*
	*/
	private function setSidebarDefault(){
		$this->addSidebar('sidebar-left',
			array(
				'name'          => $this->l( 'Left Sidebar' ),
				'id'            => 'sidebar-left',
				'description'   => $this->l( 'Appears on posts and pages in the sidebar.'),
				'before_widget' => '<aside id="%1$s" class="widget box clearfix %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<div class="widget-title box-heading"><span>',
				'after_title'   => '</span></div>',
			));
		$this->addSidebar('sidebar-right',
			array(
				'name'          => $this->l( 'Right Sidebar' ),
				'id'            => 'sidebar-right',
				'description'   => $this->l( 'Appears on posts and pages in the sidebar.'),
				'before_widget' => '<aside id="%1$s" class="widget box clearfix %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<div class="widget-title box-heading"><span>',
				'after_title'   => '</span></div>',
			));

			$this->addSidebar('blog-sidebar-left',
			array(
				'name'          => $this->l( 'Blog Left Sidebar' ),
				'id'            => 'blog-sidebar-left',
				'description'   => $this->l( 'Appears on posts and pages in the sidebar.'),
				'before_widget' => '<aside id="%1$s" class="widget box clearfix %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<div class="widget-title box-heading"><span>',
				'after_title'   => '</span></div>',
			));

			$this->addSidebar('blog-sidebar-right',
			array(
				'name'          => $this->l( 'Blog Right Sidebar' ),
				'id'            => 'blog-sidebar-right',
				'description'   => $this->l( 'Appears on posts and pages in the sidebar.'),
				'before_widget' => '<aside id="%1$s" class="widget box clearfix %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<div class="widget-title box-heading"><span>',
				'after_title'   => '</span></div>',
			));

		$this->addSidebar('footer-1',
			array(
				'name'          => $this->l( 'Footer 1' ),
				'id'            => 'footer-1',
				'description'   => $this->l( 'Appears in the footer section of the site.'),
				'before_widget' => '<aside id="%1$s" class="widget clearfix %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title"><span>',
				'after_title'   => '</span></h3>',
			));
		$this->addSidebar('footer-2',
			array(
				'name'          => $this->l( 'Footer 2' ),
				'id'            => 'footer-2',
				'description'   => $this->l( 'Appears in the footer section of the site.'),
				'before_widget' => '<aside id="%1$s" class="widget clearfix %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title"><span>',
				'after_title'   => '</span></h3>',
			));
		$this->addSidebar('footer-3',
			array(
				'name'          => $this->l( 'Footer 3' ),
				'id'            => 'footer-3',
				'description'   => $this->l( 'Appears in the footer section of the site.'),
				'before_widget' => '<aside id="%1$s" class="widget clearfix %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title"><span>',
				'after_title'   => '</span></h3>',
			));
		$this->addSidebar('footer-4',
			array(
				'name'          => $this->l( 'Footer 4' ),
				'id'            => 'footer-4',
				'description'   => $this->l( 'Appears in the footer section of the site.'),
				'before_widget' => '<aside id="%1$s" class="widget clearfix %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title"><span>',
				'after_title'   => '</span></h3>',
			));
        $this->addSidebar('newletter',
			array(
				'name'          => $this->l( 'Newletter' ),
				'id'            => 'newletter',
				'description'   => $this->l( 'Appears in the newletter section of the site.'),
				'before_widget' => '<aside id="%1$s" class="widget clearfix %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title"><span>',
				'after_title'   => '</span></h3>',
			));
	}
}
?>
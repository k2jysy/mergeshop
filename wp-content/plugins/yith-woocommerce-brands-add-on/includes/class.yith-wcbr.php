<?php
/**
 * Main class
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Brands
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCBR' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCBR' ) ) {
	/**
	 * WooCommerce Brands
	 *
	 * @since 1.0.0
	 */
	class YITH_WCBR {
		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCBR
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Taxonomy slug
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public static $brands_taxonomy = 'yith_product_brand';

		/**
		 * Constructor.
		 *
		 * @return \YITH_WCBR
		 * @since 1.0.0
		 */
		public function __construct() {
			// load plugin-fw
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

			// register new image dimensions
			add_action( 'after_setup_theme', array( $this, 'register_image_size' ) );

			// register brand taxonomy
			add_action( 'init', array( $this, 'register_taxonomy' ) );
			add_filter( 'yith_wcan_product_taxonomy_type', array( $this, 'add_ajax_navigation_taxonomy' ) );

			// add brand template to products page
			add_action( 'woocommerce_product_meta_end', array( $this, 'add_single_product_brand_template' ) );

			// add description to archive page
			add_action( 'woocommerce_archive_description', array( $this, 'add_archive_brand_template' ), 10 );

			// enqueue styles
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		}

		/* === PLUGIN FW LOADER === */

		/**
		 * Loads plugin fw, if not yet created
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if( ! empty( $plugin_fw_data ) ){
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once( $plugin_fw_file );
				}
			}
		}

		/* === TAXONOMY METHODS === */

		/**
		 * Register taxonomy for brands
		 *
		 * @return void
		 * @since 1.0.0
		 */
		 public function register_taxonomy(){
			 $taxonomy_labels = array(
				 'name' => __( '品牌', 'yith-wcbr' ),
				 'singular_name' => __( 'Brand', 'yith-wcbr' ),
				 'all_items' => __( '所有品牌', 'yith-wcbr' ),
				 'edit_item' => __( '编辑品牌', 'yith-wcbr' ),
				 'view_item' => __( 'View Brand', 'yith-wcbr' ),
				 'update_item' => __( '更新品牌', 'yith-wcbr' ),
				 'add_new_item' => __( '添加新品牌', 'yith-wcbr' ),
				 'new_item_name' => __( '新品牌名称', 'yith-wcbr' ),
				 'parent_item' => __( 'Parent Brand', 'yith-wcbr' ),
				 'parent_item_colon' => __( 'Parent Brand:', 'yith-wcbr' ),
				 'search_items' => __( '查找品牌', 'yith-wcbr' ),
				 'separate_items_with_commas' => __( 'Separate brands with commas', 'yith-wcbr' ),
				 'not_found' => __( '没有找到品牌', 'yith-wcbr' )
			 );

			 $taxonomy_args = array(
				 'label' => __( 'Brands', 'yith-wcbr' ),
				 'labels' => $taxonomy_labels,
				 'public' => true,
				 'show_admin_column' => true,
				 'hierarchical' => true,
				 'rewrite' => array(
					 'slug' => apply_filters( 'yith_wcbr_taxonomy_rewrite', 'product-brands' ),
					 'hierarchical' => true
				 ),
                 'capabilities' => apply_filters( 'yith_wcbr_taxonomy_capabilities', array(
                         'manage_terms' => 'manage_product_terms',
                         'edit_terms'   => 'edit_product_terms',
                         'delete_terms' => 'delete_product_terms',
                         'assign_terms' => 'assign_product_terms',
                     )
                 )

			 );

			 register_taxonomy( self::$brands_taxonomy, 'product', $taxonomy_args );
			 register_taxonomy_for_object_type( self::$brands_taxonomy, 'product' );
		 }

		/**
		 * Add compatibility to Ajax Navigation, forcing widget to display on brands archive pages
		 *
		 * @param $tax array Valid product taxonomies where to show ajax navigation widget
		 * @return array Filtered array
		 * @since 1.0.0
		 */
		public function add_ajax_navigation_taxonomy( $tax ) {
			return array_merge(
				$tax,
				array( self::$brands_taxonomy )
			);
		}
		
		/* === FRONTEND METHODS === */

		/**
		 * Enqueue frontend scripts
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue() {
			// include payment form template
			$template_name = 'brands.css';
			$locations = array(
				trailingslashit( WC()->template_path() ) . 'yith-wcbr/' . $template_name,
				trailingslashit( WC()->template_path() ) . $template_name,
				'yith-wcbr/' . $template_name,
				$template_name
			);

			$template = locate_template( $locations );

			if( ! $template ){
				$template = YITH_WCBR_URL . 'assets/css/yith-wcbr.css';
			}
			else{
				$search     = array( get_stylesheet_directory(), get_template_directory() );
				$replace    = array( get_stylesheet_directory_uri(), get_template_directory_uri() );
				$template = str_replace( $search, $replace, $template );
			}

			wp_register_style( 'yith-wcbr', $template );

			if( is_product() || is_tax( self::$brands_taxonomy ) ){
				do_action( 'yith_wcbr_enqueue_frontend_style' );
				wp_enqueue_style( 'yith-wcbr' );
			}
		}

		/**
		 * Register thumb size for brand logo on single product page
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_image_size() {
			$single_thumb_width = apply_filters( 'yith_wcbr_single_thumb_width', 0 );
			$single_thumb_height = apply_filters( 'yith_wcbr_single_thumb_height', 30 );
			$single_thumb_crop = apply_filters( 'yith_wcbr_single_thumb_crop', true );

			add_image_size( 'yith_wcbr_logo_size', $single_thumb_width, $single_thumb_height, $single_thumb_crop );
		}
		
		/**
		 * Include template for brands on single product page
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function add_single_product_brand_template() {
			global $product;

			// retrieve data to use in template
			$brands_taxonomy = self::$brands_taxonomy;
			$before_term_list = apply_filters( 'yith_wcbr_single_product_before_term_list', '' );
			$after_term_list = apply_filters( 'yith_wcbr_single_product_after_term_list', '' );
			$term_list_sep = apply_filters( 'yith_wcbr_single_product_term_list_sep', ', ' );
			$brands_label = get_option( 'yith_wcbr_brands_label' );
			$product_brands = get_the_terms( $product->id, self::$brands_taxonomy );
			$product_has_brands = ! is_wp_error( $product_brands ) && $product_brands;

			$args = array(
				'brands_taxonomy' => $brands_taxonomy,
				'before_term_list' => $before_term_list,
				'after_term_list' => $after_term_list,
				'term_list_sep' => $term_list_sep,
				'brands_label' => $brands_label,
				'product_brands' => $product_brands,
				'product_has_brands' => $product_has_brands
			);

			// include payment form template
			$template_name = 'single-product-brands.php';

			yith_wcbr_get_template( $template_name, $args );
		}

		/**
		 * Include template for brands on archive product page
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function add_archive_brand_template() {
			if( is_tax( self::$brands_taxonomy ) && get_query_var( 'paged' ) == 0 ){

				// retrieve data to use in template
				$qo = get_queried_object();
				$term_id = $qo->term_id;
				$term = get_term( $term_id, self::$brands_taxonomy );
				$term_description = $term->description;

				// include payment form template
				$template_name = 'archive-product-brands-description.php';
				$locations = array(
					trailingslashit( WC()->template_path() ) . 'yith-wcbr/' . $template_name,
					trailingslashit( WC()->template_path() ) . $template_name,
					'yith-wcbr/' . $template_name,
					$template_name
				);

				$template = locate_template( $locations );

				if( ! $template ){
					$template = YITH_WCBR_DIR . 'templates/' . $template_name;
				}

				include( $template );
			}
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCBR
		 * @since 1.0.0
		 */
		public static function get_instance(){
			if( is_null( self::$instance ) ){
				self::$instance = new self;
			}

			return self::$instance;
		}
	}
}

/**
 * Unique access to instance of YITH_WCBR class
 *
 * @return \YITH_WCBR
 * @since 1.0.0
 */
function YITH_WCBR(){
	return YITH_WCBR::get_instance();
}
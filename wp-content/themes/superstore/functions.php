<?php
/**
 * @package WordPress
 * @subpackage WPbase
 * @since WPbase 1.0
 */

// Init Framework
require_once('framework/loader.php');
require_once('sub/theme.php');
require_once('sub/pagebuilder.php');
require_once('sub/pagebuilder.php');
require_once('shortcode.php');

// Make theme available for translation
load_theme_textdomain('woocommerce', get_template_directory() . '/languages');
load_theme_textdomain('woothemes', get_template_directory() . '/languages');

define( 'THEME_OPTION_ACTIVED', class_exists('Options_Framework') );

add_theme_support( 'woocommerce' );

$wpo = new WPO_SubTheme();

$protocol = is_ssl() ? 'https:' : 'http:';


if( !defined("WPB_VC_VERSION") ){
	define("WPB_VC_VERSION",'4.3.3');	
}


/* add  post types support as default */
$wpo->addThemeSupport( 'post-formats',  array( 'link', 'gallery', 'image' , 'video' , 'audio' ) );

// Add size image
$wpo->addImagesSize('blog-thumbnail',190,190,true);
// Add Menus
$wpo->addMenu('mainmenu','Main Menu');
$wpo->addMenu('topmenu','Top Header Menu');
//$wpo->addThemeSupport( 'post-formats',  array( 'aside', 'link' , 'quote', 'image' ) );


// AddScript
$wpo->addScript('scroll_animate',get_template_directory_uri().'/js/smooth-scrollbar.js',array(),false,true);
$wpo->addScript('parallax',get_template_directory_uri().'/js/jquery.stellar.js',array(),false,true);
$wpo->addScript('modernizr',get_template_directory_uri().'/js/modernizr.custom.js',array(),false,true);
$wpo->addScript('noty_js',get_template_directory_uri().'/js/jquery.noty.packaged.min.js',array(),false,true);

$wpo->addScript('main_js',get_template_directory_uri().'/js/main.js',array(),false,true);

/* Add Google Font
$wpo->addStyle('theme-montserrat-font',$protocol.'//fonts.googleapis.com/css?family=Montserrat:400,700');
$wpo->addStyle('theme-opensans-font',$protocol.'//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800');
*/

$wpo->init();

if(!function_exists('superstore_pagination')){
	function superstore_pagination($per_page,$total,$max_num_pages=''){
		?>
		<div class="well small-padding clearfix">
			<?php global  $wp_query; ?>
	        <?php wpo_pagination($prev = __('Previous',TEXTDOMAIN), $next = __('Next',TEXTDOMAIN), $pages=$max_num_pages,array('class'=>'pull-left pagination-sm')); ?>

	        <div class="result-count pull-right">
	            <?php
	            $paged    = max( 1, $wp_query->get( 'paged' ) );
	            $first    = ( $per_page * $paged ) - $per_page + 1;
	            $last     = min( $total, $wp_query->get( 'posts_per_page' ) * $paged );

	            if ( 1 == $total ) {
	                _e( 'Showing the single result', 'woocommerce' );
	            } elseif ( $total <= $per_page || -1 == $per_page ) {
	                printf( __( 'Showing all %d results', 'woocommerce' ), $total );
	            } else {
	                printf( _x( 'Showing %1$d–%2$d of %3$d results', '%1$d = first, %2$d = last, %3$d = total', 'woocommerce' ), $first, $last, $total );
	            }
	            ?>
	        </div>
	    </div>
	<?php
	}
}

if(!function_exists('superstore_searchform')){
    function superstore_searchform(){
        if(class_exists('WooCommerce')){
        	global $wpdb;
			$dropdown_args = array(
                'show_counts'        => false,
                'hierarchical'       => true,
                'show_uncategorized' => 0
            );
        ?>
			<form role="search" method="get" class="searchform" action="<?php echo home_url('/'); ?>">

	            <div class="wpo-search input-group">
	                <div class="filter_type category_filter pull-left">
	                	<?php wc_product_dropdown_categories( $dropdown_args ); ?>    
	                </div>

	                <div class="filter_search pull-left">
	                    <input name="s" id="s" maxlength="40"
	                           class="form-control input-large input-search" type="text" size="20"
	                           placeholder="查找商品...">
	                <span class="input-group-addon input-large btn-search">
	                    <input type="submit" id="searchsubmit" class="fa" value="&#xf002;"/>
	                    <input type="hidden" name="post_type" value="product"/>
	                </span>
	                </div>
	            </div>
	        </form>
        <?php
        }else{
        	get_search_form();
        }
    }
}

if(!function_exists('wpo_cartdropdown')){
    function wpo_cartdropdown(){
        if(class_exists('WooCommerce')){
            global $woocommerce; ?>
            <div class="dropdown cart-header">
                <h4>购物车</h4>
                <a class="dropdown-toggle cart-dropdown" data-toggle="dropdown" data-hover="dropdown" data-delay="0" href="#" title="<?php _e('View your shopping cart', 'woothemes'); ?>">
                    <?php echo sprintf(_n('%d item', '%d items', $woocommerce->cart->cart_contents_count, 'woothemes'), $woocommerce->cart->cart_contents_count);?> - <?php echo $woocommerce->cart->get_cart_total(); ?>
                </a>
                <div class="dropdown-menu">
                    <?php woocommerce_mini_cart(); ?>
                </div>
            </div>
        <?php
        }
    }
}


/* ---------------------------------------------------------------------------
 * WooCommerce - Define image sizes
 * --------------------------------------------------------------------------- */
global $pagenow;
if ( is_admin() && isset( $_GET['activated'] ) && $pagenow == 'themes.php' ) add_action( 'init', 'wpo_woocommerce_image_dimensions', 1 );

function wpo_woocommerce_image_dimensions() {
    $catalog = array(
        'width'     => '279',   // px
        'height'    => '268',   // px
        'crop'      => 1        // true
    );

    $single = array(
        'width'     => '600',   // px
        'height'    => '576',   // px
        'crop'      => 1        // true
    );

    $thumbnail = array(
        'width'     => '80',   // px
        'height'    => '77',   // px
        'crop'      => 1        // true
    );

    // Image sizes
    update_option( 'shop_catalog_image_size', $catalog );       // Product category thumbs
    update_option( 'shop_single_image_size', $single );         // Single product image
    update_option( 'shop_thumbnail_image_size', $thumbnail );   // Image gallery thumbs
}

if( of_get_option('woo-show-noty') ) {
    add_action('init','WPO_jsWoocommerce');
    function WPO_jsWoocommerce(){
        wp_dequeue_script('wc-add-to-cart');
        wp_register_script( 'wc-add-to-cart', get_template_directory_uri(). '/js/add-to-cart.js' , array( 'jquery' ) );
        wp_localize_script('wc-add-to-cart','woocommerce_localize',array(
            'cart_success'=> __( of_get_option('woo-show-text', 'Success: Your item has been added to cart!') ),
        ));
        wp_enqueue_script('wc-add-to-cart');
    }
}

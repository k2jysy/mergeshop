<?php
/**
 * Single Product Up-Sells
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $woocommerce_loop,$wp_query;

$upsells = $product->get_upsells();
$posts_per_page = 6;
if ( sizeof( $upsells ) == 0 ) return;

$meta_query = WC()->query->get_meta_query();

$args = array(
	'post_type'           => 'product',
	'ignore_sticky_posts' => 1,
	'posts_per_page'      => $posts_per_page,
	'orderby'             => $orderby,
	'post__in'            => $upsells,
	'post__not_in'        => array( $product->id ),
	'meta_query'          => $meta_query
);
$_count =1;
$products = new WP_Query( $args );

$columns_count = of_get_option('woo-number-columns',4);
$class_column = 'col-sm-' . 12/$columns_count;
$woocommerce_loop['columns'] = $columns;

if ( $products->have_posts() ) : ?>

	<div class="box upsells products">
		<div class="box-heading">
	        <span><?php _e( 'You may also like&hellip;', 'woocommerce' ) ?></span>
		</div>
		<div class="woocommerce">
			<div class="widget-content <?php echo $style; ?>">
				<?php wc_get_template( 'widget-products/carousel.php' , array( 'loop'=>$products,'columns_count'=>$columns_count,'class_column'=>$class_column,'posts_per_page'=>$posts_per_page ) ); ?>
			</div>
		</div>
	</div>

<?php endif;

wp_reset_postdata();

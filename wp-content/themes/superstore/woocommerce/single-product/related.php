<?php
/**
 * Related Products
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $woocommerce_loop;

$related = $product->get_related( 6 );

if ( sizeof( $related ) == 0 ) return;
$posts_per_page = of_get_option('woo-number-product','4');
$args = apply_filters( 'woocommerce_related_products_args', array(
	'post_type'				=> 'product',
	'ignore_sticky_posts'	=> 1,
	'posts_per_page' 		=> $posts_per_page,
	'orderby' 				=> $orderby,
	'post__in' 				=> $related,
	'post__not_in'			=> array( $product->id )
) );
$_id = wpo_makeid();
$_count =1;
$products = new WP_Query( $args );

$columns_count = of_get_option('woo-number-columns',4);
$class_column = 'col-sm-' . 12/$columns_count;

if ( $products->have_posts() ) : ?>

	<div class="box related products">
		<div class="box-heading">
	        <span><?php _e( 'Related Products', 'woocommerce' ); ?></span>
		</div>
		<div class="woocommerce">
			<div class="widget-content <?php echo $style; ?>">
				<?php wc_get_template( 'widget-products/carousel.php' , array( 'loop'=>$products,'columns_count'=>$columns_count,'class_column'=>$class_column,'posts_per_page'=>$posts_per_page ) ); ?>
			</div>
		</div>

	</div>

<?php endif;

wp_reset_postdata();

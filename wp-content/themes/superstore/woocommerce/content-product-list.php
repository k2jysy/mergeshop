<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $woocommerce_loop;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
    $woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
    $woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

// Ensure visibility
if ( ! $product || ! $product->is_visible() )
    return;

// Increase loop count
$woocommerce_loop['loop']++;

// Extra post classes
$classes = array();
if ( 0 == ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 == $woocommerce_loop['columns'] )
    $classes = 'first';
if ( 0 == $woocommerce_loop['loop'] % $woocommerce_loop['columns'] )
    $classes = 'last';

$columns = 12/$woocommerce_loop['columns']
?>


<div <?php post_class( 'shopcol '.$classes.' col-md-12 product-cols' ); ?>>
	<?php
    global $product;
?>
<div class="product-block product product-list">	
	<div class="row">
		<div class="col-md-4 col-sm-4">
			<div class="image">
	            <a href="<?php the_permalink(); ?>">
	                <?php
	                    /**
	                     * woocommerce_before_shop_loop_item_title hook
	                     *
	                     * @hooked woocommerce_show_product_loop_sale_flash - 10
	                     * @hooked woocommerce_template_loop_product_thumbnail - 10
	                     */
	                    do_action( 'woocommerce_before_shop_loop_item_title' );
	                ?>
	    		</a>
			</div>
			<?php if(of_get_option('is-quickview',true)){ ?>
            <div class="quick-view">
                <a href="#" class="btn btn-quickview quickview"
                   data-productslug="<?php echo $product->post->post_name; ?>"
                   data-toggle="modal"
                   data-target="#wpo_modal_quickview">
                    <span><?php echo __('Quick view',TEXTDOMAIN); ?></span>
                </a>
            </div>
            <?php } ?>
		</div>

		<div class="product-meta col-md-5 col-sm-5">
			<div class="name">
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</div>			
			<?php woocommerce_template_single_excerpt(); ?>       


		</div>

		<div class="col-md-3 col-sm-3">
			<?php
				/**
				* woocommerce_after_shop_loop_item_title hook
				*
				* @hooked woocommerce_template_loop_rating - 5
				* @hooked woocommerce_template_loop_price - 10
				*/
				do_action( 'woocommerce_after_shop_loop_item_title' );
	        ?>
			 <div class="button-groups">
	                           

	                <?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
					<?php if( class_exists( 'YITH_WCWL' ) ) { ?>
	                	<?php echo do_shortcode( '[yith_wcwl_add_to_wishlist]' ); ?>
					<?php } ?>

					<?php if( class_exists( 'YITH_Woocompare' ) ) { ?>
						<?php
		                    $action_add = 'yith-woocompare-add-product';
		                    $url_args = array(
		                        'action' => $action_add,
		                        'id' => $product->id
		                    );
		                ?>
		                <a href="<?php echo wp_nonce_url( add_query_arg( $url_args ), $action_add ); ?>" 
		                    class="btn btn-compare compare"
		                    data-product_id="<?php echo $product->id; ?>">
		                    <span><?php echo __('add to compare',TEXTDOMAIN); ?></span>
		                </a>
	            	<?php } ?>
	        </div>
		</div>
	</div>	
</div>
</div>
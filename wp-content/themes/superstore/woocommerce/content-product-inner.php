<?php
/**
 * $Desc
 *
 * @version    $Id$
 * @package    wpbase
 * @author     WPOpal  Team <wpopal@gmail.com, support@wpopal.com>
 * @copyright  Copyright (C) 2014 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */
global $product;
?>
<div class="product-block product product-grid">
	<div class="product-image">
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
            <?php if ( $price_html = $product->get_price_html() ) { ?>
                <div class="price"><?php echo $price_html; ?></div>
            <?php } else { ?>
                <div class="price"></div>
            <?php } ?>

            <div class="button-groups">
                <?php if(of_get_option('is-quickview',true)){ ?>
                    <div class="quick-view">
                        <a href="#" class="btn btn-quickview quickview"
                           data-productslug="<?php echo $product->post->post_name; ?>"
                           data-toggle="modal"
                           data-target="#wpo_modal_quickview"
                            >
                            <i class="fa fa-eye"></i><span><?php echo __('Quick view',TEXTDOMAIN); ?></span>
                        </a>
                    </div>
                <?php } ?>
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
                        <i class="fa fa-files-o"></i>
                        <span><?php echo __('add to compare',TEXTDOMAIN); ?></span>
                    </a>
                <?php } ?>
            </div>
		</div>
	</div>

	<div class="product-meta">
        <?php echo $product->get_categories( ', ', '<h5 class="category">', '</h5>' ); ?>
        <div class="name">
            <a href="<?php the_permalink(); ?>"><?php the_title(''); ?></a>
        </div>
		<?php
            /**
             * woocommerce_after_shop_loop_item_title hook
             *
             * @hooked woocommerce_template_loop_rating - 5
             * @hooked woocommerce_template_loop_price - 10
             */

            do_action( 'woocommerce_after_shop_loop_item_title' );
        ?>



        <?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
        <?php
        $action_add = 'yith-woocompare-add-product';
        $url_args = array(
            'action' => $action_add,
            'id' => $product->id
        );
        ?>
	</div>
</div>


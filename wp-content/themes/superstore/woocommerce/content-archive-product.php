<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>

    <div class="wpo-page-title">
        <h1 class="page-title"><?php woocommerce_page_title(); ?></h1>
    </div>

<?php endif; ?>

<?php
if (is_product_category()){
    global $wp_query;
    // get the query object
    $cat = $wp_query->get_queried_object();
    // get the thumbnail id user the term_id
    $thumbnail_id = get_woocommerce_term_meta( $cat->term_id, 'thumbnail_id', true );
    if($thumbnail_id!=0){
        // get the image URL
        $image = wp_get_attachment_link( $thumbnail_id ,'category-image');

?>

    <div class="category-image">
        <?php echo $image; ?>
    </div>
    <?php } ?>
<?php } ?>
<?php do_action( 'woocommerce_archive_description' ); ?>
<?php if ( have_posts() ) : ?>
    <?php wc_print_notices(); ?>
	<div id="wpo-filter" class="clearfix wrapper">
        <?php do_action('wpo_button_display'); ?>

        <?php
            /**
             * woocommerce_before_shop_loop hook
             *
             * @hooked woocommerce_result_count - 20
             * @hooked woocommerce_catalog_ordering - 30
             */
             //woocommerce_show_messages();
            woocommerce_catalog_ordering();
        ?>
	</div>
    <?php woocommerce_product_loop_start(); ?>

        <?php woocommerce_product_subcategories(); ?>
        <?php
            if (isset($_COOKIE['wpo_cookie_layout']) && $_COOKIE['wpo_cookie_layout']=='list') {
                $layout = 'product-list';
            }else{
                $layout = 'product';
            }
        ?>
        <?php while ( have_posts() ) : the_post(); ?>
            <?php
                wc_get_template_part( 'content', $layout );
            ?>
        <?php endwhile; // end of the loop. ?>

    <?php woocommerce_product_loop_end(); ?>

    <div class="well clearfix product-bottom no-margin small-padding">
        <?php
            /**
             * woocommerce_after_shop_loop hook
             *
             * @hooked woocommerce_pagination - 10
             */

            add_action( 'woocommerce_after_shop_loop','woocommerce_result_count' ,20);
            do_action( 'woocommerce_after_shop_loop' );
        ?>
    </div>                    

<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>

    <?php wc_get_template( 'loop/no-products-found.php' ); ?>

<?php endif; ?>
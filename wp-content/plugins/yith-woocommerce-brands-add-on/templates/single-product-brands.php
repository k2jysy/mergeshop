<?php
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

global $product;
?>

<?php if( $product_has_brands ): ?>

	<?php if( ! isset( $content_to_show ) || ( $content_to_show == 'both' || $content_to_show == 'name' ) ): ?>
		<span class="yith-wcbr-brands">
			<?php echo $brands_label ?>
			<?php echo get_the_term_list( $product->id, $brands_taxonomy, $before_term_list, $term_list_sep, $after_term_list ); ?>
		</span>
	<?php endif; ?>

	<?php if( ! isset( $content_to_show ) || ( $content_to_show == 'both' || $content_to_show == 'logo' ) ): ?>
		<span class="yith-wcbr-brands-logo">
			<?php foreach( $product_brands as $term ) {
				$thumbnail_id = absint( get_woocommerce_term_meta( $term->term_id, 'thumbnail_id', true ) );

				if ( $thumbnail_id ) {
					$image = wp_get_attachment_image_src( $thumbnail_id, 'yith_wcbr_logo_size' );

					if( $image ){
						echo sprintf( '<a href="%s"><img src="%s" width="%d" height="%d" alt="%s"/></a>', get_term_link( $term ), $image[0], $image[1], $image[2], $term->name );
					}
				}
				else{
					do_action( 'yith_wcbr_no_brand_logo', $term->term_id, $term, 'yith_wcbr_logo_size', false, false );
				}
			}
			?>
		</span>
	<?php endif; ?>

<?php endif; ?>
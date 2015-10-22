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

global $post, $product, $woocommerce;

$attachment_ids = $product->get_gallery_attachment_ids();
$_images =array();
if(has_post_thumbnail()){
	$_images[] = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ));
}else{
	$_images[] = '<img src="'.wc_placeholder_img_src().'" alt="Placeholder" />';
}
foreach ($attachment_ids as $attachment_id) {
	$_images[]       = wp_get_attachment_image( $attachment_id, 'shop_single' );
}
?>
<div id="quickview-carousel" class="carousel slide" data-ride="carousel">
	<?php if(count($_images)>1){ ?>
	<!-- Indicators -->
	<ol class="carousel-indicators">
		<?php foreach ($_images as $key => $image) {
			echo '<li data-target="#quickview-carousel" data-slide-to="'.$key.'" '.(($key==0)?'class="active"':'').'></li>';
		} ?>
	</ol>
	<?php } ?>
	<!-- Wrapper for slides -->
	<div class="carousel-inner">
		<?php foreach ($_images as $key => $image) { ?>
		<div class="item<?php echo (($key==0)?' active':'') ?>">
			<?php echo $image; ?>
		</div>
		<?php } ?>
	</div>
    <div class="woo-carousel-controls">
        <!-- Controls -->
        <a class="left woo-carousel-control" href="#quickview-carousel" data-slide="prev">
            <i class="fa fa-angle-left"></i>
        </a>
        <a class="right woo-carousel-control" href="#quickview-carousel" data-slide="next">
            <i class="fa fa-angle-right"></i>
        </a>
    </div>
</div>
<?php
/**
 * $Desc
 *
 * @version    $Id$
 * @package    wpbase
 * @author     Opal  Team <opalwordpressl@gmail.com >
 * @copyright  Copyright (C) 2014 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */
$camera = new WP_Query(array(
				'post_type'=>'sliders',
				'posts_per_page'=>4,
				'tax_query'=>array(
						'taxonomy'=>'nivo-slider'
					)
				));
if( $camera->have_posts() ):
?>

<div class="camera-slider clearfix" >
	<div id="camera_slider_wrapper">
	<?php
		//Loop
		while($camera->have_posts()): $camera->the_post();
		$linkimage = wp_get_attachment_url(get_post_thumbnail_id());
	?>
		<div data-src="<?php echo $linkimage; ?>">
	        <div class="camera_caption fadeFromBottom">
	          	<?php the_title( '<h3>', '</h3>' ); ?>
	          	<p><?php the_content( ); ?></p>
	        </div>
	    </div>
		<?php endwhile; ?>
	</div>
</div>

<script>
jQuery(document).ready(function() {
	jQuery('#camera_slider_wrapper').camera({
	    height: '35%',
	    loader: 'none',
	    pagination: false
	});
});
</script>
 <?php endif; ?>
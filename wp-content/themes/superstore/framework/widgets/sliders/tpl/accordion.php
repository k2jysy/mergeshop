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
$sliders = new WP_Query(array(
				'post_type'=>'sliders',
				'posts_per_page'=>4,
				'tax_query'=>array(
						'taxonomy'=>'nivo-slider'
					)
				));
if( $sliders->have_posts() ):
?>
<div class="clearfix">
	<ul class='kwicks kwicks-horizontal clearfix' style="height:500px;overflow:hidden;">
	<?php
		$count=0;
		while($sliders->have_posts()): $sliders->the_post();
	?>
		<li <?php echo ($count==0)?"class='kwicks-selected'":""; ?>><?php the_post_thumbnail(); ?></li>
		<?php $count++; ?>
	<?php endwhile; ?>
	</ul>
</div>
<script>
	jQuery(document).ready(function() {
		jQuery('.kwicks').kwicks({
			minSize : 40,
			spacing : 3,
			behavior: 'slideshow'
		});
	});
</script>
<?php endif; ?>
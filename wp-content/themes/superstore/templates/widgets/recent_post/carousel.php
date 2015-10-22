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
$args = array(
	'post_type' => $post_type,
	'posts_per_page' => $number_post
);

$query = new WP_Query($args);

if($query->have_posts()):
	$count = 0;

$_id = time();
// Display the widget title
if ( $title ) {
	echo $before_title;
	echo $title;
	?>
		<span class="carousel-controls">
			<!-- Controls -->
			<a class="" href="#carousel-<?php echo $_id; ?>" data-slide="prev">
				<i class="fa fa-angle-left"></i>
			</a>
			<a class="" href="#carousel-<?php echo $_id; ?>" data-slide="next">
				<i class="fa fa-angle-right"></i>
			</a>
		</span>
	<?php
	echo $after_title;
}


?>
<div id="carousel-<?php echo $_id; ?>" class="post-widget media-post-carousel carousel slide" data-ride="carousel">
	<div class="carousel-inner">
	<?php while($query->have_posts()):$query->the_post(); ?>
		<!-- Wrapper for slides -->
		<div class="item<?php echo (($count==0)?" active":"") ?>">			
			<div class="description">
				<?php the_content() ?>
			</div>
			<div class="carousel-item">
				<figure class="carousel-image">						
					<?php the_post_thumbnail('blog-thumbnails');?>
				</figure>
				<div class="carousel-body">
					<?php the_title(); ?>
					<?php the_excerpt(); ?>
				</div>
			</div>			
		</div>
		<?php $count++; ?>
	<?php endwhile; ?>
	</div>
</div>
<?php endif; ?>
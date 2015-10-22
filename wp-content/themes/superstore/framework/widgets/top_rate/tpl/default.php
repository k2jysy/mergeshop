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
// Display the widget title
if ( $title ) {
    echo $before_title . $title . $after_title;
}

$rt_option = PostRatings()->getOptions();
$max_rating =  $rt_option['max_rating'];
?>
<div class="post-widget widget-rate">
	<?php foreach ($posts as $key => $post) { ?>
		<?php
			$rating = (float)get_post_meta($post->ID, 'rating', true);
			$current_rating = apply_filters('post_ratings_current_rating', sprintf('%.2F / %d', $rating, $max_rating), $rating, $max_rating);
		?>
		<article>
			<?php
				if(has_post_thumbnail()){
			?>
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( 'widget' ); ?>
			</a>
			<?php } ?>
			<h6>
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h6>
			<div class="post-rate ratings">
				<ul class="rated" style="width:<?php print ($max_rating * 16); ?>px" title="<?php esc_attr_e($current_rating); ?>">
					<li class="rating" style="width:<?php print ($rating * 16); ?>px">
						<span class="average">
							<?php print $current_rating; ?>
						</span>
						<span class="best">
							<?php print $max_rating; ?>
						</span>
					</li>
				</ul>
			</div>
			<p class="post-date">
				<?php the_time( 'd M Y' ); ?>
			</p>
		</article>
	<?php } ?>
</div>
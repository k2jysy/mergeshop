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

$args = array(
	'post_type' => 'post',
	'posts_per_page' => $number_post
);

$query = new WP_Query($args);
if($query->have_posts()):
?>
<div class="post-widget">
<?php
	while($query->have_posts()):$query->the_post();
?>
	<article class="item-post clearfix">
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
		<p class="post-date">
			<?php echo __('Posted: ',TEXTDOMAIN); ?>
			<?php the_time( 'd M Y' ); ?>
		</p>
		<p class="post-author">
			by <?php the_author_posts_link(); ?>
		</p>
	</article>
<?php endwhile; ?>
</div>
<?php endif; ?>

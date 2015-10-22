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
global $post;
?>
<ul class="social-networks none-style">
	<?php if((bool)of_get_option('sharing-facebook',true)): ?>
	<li class="facebook">
		<a data-toggle="tooltip" data-placement="<?php echo $args['position']; ?>" data-animation="<?php echo $args['animation']; ?>"  data-original-title="Facebook" href="http://www.facebook.com/sharer.php?s=100&p&#91;url&#93;=<?php the_permalink(); ?>&p&#91;title&#93;=<?php the_title(); ?>" target="_blank">
			<i class="fa fa-facebook"></i>
		</a>
	</li>
	<?php endif; ?>
	<?php if((bool)of_get_option('sharing-twitter',true)): ?>
	<li class="twitter">
		<a data-toggle="tooltip" data-placement="<?php echo $args['position']; ?>" data-animation="<?php echo $args['animation']; ?>"  data-original-title="Twitter" href="http://twitter.com/home?status=<?php the_title(); ?> <?php the_permalink(); ?>" target="_blank">
			<i class="fa fa-twitter"></i>
		</a>
	</li>
	<?php endif; ?>
	<?php if((bool)of_get_option('sharing-linkedin',true)): ?>
	<li class="linkedin">
		<a data-toggle="tooltip" data-placement="<?php echo $args['position']; ?>" data-animation="<?php echo $args['animation']; ?>"  data-original-title="LinkedIn" href="http://linkedin.com/shareArticle?mini=true&amp;url=<?php the_permalink(); ?>&amp;title=<?php the_title(); ?>" target="_blank">
			<i class="fa fa-linkedin"></i>
		</a>
	</li>
	<?php endif; ?>
	<?php if((bool)of_get_option('sharing-tumblr',true)): ?>
	<li class="tumblr">
		<a data-toggle="tooltip" data-placement="<?php echo $args['position']; ?>" data-animation="<?php echo $args['animation']; ?>"  data-original-title="Tumblr" href="http://www.tumblr.com/share/link?url=<?php echo urlencode(get_permalink()); ?>&amp;name=<?php echo urlencode($post->post_title); ?>&amp;description=<?php echo urlencode(get_the_excerpt()); ?>" target="_blank">
			<i class="fa fa-tumblr"></i>
		</a>
	</li>
	<?php endif; ?>
	<?php if((bool)of_get_option('sharing-google',true)): ?>
	<li class="google">
		<a data-toggle="tooltip" data-placement="<?php echo $args['position']; ?>" data-animation="<?php echo $args['animation']; ?>"  data-original-title="Google +1" href="https://plus.google.com/share?url=<?php the_permalink(); ?>" onclick="javascript:window.open(this.href,
'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" target="_blank">
			<i class="fa fa-google-plus"></i>
		</a>
	</li>
	<?php endif; ?>
	<?php if((bool)of_get_option('sharing-pinterest',true)): ?>
	<li class="pinterest">
		<?php $full_image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full'); ?>
		<a data-toggle="tooltip" data-placement="<?php echo $args['position']; ?>" data-animation="<?php echo $args['animation']; ?>"  data-original-title="Pinterest" href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode(get_permalink()); ?>&amp;description=<?php echo urlencode($post->post_title); ?>&amp;media=<?php echo urlencode($full_image[0]); ?>" target="_blank">
			<i class="fa fa-pinterest"></i>
		</a>
	</li>
	<?php endif; ?>
	<?php if((bool)of_get_option('sharing-email',true)): ?>
	<li class="email">
		<a data-toggle="tooltip" data-placement="<?php echo $args['position']; ?>" data-animation="<?php echo $args['animation']; ?>"  data-original-title="Email" href="mailto:?subject=<?php the_title(); ?>&amp;body=<?php the_permalink(); ?>">
			<i class="fa fa-envelope"></i>
		</a>
	</li>
	<?php endif; ?>
</ul>
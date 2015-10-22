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
?>
<div class="title">
	<h2 class="author-title">
		<?php echo __( 'About the Author :', TEXTDOMAIN ); ?>
		<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
			<?php echo get_the_author(); ?>
		</a>
	</h2>
</div>

<div class="author-about-container media">
	<div class="avatar-img pull-left">
		<?php echo get_avatar( get_the_author_meta( 'user_email' ),72 ); ?>
	</div>
	<!-- .author-avatar -->
	<div class="description media-body">
		<?php the_author_meta( 'description' ); ?>
	</div>
</div>
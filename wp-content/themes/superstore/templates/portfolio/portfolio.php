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
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="post-container">
		<div class="post-thumb">
			<?php
				if ( has_post_format( 'video' )) {
				?>
					<div class="video-responsive">
						<?php wpo_embed(); ?>
					</div>
				<?php
				}
				else if ( has_post_format( 'audio' )) {
				?>
					<div class="audio-thumb">
						<?php wpo_embed(); ?>
					</div>
				<?php
				}
				else if ( has_post_format( 'gallery' )) {
					$_imgs = wpo_gallery();
				?>
					<div id="post-slide-<?php the_ID(); ?>" class="carousel slide" data-ride="carousel">
						<div class="carousel-inner">
							<?php foreach ($_imgs as $key => $_img) {
								echo '<div class="item '.(($key==0)?'active':'').'">';
									echo '<img src="'.$_img.'" alt="">';
								echo '</div>';
							} ?>
						</div>
						<a class="left carousel-control" href="#post-slide-<?php the_ID(); ?>" data-slide="prev">
							<span class="fa fa-angle-left"></span>
						</a>
						<a class="right carousel-control" href="#post-slide-<?php the_ID(); ?>" data-slide="next">
							<span class="fa fa-angle-right"></span>
						</a>
					</div>
				<?php
				}
				else if (has_post_thumbnail()) {
				?>
				<a href="<?php the_permalink(); ?>" title="">
					<?php the_post_thumbnail();?>
				</a>
				<?php }
			?>
		</div>
		<div class="post-name">
			<h2 class="entry-title">
				<a href="<?php the_permalink(); ?>">
					<?php the_title(); ?>
				</a>
			</h2>
			<div class="entry-meta">
				<span class="published"><?php the_time( 'M d, Y' ); ?></span>
				<span class="meta-sep"> / </span>
				<span class="comment-count">
					<?php comments_popup_link(__(' 0 comment', TEXTDOMAIN), __(' 1 comment', TEXTDOMAIN), __(' % comments', TEXTDOMAIN)); ?>
				</span>
				<span class="meta-sep"> / </span>
				<span class="author-link"><?php the_author_posts_link(); ?></span>
				<?php if(is_tag()): ?>
				<span class="meta-sep"> / </span>
				<span class="tag-link"><?php the_tags('Tags: ',', '); ?></span>
				<?php endif; ?>
			</div>
		</div>
		<div class="entry-content">
			<?php echo wpo_excerpt(20); ?>
		</div>
		<div class="readmore">
			<a href="<?php the_permalink(); ?>" class="btn btn-primary btn-theme-default"><?php echo __( 'read more',TEXTDOMAIN ); ?></a>
		</div>
	</div>
</article>
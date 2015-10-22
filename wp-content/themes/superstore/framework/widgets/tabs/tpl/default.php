<ul class="nav nav-tabs">
	<?php if($show_popular_posts == 'true'): ?>
		<li class="active">
			<a href="#tab-popular" data-toggle="tab"><?php echo __('Popular', TEXTDOMAIN ); ?></a>
		</li>
	<?php endif; ?>
	<?php if($show_recent_posts == 'true'): ?>
		<li <?php if($show_popular_posts != 'true') echo 'class="active"'; ?>>
			<a href="#tab-recent" data-toggle="tab"><?php echo __('Recent', TEXTDOMAIN ); ?></a>
		</li>
	<?php endif; ?>
	<?php if($show_comments == 'true'): ?>
		<li <?php if($show_popular_posts != 'true' && $show_recent_posts != 'true' ) echo 'class="active"'; ?>>
			<a href="#tab-comments" data-toggle="tab"><?php echo __('Comments', TEXTDOMAIN ); ?></a>
		</li>
	<?php endif; ?>
</ul>

<!-- Tab panes -->
<div class="tab-content">
	<?php if($show_popular_posts == 'true'): ?>
		<div class="tab-pane active" id="tab-popular">
			<?php
			$popular_posts = new WP_Query('showposts='.$posts.'&meta_key=wpo_post_views_count&orderby=meta_value_num&order=DESC');
			if($popular_posts->have_posts()): ?>
			<div class="post-widget">
				<?php while($popular_posts->have_posts()): $popular_posts->the_post(); ?>
				<article>
					<?php if(has_post_thumbnail()): ?>
						<a href="<?php the_permalink(); ?>">
							<?php the_post_thumbnail( 'widget' ); ?>
						</a>
					<?php endif; ?>
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
		</div>
	<?php endif; ?>
	<?php if($show_recent_posts == 'true'): ?>
		<div class="tab-pane" id="tab-recent">
			<?php
			$recent_posts = new WP_Query('showposts='.$tags_count);
			if($recent_posts->have_posts()):
			?>
			<div class="post-widget">
				<?php while($recent_posts->have_posts()): $recent_posts->the_post(); ?>
				<article>
					<?php if(has_post_thumbnail()): ?>
						<a href="<?php the_permalink(); ?>">
							<?php the_post_thumbnail( 'widget' ); ?>
						</a>
					<?php endif; ?>
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
		</div>
	<?php endif; ?>
	<?php if($show_comments == 'true'): ?>
		<div class="tab-pane" id="tab-comments">
			<div class="comment-widget">
				<?php
				$number = $instance['comments'];
				global $wpdb;
				$recent_comments = "SELECT DISTINCT ID, post_title, post_password, comment_ID, comment_post_ID, comment_author, comment_author_email, comment_date_gmt, comment_approved, comment_type, comment_author_url, SUBSTRING(comment_content,1,110) AS com_excerpt FROM $wpdb->comments LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID = $wpdb->posts.ID) WHERE comment_approved = '1' AND comment_type = '' AND post_password = '' ORDER BY comment_date_gmt DESC LIMIT $number";
				$the_comments = $wpdb->get_results($recent_comments);
				foreach($the_comments as $comment) { ?>
				<article class="clearfix">
					<?php echo get_avatar($comment, '52'); ?>
					<h6>
						<?php echo strip_tags($comment->comment_author); ?> <?php __('says', TEXTDOMAIN ); ?>:
					</h6>
					<a class="comment-text-side" href="<?php echo get_permalink($comment->ID); ?>#comment-<?php echo $comment->comment_ID; ?>" title="<?php echo strip_tags($comment->comment_author); ?> on <?php echo $comment->post_title; ?>">
						<?php echo wpo_string_limit_words(strip_tags($comment->com_excerpt), 12); ?>...
					</a>
				</article>
				<?php } ?>
			</div>
		</div>
	<?php endif; ?>
</div>
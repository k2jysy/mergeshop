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
?>

<div class="comment-widget">
	<?php
	$number = $instance['number_comment'];
	global $wpdb;
	$recent_comments = "SELECT DISTINCT ID, post_title, post_password, comment_ID, comment_post_ID, comment_author, comment_author_email, comment_date_gmt, comment_approved, comment_type, comment_author_url, SUBSTRING(comment_content,1,110) AS com_excerpt FROM $wpdb->comments LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID = $wpdb->posts.ID) WHERE comment_approved = '1' AND comment_type = '' AND post_password = '' ORDER BY comment_date_gmt DESC LIMIT $number";
	$the_comments = $wpdb->get_results($recent_comments);
	foreach($the_comments as $comment) { ?>
	<article class="clearfix">
        <div class="avatar-comment-widget">
            <?php echo get_avatar($comment, '70'); ?>
        </div>
        <div class="content-comment-widget">
            <h6>
                <?php echo strip_tags($comment->comment_author); ?> <?php __('says', TEXTDOMAIN ); ?>:
            </h6>
            <a class="comment-text-side" href="<?php echo get_permalink($comment->ID); ?>#comment-<?php echo $comment->comment_ID; ?>" title="<?php echo strip_tags($comment->comment_author); ?> on <?php echo $comment->post_title; ?>">
                <?php echo wpo_string_limit_words(strip_tags($comment->com_excerpt), 12); ?>...
            </a>
        </div>
	</article>
	<?php } ?>
</div>
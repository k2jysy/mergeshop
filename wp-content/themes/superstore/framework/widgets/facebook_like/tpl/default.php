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

if($page_url): ?>
	<iframe src="http<?php echo (is_ssl())? 's' : ''; ?>://www.facebook.com/plugins/likebox.php?href=<?php echo urlencode($page_url); ?>&amp;width=<?php echo $width; ?>&amp;colorscheme=<?php echo $color_scheme; ?>&amp;show_faces=<?php echo $show_faces; ?>&amp;stream=<?php echo $show_stream; ?>&amp;header=<?php echo $show_header; ?>&amp;height=<?php echo $height; ?>&amp;force_wall=true<?php if($show_faces == 'true'): ?>&amp;connections=8<?php endif; ?>" 
		style="border:none; overflow:hidden; width:<?php echo $width; ?>px; height: <?php echo $height; ?>px;">
	</iframe>
<?php endif;
?>
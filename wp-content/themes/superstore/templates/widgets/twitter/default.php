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
<ul class="tw-widget">
	<?php foreach ((array)$response as $tweet) { ?>
	<li>
		<p><?php echo $this->processString($tweet->text); ?><br> </p>
		<p class="twitter-meta">
			<span class="user">
				<i class="icon-twitter"></i>
				<a href="https://twitter.com/<?php echo $twitteruser; ?>"><?php echo $twitteruser; ?></a>
			</span>
			<small>
				<i class="icon-calendar"></i>
				<?php echo $this->getAgo(strtotime($tweet->created_at)); ?>
			</small>
		</p>
	</li>
	<?php } ?>
</ul>
<?php
/**
 * $Desc
 *
 * @version    $Id$
 * @package    wpbase
 * @author     Brainweb  Team < support@brainweb.vn>
 * @copyright  Copyright (C) 2014 brainweb.vn. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.brainweb.vn
 * @support  http://www.brainweb.vn/support/forum.html
 */
?>

<?php get_header(); ?>

<section class="container">
	<div class="page_not_found text-center clearfix">
		<h1>
			<?php echo __('page not found',TEXTDOMAIN); ?>		
		</h1>
		<div class="bigtext">404</div>	
		<div class="col-sm-6 col-sm-offset-3">
			<p>
				<?php echo of_get_option('404','This is Photoshop\'s version  of Lorem Ipsum. Proin gravida nibh vel velit auctor aliquet. Aenean sollicitudin, lorem quis bibendum auctor, nisi elit consequat ipsum'); ?>
			</p>
		</div>
	</div>
</section>

<?php get_footer(); ?>
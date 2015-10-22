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

$config = $wpo->getPageConfig();
?>

<?php get_header(); ?>

<?php
	// Show Breadcumb
	if($config['breadcrumb']){
		wpo_breadcrumb();
	}
?>
<section id="wpo-mainbody" class=" wpo-mainbody container">
	<div class="row">
		<!-- MAIN CONTENT -->
		<div id="wpo-content" class="wpo-content <?php echo $config['main']['class']; ?>">
			<?php if($config['showtitle']){ ?>
				<h1 class="page-title"><span><?php the_title(); ?></span></h1>
			<?php } ?>
			<?php /* The loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<?php the_content(); ?>
					<?php wp_link_pages(); ?>
				</article><!-- #post -->
				<?php //comments_template(); ?>
			<?php endwhile; ?>
		</div>
		<!-- //MAIN CONTENT -->
		<?php /******************************* SIDEBAR LEFT ************************************/ ?>
		<?php if($config['left-sidebar']['show']){ ?>
			<div class="wpo-sidebar wpo-sidebar-1 <?php echo $config['left-sidebar']['class']; ?>">
				<?php if(is_active_sidebar($config['left-sidebar']['widget'])): ?>
				<div class="sidebar-inner">
					<?php dynamic_sidebar($config['left-sidebar']['widget']); ?>
				</div>
				<?php endif; ?>
			</div>
		<?php } ?>
		<?php /******************************* END SIDEBAR LEFT *********************************/ ?>

		<?php /******************************* SIDEBAR RIGHT ************************************/ ?>
		<?php if($config['right-sidebar']['show']){ ?>
			<div class="wpo-sidebar wpo-sidebar-2 <?php echo $config['right-sidebar']['class']; ?>">
				<?php if(is_active_sidebar($config['right-sidebar']['widget'])): ?>
				<div class="sidebar-inner">
					<?php dynamic_sidebar($config['right-sidebar']['widget']); ?>
				</div>
				<?php endif; ?>
			</div>
		<?php } ?>
		<?php /******************************* END SIDEBAR RIGHT *********************************/ ?>
	</div>
</section>
<?php get_footer(); ?>
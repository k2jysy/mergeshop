<?php
/**
 * The Template for displaying all single products.
 *
 * Override this template by copying it to yourtheme/woocommerce/single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

	<?php
		/**
		 * woocommerce_before_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked woocommerce_breadcrumb - 20
		 */
		do_action( 'woocommerce_before_main_content' );
	?>
		<div class="row">
			<section class="<?php echo $config['main']['class']; ?>">
				<?php while ( have_posts() ) : the_post(); ?>

					<?php wc_get_template_part( 'content', 'single-product' ); ?>

				<?php endwhile; // end of the loop. ?>
			</section>
			
			<?php /******************************* SIDEBAR LEFT ************************************/ ?>
		        <?php if($config['left-sidebar']['show']){ ?>
		            <div class="wpo-sidebar wpo-sidebar-1 <?php echo $config['left-sidebar']['class']; ?>">
		                <?php if(is_active_sidebar(of_get_option('woocommerce-single-left-sidebar'))): ?>
		                    <div class="sidebar-inner">
		                        <?php dynamic_sidebar(of_get_option('woocommerce-single-left-sidebar')); ?>
		                    </div>
		                <?php endif; ?>
		            </div>
		        <?php } ?>
		        <?php /******************************* END SIDEBAR LEFT *********************************/ ?>

		        <?php /******************************* SIDEBAR RIGHT ************************************/ ?>
		        <?php if($config['right-sidebar']['show']){ ?>
		            <div class="wpo-sidebar wpo-sidebar-2 <?php echo $config['right-sidebar']['class']; ?>">
		                <?php if(is_active_sidebar(of_get_option('woocommerce-single-right-sidebar'))): ?>
		                    <div class="sidebar-inner">
		                        <?php dynamic_sidebar(of_get_option('woocommerce-single-right-sidebar')); ?>
		                    </div>
		                <?php endif; ?>
		            </div>
		        <?php } ?>
		        <?php /******************************* END SIDEBAR RIGHT *********************************/ ?>
		</div>
	<?php
		/**
		 * woocommerce_after_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'woocommerce_after_main_content' );
	?>
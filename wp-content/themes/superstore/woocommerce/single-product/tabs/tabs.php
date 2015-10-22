<?php
/**
 * Single Product tabs
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Filter tabs and allow third parties to add their own
 *
 * Each tab is an array containing title, callback and priority.
 * @see woocommerce_default_product_tabs()
 */
$tabs = apply_filters( 'woocommerce_product_tabs', array() );

$_count=0;

if ( ! empty( $tabs ) ) : ?>

	<div class="woocommerce-tabs clearfix">
		<ul class="nav nav-tabs col-md-4 col-sm-12">
			<?php foreach ( $tabs as $key => $tab ) : ?>
				<li class="<?php echo $key ?>_tab<?php echo ($_count==0)?' active':''; ?>">
					<a data-toggle="tab" href="#tab-<?php echo $key ?>"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key ) ?></a>
				</li>
			<?php $_count++; endforeach; ?>
		</ul>
		<?php $_count=0; ?>
		<div class="tab-content col-md-8 col-sm-12">
			<?php foreach ( $tabs as $key => $tab ) : ?>
				<div class="tab-pane<?php echo ($_count==0)?' active':''; ?>" id="tab-<?php echo $key ?>">
					<?php call_user_func( $tab['callback'], $key, $tab ) ?>
				</div>
			<?php $_count++; endforeach; ?>
		</div>
	</div>

<?php endif; ?>
<?php
/**
 * Change password form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;
?>

<?php wc_print_notices(); ?>

<form action="<?php echo esc_url( get_permalink( wc_get_page_id( 'change_password' ) ) ); ?>" method="post">

	<div class="form-group">
		<label for="password_1"><?php _e( 'New password', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input type="password" class="input-text form-control" name="password_1" id="password_1" />
	</div>
	<div class="form-group">
		<label for="password_2"><?php _e( 'Re-enter new password', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input type="password" class="input-text form-control" name="password_2" id="password_2" />
	</div>
	
	<button class="button" name="change_password"><?php _e( 'Save', 'woocommerce' ); ?></button>

	<?php wp_nonce_field( 'woocommerce-change_password' ); ?>
	<input type="hidden" name="action" value="change_password" />

</form>
<?php
/**
 * Edit account form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;
?>

<?php wc_print_notices(); ?>

<form action="" method="post">
<?php do_action( 'woocommerce_edit_account_form_start' ); ?>
	<div class="form-group">
		<label for="account_first_name"><?php _e( 'First name', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input type="text" class="input-text form-control" name="account_first_name" id="account_first_name" value="<?php esc_attr_e( $user->first_name ); ?>" />
	</div>
	<div class="form-group">
		<label for="account_last_name"><?php _e( 'Last name', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input type="text" class="input-text form-control" name="account_last_name" id="account_last_name" value="<?php esc_attr_e( $user->last_name ); ?>" />
	</div>
	<div class="form-group">
		<label for="account_email"><?php _e( 'Email address', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input type="email" class="input-text form-control" name="account_email" id="account_email" value="<?php esc_attr_e( $user->user_email ); ?>" />
	</div>
	<div class="form-group">
		<label for="password_1"><?php _e( 'Password (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
		<input type="password" class="input-text form-control" name="password_1" id="password_1" />
	</div>
	<div class="form-group">
		<label for="password_2"><?php _e( 'Confirm new password', 'woocommerce' ); ?></label>
		<input type="password" class="input-text form-control" name="password_2" id="password_2" />
	</div>

	<button  class="button" name="save_account_details"><?php _e( 'Save changes', 'woocommerce' ); ?></button>
<?php do_action( 'woocommerce_edit_account_form' ); ?>
	<?php wp_nonce_field( 'save_account_details' ); ?>
	<input type="hidden" name="action" value="save_account_details" />
	
	<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
</form>

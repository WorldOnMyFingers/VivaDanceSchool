<?php
/**
 * Checkout coupon form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-coupon.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.4.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! wc_coupons_enabled() ) {
	return;
}

if ( empty( WC()->cart->applied_coupons ) ) {
	$info_message = apply_filters( 'woocommerce_checkout_coupon_message', __( 'Have a coupon?', 'woocommerce' ) . ' <a href="#" class="showcoupon">' . __( 'Click here to enter your code', 'woocommerce' ) . '</a>' );
	wc_print_notice( $info_message, 'notice' );
}

?>
<div class="checkout-row col-lg-6 pull-right">
	<div class="title"><?php _e( 'Coupon', 'inwavethemes' ); ?> <i class="fa fa-minus-square-o"></i>
	</div>
	<div class="box">
		<form method="post" class="checkout_coupon" style="display:block!important;padding:0;border:none;">

			<input type="text" name="coupon_code" class="input-text"
				   placeholder="<?php esc_attr_e( 'Coupon code', 'inwavethemes' ); ?>" id="coupon_code" value="" />

			<button type="submit" class="button" name="apply_coupon"
					value="<?php esc_attr_e( 'Apply Coupon', 'inwavethemes' ); ?>"><em class="fa-icon"><i
						class="fa fa-check"></i></em><?php esc_html_e( 'Apply Coupon', 'inwavethemes' ); ?></button>

		</form>
	</div>
</div>
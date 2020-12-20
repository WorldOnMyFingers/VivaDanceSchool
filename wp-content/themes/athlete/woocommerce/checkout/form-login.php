<?php
/**
 * Checkout login form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( is_user_logged_in() || 'no' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ) {
	return;
}

$info_message  = apply_filters( 'woocommerce_checkout_login_message', __( 'Returning customer?', 'inwavethemes' ) );
$info_message .= ' <a href="#" class="showlogin">' . __( 'Click here to login', 'inwavethemes' ) . '</a>';
//wc_print_notice( $info_message, 'notice' );
?>

<?php
	woocommerce_login_form(
		array(
			'message'  => __( 'If you have shopped with us before, please enter your details in the boxes below. If you are a new customer please proceed to the Billing &amp; Shipping section.', 'inwavethemes' ),
			'redirect' => wc_get_page_permalink( 'checkout' ),
			'hidden'   => true
		)
	);
?>

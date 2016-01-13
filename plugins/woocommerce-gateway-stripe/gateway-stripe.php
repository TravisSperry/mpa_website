<?php
/*
Plugin Name: WooCommerce Stripe Gateway
Plugin URI: http://woothemes.com/woocommerce
Description: A payment gateway for Stripe (https://stripe.com/). A Stripe account and a server with Curl, SSL support, and a valid SSL certificate is required (for security reasons) for this gateway to function. Stripe currently only supports USD and CAD.
Version: 1.3.5
Author: Mike Jolley
Author URI: http://mikejolley.com

	Copyright: © 2009-2011 WooThemes.
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html

	Stripe Docs: https://stripe.com/docs
*/

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) )
	require_once( 'woo-includes/woo-functions.php' );

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), 'b022f53cd049144bfd02586bdc0928cd', '18627' );

add_action( 'plugins_loaded', 'woocommerce_stripe_init', 0 );

function woocommerce_stripe_init() {

	if ( ! class_exists( 'WC_Payment_Gateway' ) )
		return;

	load_plugin_textdomain( 'wc_stripe', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	include_once( 'classes/class-wc-gateway-stripe.php' );

	if ( class_exists( 'WC_Subscriptions_Order' ) )
		include_once( 'classes/class-wc-gateway-stripe-subscriptions.php' );

	/**
	 * account_cc function.
	 *
	 * @access public
	 * @return void
	 */
	function woocommerce_stripe_saved_cards() {
		$credit_cards = get_user_meta( get_current_user_id(), '_stripe_customer_id', false );

		if ( ! $credit_cards )
			return;

		if ( isset( $_GET['delete_card'] ) ) {
			$credit_card = $credit_cards[ (int) $_GET['delete_card'] ];
			delete_user_meta( get_current_user_id(), '_stripe_customer_id', $credit_card );
		}

		$credit_cards = get_user_meta( get_current_user_id(), '_stripe_customer_id', false );

		if ( ! $credit_cards )
			return;
		?>
			<h2 id="saved-cards" style="margin-top:40px;"><?php _e('Saved cards', 'wc_stripe' ); ?></h2>
			<table class="shop_table">
				<thead>
					<tr>
						<th><?php _e('Card ending in...','wc_stripe'); ?></th>
						<th><?php _e('Expires','wc_stripe'); ?></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $credit_cards as $i => $credit_card ) : ?>
					<tr>
						<td><?php echo $credit_card['active_card']; ?></td>
						<td><?php echo $credit_card['exp_month'] . '/' . $credit_card['exp_year'] ?></td>
						<td>
							<a class="button" href="<?php echo add_query_arg( 'delete_card', $i ); ?>#saved-cards"><?php _e( 'Delete card', 'wc_stripe' ); ?></a>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php
	}

	add_action( 'woocommerce_after_my_account', 'woocommerce_stripe_saved_cards' );

	/**
 	* Add the Gateway to WooCommerce
 	**/
	function add_stripe_gateway($methods) {
		if ( class_exists( 'WC_Subscriptions_Order' ) )
			$methods[] = 'WC_Gateway_Stripe_Subscriptions';
		else
			$methods[] = 'WC_Gateway_Stripe';
		return $methods;
	}

	add_filter('woocommerce_payment_gateways', 'add_stripe_gateway' );
}
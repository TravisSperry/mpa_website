<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Plugin_Name
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Your Name or Company Name
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if( get_option('wew-remove-waitlist-on-uninstall') && get_option('wew-remove-waitlist-on-uninstall') == "on" ){
	
	global $wpdb;

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$wew_DBtable_name = $wpdb->prefix . 'woocommerce_waitlist';

	$sql = "DROP TABLE IF EXISTS " . $wew_DBtable_name . " ; ";

	$wpdb->query($sql);

	delete_option('wew-subscribe-to-waitlist-str');

	delete_option('wew-subscribe-backorders');
	
	delete_option('wew-back-in-stock_quantity');
	
	delete_option("wew-unsubscribe-page");

	delete_option("wew-out-of-stock-message");

	delete_option("wew-notify-available-product");

	delete_option("wew-enable-shop-manager-settings");

	delete_option("wew-remove-waitlist-on-uninstall");

	delete_option("wew-admin-notification-on-subscription");

	delete_option("wew-variations-out-of-stock-message");

	delete_option("notifications-sender-email");

	delete_option("wew-subscription-email-subject");

	delete_option("wew-subscription-email-from-name");
	
	delete_option("woowaitilist-default-email-options");
	
	delete_option("wew-custom-css");
	
	delete_option("wew-no-email-alert");

	delete_option("wew-already-product-subscribed");

	delete_option("wew-already-variation-subscribed");

	delete_option("wew-subscribe-product-success-msg");

	delete_option("wew-subscribe-variation-success-msg");	
}
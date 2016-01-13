<?php
/*
Plugin Name: WooWaitlist
Plugin URI: http://www.woowaitlist.com
Description: WooWaitlist, a waitlist plugin for your WooCommerce website by WPCream.com.
Author: WPCream.com
Version: 2.2.1
Author URI: http://wpcream.com
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/


require_once( plugin_dir_path( __FILE__ ) . 'public/class-woocommerce-waitlist.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 */
register_activation_hook( __FILE__, array( 'Woocommerce_Waitlist', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Woocommerce_Waitlist', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'Woocommerce_Waitlist', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */

function on_woocommerce_waitlist_loaded(){
	
	$canView_settings = false;

	if( is_user_logged_in() ){

		global $current_user, $wpdb;

		$allowed_settings_role = array( "administrator" );

		$wew_enable_shop_manager_settings = get_option('wew-enable-shop-manager-settings') && get_option('wew-enable-shop-manager-settings') == "on" ? true : false;

		if( $wew_enable_shop_manager_settings ){

			$allowed_settings_role[] = "shop_manager";
		}

		$user = get_userdata( $current_user->ID );

		$capabilities = $user->{$wpdb->prefix . 'capabilities'};

		if ( !isset( $wp_roles ) ){
		
			$wp_roles = new WP_Roles();
		}

		foreach ( $wp_roles->role_names as $role => $name ){

			if ( array_key_exists( $role, $capabilities ) ){

				if( !$canView_settings && in_array( $role, $allowed_settings_role ) ){

					$canView_settings = true;
				}

			}
	
		}
	
	}

	if( $canView_settings ){

		require_once( plugin_dir_path( __FILE__ ) . 'admin/class-woocommerce-waitlist.php' );
		
		Woocommerce_Waitlist_Admin::get_instance();
	}
}

if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {

	add_action( 'plugins_loaded', 'on_woocommerce_waitlist_loaded' );
}
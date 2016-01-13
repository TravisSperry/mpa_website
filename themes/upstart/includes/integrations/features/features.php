<?php
/**
 * Integrates this theme with the Features by WooThemes plugin
 * http://wordpress.org/plugins/features-by-woothemes/
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Styles
 */
function woo_features_scripts() {
	wp_register_style( 'woo-features-css', get_template_directory_uri() . '/includes/integrations/features/css/features.css' );
	wp_enqueue_style( 'woo-features-css' );

	wp_enqueue_script( 'woo-features-js', get_template_directory_uri() . '/includes/integrations/features/js/features.min.js' );
}
add_action( 'wp_enqueue_scripts', 'woo_features_scripts' );


/**
 * Customise Features
 * Change the default features columns to 3.
 * @param  integer $args['per_row'] Number of columns to display
 * @param  integer $args['size'] image size set to a crazy height to ensure the full size image is used.
 * @return array Features args
 */
function woo_customise_features( $args ) {
	$args['per_row'] 	= 999;
	$args['size']		= 99999;
	return $args;
}
add_filter( 'woothemes_features_args', 'woo_customise_features', 10 );
<?php
/**
 * Integrates this theme with the Testimonials by WooThemes plugin
 * http://wordpress.org/plugins/testimonials-by-woothemes/
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Styles
 */
function woo_testimonials_scripts() {
	wp_register_style( 'woo-testimonials-css', get_template_directory_uri() . '/includes/integrations/testimonials/css/testimonials.css' );
	wp_enqueue_style( 'woo-testimonials-css' );

	wp_enqueue_script( 'woo-testimonials-js', get_template_directory_uri() . '/includes/integrations/testimonials/js/testimonials.min.js' );
}
add_action( 'wp_enqueue_scripts', 'woo_testimonials_scripts' );


/**
 * Customise Testimonials
 * Change the default testimonials columns to 3. Change the Gravatar size to 100.
 * @param  integer $args['per_row'] Number of columns to display
 * @param  integer $args['size'] Gravatar size
 * @return array Testimonials args
 */
function woo_customise_testimonials( $args ) {
	$args['per_row'] 	= 4;
	$args['size']		= 500;
	$args['limit']		= 4;
	return $args;
}
add_filter( 'woothemes_testimonials_args', 'woo_customise_testimonials', 10 );


/**
 * Testimonials template
 * @param  string $template Testimonials Template
 */
function woo_customise_testimonials_template ( $template ) {
	return '<div id="quote-%%ID%%" class="%%CLASS%%"><div class="quote-content"><blockquote class="testimonials-text">%%AUTHOR%% %%TEXT%%</blockquote>%%AVATAR%%</div></div>';
}
add_filter( 'woothemes_testimonials_item_template', 'woo_customise_testimonials_template' );


/**
 * Display testimonial content in archives
 */
function woo_testimonial_content() {
	if ( is_post_type_archive( 'testimonial' ) ) {
		echo '<div class="testimonial-content">' . get_the_content() . '</div>';
	}
}
add_action( 'woo_loop_post_header', 'woo_testimonial_content' );
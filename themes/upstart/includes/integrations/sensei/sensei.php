<?php
/**
 * Integrates this theme with the Sensei plugin
 * http://www.woothemes.com/products/sensei/
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Styles
 */
function woo_sensei_scripts() {
	wp_register_style( 'woo-sensei-css', get_template_directory_uri() . '/includes/integrations/sensei/css/sensei.css' );
	wp_enqueue_style( 'woo-sensei-css' );
}
add_action( 'wp_enqueue_scripts', 'woo_sensei_scripts' );

/**
 * Wrappers
 */
add_action( 'sensei_before_main_content', 'woo_sensei_layout_wrap', 10 );
add_action( 'sensei_after_main_content', 'woo_sensei_layout_wrap_end', 10 );

add_action( 'sensei_before_main_content', 'woo_sensei_content_wrap', 14 );
add_action( 'sensei_after_main_content', 'woo_sensei_content_wrap_end', 8 );

function woo_sensei_layout_wrap() {
	echo '<div id="content" class="page content-container">';
}

function woo_sensei_layout_wrap_end() {
	echo '</div>';
}

function woo_sensei_content_wrap() {
	echo '<div class="sensei-wrap">';
}

function woo_sensei_content_wrap_end() {
	echo '</div>';
}

/**
 * Breadcrumbs
 */
add_action( 'sensei_before_main_content', 'woo_display_breadcrumbs', 12 );
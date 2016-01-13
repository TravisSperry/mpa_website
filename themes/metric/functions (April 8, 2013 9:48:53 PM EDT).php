<?php
// Start the engine
require_once(TEMPLATEPATH.'/lib/init.php');

// Add new image sizes
add_image_size('Slideshow', 500, 260, TRUE);
add_image_size('Mini', 90, 90, TRUE);

// Add widgeted footer section
add_action('genesis_before_footer', 'metric_include_footer_widgets'); 
function metric_include_footer_widgets() {
    require(CHILD_DIR.'/footer-widgeted.php');
}

// Customizes go to top text
add_filter('genesis_footer_backtotop_text', 'footer_backtotop_filter');
function footer_backtotop_filter($backtotop) {
    $backtotop = '[footer_backtotop text="Top of Page"]';
    return $backtotop;
} 

// Register widget areas
genesis_register_sidebar(array(
	'name'=>'Home Top Left',
	'id' => 'home-top-left',
	'description' => 'This is the top left section of the homepage.',
	'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget'  => '</div>',
	'before_title'=>'<h4 class="widgettitle">','after_title'=>'</h4>'
));
genesis_register_sidebar(array(
	'name'=>'Home Top Right',
	'id' => 'home-top-right',
	'description' => 'This is the top right section of the homepage.',
	'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget'  => '</div>',
	'before_title'=>'<h4 class="widgettitle">','after_title'=>'</h4>'
));
genesis_register_sidebar(array(
	'name'=>'Home Top Bar',
	'id' => 'home-top-bar',
	'description' => 'This is the bar under the home-top-bar section of the homepage.',
	'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget'  => '</div>',
	'before_title'=>'<h4 class="widgettitle">','after_title'=>'</h4>'
));
genesis_register_sidebar(array(
	'name'=>'Home Middle #1',
	'id' => 'home-middle-1',
	'description' => 'This is the first column of the middle section of the homepage.',
	'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget'  => '</div>',
	'before_title'=>'<h4 class="widgettitle">','after_title'=>'</h4>'
));
genesis_register_sidebar(array(
	'name'=>'Home Middle #2',
	'id' => 'home-middle-2',
	'description' => 'This is the second column of the middle section of the homepage.',
	'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget'  => '</div>',
	'before_title'=>'<h4 class="widgettitle">','after_title'=>'</h4>'
));
genesis_register_sidebar(array(
	'name'=>'Home Middle #3',
	'id' => 'home-middle-3',
	'description' => 'This is the third column of the middle section of the homepage.',
	'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget'  => '</div>',
	'before_title'=>'<h4 class="widgettitle">','after_title'=>'</h4>'
));
genesis_register_sidebar(array(
	'name'=>'Footer #1',
	'id' => 'footer-1',
	'description' => 'This is the first column of the footer section.',
	'before_title'=>'<h4 class="widgettitle">','after_title'=>'</h4>'
));
genesis_register_sidebar(array(
	'name'=>'Footer #2',
	'id' => 'footer-2',
	'description' => 'This is the second column of the footer section.',
	'before_title'=>'<h4 class="widgettitle">','after_title'=>'</h4>'
));
genesis_register_sidebar(array(
	'name'=>'Footer #3',
	'id' => 'footer-3',
	'description' => 'This is the third column of the footer section.',
	'before_title'=>'<h4 class="widgettitle">','after_title'=>'</h4>'
));
genesis_register_sidebar(array(
	'name'=>'Footer #4',
	'id' => 'footer-4',
	'description' => 'This is the fourth column of the footer section.',
	'before_title'=>'<h4 class="widgettitle">','after_title'=>'</h4>'
));

genesis_register_sidebar(array(
	'name'=>'Shop Sidebar',
	'id' => 'shopbar',
	'description' => 'This is sidebar for the shop catalog.',
	'before_title'=>'<h4 class="widgettitle">','after_title'=>'</h4>'
));

function remove_more_jump_link($link) { 
	$offset = strpos($link, '#more-');
	if ($offset) {
		$end = strpos($link, '"',$offset);
	}
	if ($end) {
		$link = substr_replace($link, '', $offset, $end-$offset);
	}
	return $link;
}

add_filter('the_content_more_link', 'remove_more_jump_link');

/**
* Creates sharethis shortcode
*/
if (function_exists('st_makeEntries')) :
add_shortcode('sharethis', 'st_makeEntries');
endif;

// WOOCOMMERCE
// remove sidebar from single product pages
// add_action('wp', create_function("", "if (is_singular(array('product'))) remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10);") );

global $woocommerce_loop;
$woocommerce_loop['columns'] = 3;
add_filter( 'loop_shop_per_page', create_function( '$cols', 'return 9;' ), 20 );

add_filter( 'woocommerce_checkout_fields', 'custom_override_checkout_fields' );
function custom_override_checkout_fields ( $fields) {
	unset($fields['billing']['billing_company']);
	unset($fields['billing']['billing_country']);

	return $fields;
}

add_action('woocommerce_after_order_notes', 'my_custom_checkout_field');

function my_custom_checkout_field( $checkout ) {
	echo '<div id="my_custom_checkout_field">';
	woocommerce_form_field( 'child_name', array (
		'type'			=> 'text',
		'class'			=> array('form-row-wide'),
		'label'			=> __('Child\'s Name *'),
		'placeholder'	=> __('First and Last Name'),
		), $checkout->get_value( 'child_name'));
	woocommerce_form_field( 'child_age', array (
		'type'			=> 'text',
		'class'			=> array('form-row-wide'),
		'label'			=> __('Child\'s Age *'),
		'placeholder'	=> __('Age'),
		), $checkout->get_value( 'child_age'));
	echo '</div>';	
}

add_action('woocommerce_checkout_process', 'my_custom_checkout_field_process');
function my_custom_checkout_field_process() {
	global $woocommerce;
	if (!$_POST['child_name'])
		$woocommerce->add_error(__('Please enter child name.') );
	if (!$_POST['child_age'])
		$woocommerce->add_error(__('Please enter child age.') );
}


if (!function_exists('disableAdminBar')) {

	function disableAdminBar(){
  
  	remove_action( 'admin_footer', 'wp_admin_bar_render', 1000 );
  
    function remove_admin_bar_style_backend() { 
      echo '<style>body.admin-bar #wpcontent, body.admin-bar #adminmenu { padding-top: 0px !important; }</style>';
    }
          
    add_filter('admin_head','remove_admin_bar_style_backend');
  
  }

}

add_filter('admin_head','remove_admin_bar_style_backend');

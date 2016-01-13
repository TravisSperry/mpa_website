<?php

if ( ! defined( 'ABSPATH' ) ) exit;



/*-----------------------------------------------------------------------------------*/

/* Start WooThemes Functions - Please refrain from editing this section */

/*-----------------------------------------------------------------------------------*/



// WooFramework init

require_once ( get_template_directory() . '/functions/admin-init.php' );



/*-----------------------------------------------------------------------------------*/

/* Load the theme-specific files, with support for overriding via a child theme.

/*-----------------------------------------------------------------------------------*/



$includes = array(

				'includes/theme-options.php', 				// Options panel settings and custom settings

				'includes/theme-functions.php', 			// Custom theme functions

				'includes/theme-actions.php', 				// Theme actions & user defined hooks

				'includes/theme-comments.php', 				// Custom comments/pingback loop

				'includes/theme-js.php', 					// Load JavaScript via wp_enqueue_script

				'includes/sidebar-init.php', 				// Initialize widgetized areas

				'includes/theme-widgets.php',				// Theme widgets

				'includes/theme-plugin-integrations.php'	// Plugin integrations

				);



// Allow child themes/plugins to add widgets to be loaded.

$includes = apply_filters( 'woo_includes', $includes );



foreach ( $includes as $i ) {

	locate_template( $i, true );

}



/*-----------------------------------------------------------------------------------*/

/* You can add custom functions below */

/*-----------------------------------------------------------------------------------*/



remove_filter( 'the_content', 'wpautop' );

remove_filter( 'the_excerpt', 'wpautop' );



add_filter( 'woocommerce_get_availability', 'custom_get_availability', 1, 2);

  

function custom_get_availability( $availability, $_product ) {

    //change text "Out of Stock' to 'SOLD OUT'

    if ( !$_product->is_in_stock() ) $availability['availability'] = __('Sold Out', 'woocommerce');

        return $availability;

    }



function custom_widget_featured_image() {

  global $post;



  echo tribe_event_featured_image( $post->ID, 'thumbnail' );

}



add_action( 'tribe_events_list_widget_before_the_event_title', 'custom_widget_featured_image' );



/**

 * Customize the checkout

 */



// Remove the Order Notes field

add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );

function custom_override_checkout_fields( $fields ) {

     unset($fields['order']['order_comments']);



     return $fields;

}



// Add student info fields for child name and age

add_action( 'woocommerce_after_order_notes', 'custom_field_child_name' );

function custom_field_child_name( $checkout ) {

 

    echo '<div id="custom_field_child_name"><h2>' . __('Student Info') . '</h2>';

 

    woocommerce_form_field( 'child_name', array(

        'type'          => 'text',

        'class'         => array('my-field-class form-row-wide'),

        'label'         => __('Child Name'),

        'placeholder'   => __('First and Last'),

        ), $checkout->get_value( 'child_name' ));



    woocommerce_form_field( 'child_age', array(

        'type'          => 'text',

        'class'         => array('my-field-class form-row-wide'),

        'label'         => __('Age'),

        'placeholder'   => __('Age'),

        ), $checkout->get_value( 'child_age' ));

 

    echo '</div>';

}

// Update the order meta with field value

add_action( 'woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_update_order_meta' );

 

function my_custom_checkout_field_update_order_meta( $order_id ) {

    if ( ! empty( $_POST['child_name'] ) ) {

        update_post_meta( $order_id, 'Child Name', sanitize_text_field( $_POST['child_name'] ) );

    }

    if ( ! empty( $_POST['child_age'] ) ) {

        update_post_meta( $order_id, 'Child Age', sanitize_text_field( $_POST['child_age'] ) );

    }

}


function custom_excerpt_length( $length ) {
	return 20;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

function catch_that_image() {
  global $post, $posts;
  $first_img = '';
  ob_start();
  ob_end_clean();
  $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
  $first_img = $matches [1] [0];

  if(empty($first_img)){ //Defines a default image
    $first_img = "/images/default.jpg";
  }
  return $first_img;
}


/*-----------------------------------------------------------------------------------*/

/* Don't add any code below here or the sky will fall down */

/*-----------------------------------------------------------------------------------*/

?>
<?php
/**
 * Functions
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Compatibility
 * Declare WooCommerce support
 */
function woocommerce_support() {
	add_theme_support( 'woocommerce' );
}

/*-----------------------------------------------------------------------------------*/
/* Styles
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_wc_css' ) ) {
	/**
	 * WooCommerce Styles
	 * Enqueue WooCommerce styles
	 */
	function woo_wc_css () {
		wp_register_style( 'woocommerce', esc_url( get_template_directory_uri() . '/includes/integrations/woocommerce/css/woocommerce.css' ) );
		wp_enqueue_style( 'woocommerce' );

		wp_enqueue_script( 'woocommerce-js', esc_url( get_template_directory_uri() . '/includes/integrations/woocommerce/js/woocommerce.min.js' ) );
	} // End woo_wc_css()
}

if ( ! function_exists( 'woo_wc_disable_css' ) ) {
	function woo_wc_disable_css() {
		/**
		 * Disable WooCommerce styles
		 */
		if ( version_compare( WOOCOMMERCE_VERSION, "2.1" ) >= 0 ) {
			// WooCommerce 2.1 or above is active
			add_filter( 'woocommerce_enqueue_styles', '__return_false' );
		} else {
			// WooCommerce is less than 2.1
			define( 'WOOCOMMERCE_USE_CSS', false );
		}
	}
}


/*-----------------------------------------------------------------------------------*/
/* Cart Fragment
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_wc_header_add_to_cart_fragment' ) ) {
	/**
	 * Cart Fragments
	 * Ensure cart contents update when products are added to the cart via AJAX
	 * @param  array $fragments Fragments to refresh via AJAX
	 * @return array            Fragments to refresh via AJAX
	 */
	function woo_wc_header_add_to_cart_fragment( $fragments ) {
		global $woocommerce;

		ob_start();

		woo_wc_cart_link();

		$fragments['a.cart-contents'] = ob_get_clean();

		return $fragments;
	} // End woo_wc_header_add_to_cart_fragment()
}

/*-----------------------------------------------------------------------------------*/
/* Body Class
/*-----------------------------------------------------------------------------------*/

function woo_wc_body_class( $classes ) {
	if ( is_checkout() && apply_filters( 'upstart_distraction_free_checkout', true ) ) {
		$classes[] = 'distraction-free-checkout';
	}
	return $classes;
}

/*-----------------------------------------------------------------------------------*/
/* Breadcrumbs
/*-----------------------------------------------------------------------------------*/

function woo_custom_breadcrumbs_trail_add_product_categories ( $trail ) {
  if ( ( get_post_type() == 'product' ) && is_singular() ) {
		global $post;

		$taxonomy = 'product_cat';

		$terms = get_the_terms( $post->ID, $taxonomy );
		$links = array();

		if ( $terms && ! is_wp_error( $terms ) ) {
		$count = 0;
			foreach ( $terms as $c ) {
				$count++;
				if ( $count > 1 ) { continue; }
				$parents = woo_get_term_parents( $c->term_id, $taxonomy, true, ', ', $c->name, array() );

				if ( $parents != '' && ! is_wp_error( $parents ) ) {
					$parents_arr = explode( ', ', $parents );

					foreach ( $parents_arr as $p ) {
						if ( $p != '' ) { $links[] = $p; }
					}
				}
			}

			// Add the trail back on to the end.
			// $links[] = $trail['trail_end'];
			$trail_end = get_the_title($post->ID);

			// Add the new links, and the original trail's end, back into the trail.
			array_splice( $trail, 2, count( $trail ) - 1, $links );

			$trail['trail_end'] = $trail_end;
		}
	}

	return $trail;
} // End woo_custom_breadcrumbs_trail_add_product_categories()

/**
 * Retrieve term parents with separator.
 *
 * @param int $id Term ID.
 * @param string $taxonomy.
 * @param bool $link Optional, default is false. Whether to format with link.
 * @param string $separator Optional, default is '/'. How to separate terms.
 * @param bool $nicename Optional, default is false. Whether to use nice name for display.
 * @param array $visited Optional. Already linked to terms to prevent duplicates.
 * @return string
 */

if ( ! function_exists( 'woo_get_term_parents' ) ) {
function woo_get_term_parents( $id, $taxonomy, $link = false, $separator = '/', $nicename = false, $visited = array() ) {
	$chain = '';
	$parent = &get_term( $id, $taxonomy );
	if ( is_wp_error( $parent ) )
		return $parent;

	if ( $nicename ) {
		$name = $parent->slug;
	} else {
		$name = $parent->name;
	}

	if ( $parent->parent && ( $parent->parent != $parent->term_id ) && !in_array( $parent->parent, $visited ) ) {
		$visited[] = $parent->parent;
		$chain .= woo_get_term_parents( $parent->parent, $taxonomy, $link, $separator, $nicename, $visited );
	}

	if ( $link ) {
		$chain .= '<a href="' . get_term_link( $parent, $taxonomy ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $parent->name ) ) . '">'.$parent->name.'</a>' . $separator;
	} else {
		$chain .= $name.$separator;
	}
	return $chain;
} // End woo_get_term_parents()
}
<?php
/**
 * Template functions
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/*-----------------------------------------------------------------------------------*/
/* Products
/* Functions affecting WooCommerce products
/*-----------------------------------------------------------------------------------*/

/**
 * Archive description wrap open
 * Checks if the shop page has content, or the term (category, tag etc) has content before displaying a wrap opener.
 * Hooked into: woocommerce_archive_description()
 */
function woo_wc_archive_description_wrap_open() {
	$page_id 		= get_option( 'woocommerce_shop_page_id' );
	$page_object 	= get_page( $page_id );

	if ( is_shop() && $page_object->post_content != '' ) {
		$description = true;
	} elseif ( ! is_shop() ) {
		$term_obj = get_queried_object();
		$description = term_description( $term_obj->term_id, $term_obj->taxonomy );
	} else {
		$description = '';
	}
	if ( $description != '' ) {
	?>
		<div class="archive-description content-container-fullwidth">
	<?php
	}
}

/**
 * Archive description wrap open
 * Checks if the shop page has content, or the term (category, tag etc) has content before displaying a wrap opener.
 * Hooked into: woocommerce_archive_description()
 */
function woo_wc_archive_description_wrap_close() {
	$page_id 		= get_option( 'woocommerce_shop_page_id' );
	$page_object 	= get_page( $page_id );

	if ( is_shop() && $page_object->post_content != '' ) {
		$description = true;
	} elseif ( ! is_shop() ) {
		$term_obj = get_queried_object();
		$description = term_description( $term_obj->term_id, $term_obj->taxonomy );
	} else {
		$description = '';
	}
	if ( $description != '' ) {
	?>
		</div>
	<?php
	}
}

/**
 * Archive sorting wrap open
 * Wraps the product sorter and totals
 */
function woo_wc_archive_sorting_wrap_open() {
	?>
		<div class="archive-description product-sorting content-container-fullwidth">
	<?php
}

/**
 * Archive sorting wrap close
 * Wraps the product sorter and totals
 */
function woo_wc_archive_sorting_wrap_close() {
	?>
		</div>
	<?php
}

/**
 * Opens the product details wrap (loop)
 */
function woo_wc_product_loop_content_wrap_open() {
	?>
		<div class="details">
	<?php
}


/**
 * Closes the product details wrap (loop)
 */
function woo_wc_product_loop_content_wrap_close() {
	?>
		</div><!-- /.details -->
	<?php
}

/**
 * Opens the product loop wrap
 */
function woo_wc_product_loop_wrap_open() {
	?>
		<div class="content-container-fullwidth-nopadding">
	<?php
}


/**
 * Closes the product loop wrap
 */
function woo_wc_product_loop_wrap_close() {
	?>
		</div><!-- /.content-container-fullwidth-nopadding -->
	<?php
}

/**
 * Upsells
 * Replace the default upsell function with our own which displays the correct number product columns
 */
if ( ! function_exists( 'woo_wc_upsell_display' ) ) {
	function woo_wc_upsell_display() {
	    woocommerce_upsell_display( -1, 3 );
	}
}


/**
 * Archive title
 */
function woo_wc_page_title() {
	?>
	<div class="archive-header content-container-fullwidth">
		<h1 class="page-title"><?php woocommerce_page_title(); ?></h1>
		<?php the_widget( 'WC_Widget_Product_Search', 'title=' ); ?>
	</div>
	<?php
}

/**
 * Related Products
 * Replace the default related products function with our own which displays the correct number of product columns
 * @return array indicates number of products to display and in how many columns
 */
function woo_wc_related_products() {
	$args = array(
		'posts_per_page' => 3,
		'columns'        => 3,
	);
	return $args;
}

if ( ! function_exists('woocommerce_output_related_products') && version_compare( WOOCOMMERCE_VERSION, "2.1" ) < 0 ) {
	function woocommerce_output_related_products() {
		    woocommerce_related_products( 3,3 );
	}
}


if ( ! function_exists( 'woo_wc_placeholder_img_src' ) ) {
	/**
	 * Custom Placeholder
	 * Checks the theme option. If a custom placeholder is specified display it, otherwise display our Woo themed placeholder.
	 * @param  string $src Placeholder URL
	 * @return string      Sanitized placeholder URL
	 */
	function woo_wc_placeholder_img_src( $src ) {
		global $woo_options;
		if ( isset( $woo_options['woo_placeholder_url'] ) && '' != $woo_options['woo_placeholder_url'] ) {
			$src = $woo_options['woo_placeholder_url'];
		}
		else {
			$src = get_template_directory_uri() . '/images/wc-placeholder.gif';
		}
		return esc_url( $src );
	} // End woo_wc_placeholder_img_src()
}


/*-----------------------------------------------------------------------------------*/
/* Layout
/* Functions affecting the WooCommerce layout
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_wc_before_content' ) ) {
	/**
	 * Woo Wrapper Open
	 * Wraps WooCommerce pages in WooTheme Markup
	 */
	function woo_wc_before_content() {
		global $woo_options;
		?>
		<!-- #content Starts -->
		<?php woo_content_before(); ?>
	    <div id="content" class="content-container">

	        <!-- #main Starts -->
	        <?php woo_main_before(); ?>
	        <div id="main">

	    <?php
	} // End woo_wc_before_content()
}

if ( ! function_exists( 'woo_wc_after_content' ) ) {
	/**
	 * Woo Wrapper Close
	 * Wraps WooCommerce pages in WooTheme Markup
	 */
	function woo_wc_after_content() {
		?>

			</div><!-- /#main -->
	        <?php woo_main_after(); ?>

	        <?php do_action( 'woocommerce_sidebar' ); ?>

	    </div><!-- /#content -->
		<?php woo_content_after(); ?>
	    <?php
	} // End woo_wc_after_content()
}

if ( ! function_exists( 'woo_wc_loop_products' ) ) {
	/**
	 * Products per page
	 * @return integer number of products per page
	 */
	function woo_wc_loop_products( $products ) {
		$products = apply_filters( 'upstart_products_per_page', $products = 12 );
		return $products;
	}
}

/*-----------------------------------------------------------------------------------*/
/* Pagination / Search */
/* Functions affecting WooCommerce / WooFramework pagination & search */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_wc_pagination' ) ) {
	/**
	 * WooCommerce Pagination
	 * Replaces WooCommerce pagination with the function in the WooFramework
	 * @uses  woo_wc_add_search_fragment()
	 * @uses  woo_wc_pagination_defaults()
	 * @uses  woo_pagination()
	 */
	function woo_wc_pagination() {
		if ( is_search() && is_post_type_archive() ) {
			add_filter( 'woo_pagination_args', 			'woo_wc_add_search_fragment', 10 );
			add_filter( 'woo_pagination_args_defaults', 'woo_wc_pagination_defaults', 10 );
		}
		woo_pagination();
	} // End woo_wc_pagination()
}

if ( ! function_exists( 'woo_wc_add_search_fragment' ) ) {
	/**
	 * Search Fragment
	 * @param  array $settings Fragments
	 * @return array           Fragments
	 */
	function woo_wc_add_search_fragment ( $settings ) {
		$settings['add_fragment'] = '&post_type=product';
		return $settings;
	} // End woo_wc_add_search_fragment()
}

if ( ! function_exists( 'woo_wc_pagination_defaults' ) ) {
	function woo_wc_pagination_defaults ( $settings ) {
		/**
		 * Pagination Defaults
		 * @param  array $settings Settings
	 	 * @return array           Settings
		 */
		$settings['use_search_permastruct'] = false;
		return $settings;
	} // End woo_wc_pagination_defaults()
}

/*-----------------------------------------------------------------------------------*/
/* Cart Link
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_wc_cart_link' ) ) {
	/**
	 * Cart link
	 */
	function woo_wc_cart_link() {
		global $woocommerce;
		?>
		<a class="cart-contents" href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e('View your shopping cart', 'woothemes'); ?>"><?php echo $woocommerce->cart->get_cart_total(); ?> <span class="count"><?php echo sprintf( _n('%d item', '%d items', $woocommerce->cart->get_cart_contents_count(), 'woothemes' ), $woocommerce->cart->get_cart_contents_count() );?></span></a>
		<?php
	}
}
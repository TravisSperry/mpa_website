<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**

 * Header Template

 *

 * Here we setup all logic and XHTML that is required for the header section of all screens.

 *

 * @package WooFramework

 * @subpackage Template

 */



 global $woo_options, $woocommerce;



?><!DOCTYPE html>

<html <?php language_attributes(); ?>>

<head>

<meta charset="<?php bloginfo( 'charset' ); ?>" />

<title><?php

	/*

	 * Print the <title> tag based on what is being viewed.

	 */

	global $page, $paged;



	wp_title( '|', true, 'right' );



	// Add the blog name.

	bloginfo( 'name' );



	// Add the blog description for the home/front page.

	$site_description = get_bloginfo( 'description', 'display' );

	if ( $site_description && ( is_home() || is_front_page() ) )

		echo " | $site_description";



	// Add a page number if necessary:

	if ( $paged >= 2 || $page >= 2 )

		echo ' | ' . sprintf( __( 'Page %s', 'woothemes' ), max( $paged, $page ) );



	?></title>

<?php woo_meta(); ?>

<link rel="pingback" href="<?php echo esc_url( get_bloginfo( 'pingback_url' ) ); ?>" />

<!--[if lt IE 9]>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>

<![endif]-->

<!--[if (gte IE 9) | (!IE)]>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

<![endif]-->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">

<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>

<script type="text/javascript" src="/wp-content/themes/upstart/scripts/trial.js"></script>

<?php

wp_head();

woo_head();

?>
<?php if (is_home()) {
?>
<script type="text/javascript" src="wp-content/themes/upstart/bootstrap/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="wp-content/themes/upstart/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="wp-content/themes/upstart/bootstrap/css/custom.css">
<link href="http://allfont.net/allfont.css?fonts=arial-black" rel="stylesheet" type="text/css" />
<?php
} ?>
</head>

<body <?php body_class(); ?>>

<?php woo_top(); ?>



<div id="wrapper">

	<div id="inner-wrapper">



    <?php woo_header_before(); ?>



	<header id="header" class="content-container">

		<?php woo_header_inside(); ?>



		<span class="nav-toggle"><a href="#navigation"><span><?php _e( 'Navigation', 'woothemes' ); ?></span></a></span>



	    <div class="site-header">

			<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>

		</div>



        <?php woo_nav_before(); ?>



		<nav id="navigation" class="col-full" role="navigation">



			<section class="menus">



			<a href="<?php echo home_url(); ?>" class="nav-home"><span><?php _e( 'Home', 'woothemes' ); ?></span></a>



			<?php if ( is_woocommerce_activated() && isset( $woo_options['woocommerce_header_cart_link'] ) && 'true' == $woo_options['woocommerce_header_cart_link'] ) { ?>

	        	<h3><?php _e( 'Shopping Cart', 'woothemes' ); ?></h3>

	        	<ul class="nav cart">

	        		<li <?php if ( is_cart() ) { echo 'class="current-menu-item"'; } ?>>

	        			<?php woo_wc_cart_link(); ?>

	        		</li>

	       		</ul>

	        <?php }

			if ( function_exists( 'has_nav_menu' ) && has_nav_menu( 'primary-menu' ) ) {

				echo '<h3>' . woo_get_menu_name('primary-menu') . '</h3>';

				wp_nav_menu( array( 'depth' => 6, 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'main-nav', 'menu_class' => 'nav', 'theme_location' => 'primary-menu' ) );

			} else {

			?>

	        <ul id="main-nav" class="nav">

				<?php if ( is_page() ) $highlight = 'page_item'; else $highlight = 'page_item current_page_item'; ?>

				<li class="<?php echo $highlight; ?>"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php _e( 'Home', 'woothemes' ); ?></a></li>

				<?php wp_list_pages( 'sort_column=menu_order&depth=6&title_li=&exclude=' ); ?>

			</ul><!-- /#nav -->

	        <?php } ?>



	    	</section><!--/.menus-->



	        <a href="#top" class="nav-close"><span><?php _e('Return to Content', 'woothemes' ); ?></span></a>



		</nav><!-- /#navigation -->



		<?php woo_nav_after(); ?>




  </div>
	</header><!-- /#header -->



	<?php woo_content_before(); ?>

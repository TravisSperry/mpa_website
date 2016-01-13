<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Footer Template
 *
 * Here we setup all logic and XHTML that is required for the footer section of all screens.
 *
 * @package WooFramework
 * @subpackage Template
 */
	global $woo_options;

	$settings = array(
                    'contact_twitter' 	=> 'woothemes',
                    'contact_number' 	=> '',
                    'contactform_email' => '',
					'contact_address' 	=> '',
                    );

	$settings = woo_get_dynamic_values( $settings );

	if ( apply_filters( 'upstart_footer_contact', true ) ) : ?>
		<?php
			// Contact vars
			$phone 		= esc_attr( $settings['contact_number'] );
			$email 		= esc_attr( $settings['contactform_email'] );
			$address 	= esc_attr( $settings['contact_address'] );
			$twitter 	= esc_attr( $settings['contact_twitter'] );
		?>
		<section class="homepage-contact content-container-fullwidth-nopadding">
			<h1 class="section-title"><?php _e( 'Get in touch', 'woothemes' ); ?></h1>
			<ul>
				<?php
				// Phone
				if ( isset( $phone ) && $phone != '' &&  apply_filters( 'upstart_homepage_contact_phone', true ) ) {
					echo '<li class="phone"><a href="tel:' . $phone . '"><span></span>' . $phone . '</a></li>';
				}

				// Email
				if ( isset( $email ) && $email != '' &&  apply_filters( 'upstart_homepage_contact_email', true ) ) {
					echo '<li class="email"><a href="mailto:' . $email . '"><span></span>' . $email . '</a></li>';
				}

				// Address
				if ( isset( $address ) && $address != '' &&  apply_filters( 'upstart_homepage_contact_address', true ) ) {
					echo '<li class="address"><span></span>' . $address . '</li>';
				}

				// Twitter
				if ( isset( $twitter ) && $twitter != '' &&  apply_filters( 'upstart_homepage_contact_twitter', true ) ) {
					echo '<li class="twitter"><a href="http://facebook.com/' . $twitter . '"><span></span>' . $twitter . '</a></li>';
				}
				?>
			</ul>
		</section>
	<?php endif;

	$total = 4;
	if ( isset( $woo_options['woo_footer_sidebars'] ) && ( '' != $woo_options['woo_footer_sidebars'] ) ) {
		$total = $woo_options['woo_footer_sidebars'];
	}

	if ( ( woo_active_sidebar( 'footer-1' ) ||
		   woo_active_sidebar( 'footer-2' ) ||
		   woo_active_sidebar( 'footer-3' ) ||
		   woo_active_sidebar( 'footer-4' ) ) && $total > 0 ) {

?>

	<?php woo_footer_before(); ?>

	<section id="footer-widgets" class="content-container col-<?php echo esc_attr( $total ); ?> fix">

		<?php $i = 0; while ( $i < $total ) { $i++; ?>
			<?php if ( woo_active_sidebar( 'footer-' . $i ) ) { ?>

		<div class="block footer-widget-<?php echo $i; ?>">
        	<?php woo_sidebar( 'footer-' . $i ); ?>
		</div>

	        <?php } ?>
		<?php } // End WHILE Loop ?>

	</section><!-- /#footer-widgets  -->
<?php } // End IF Statement ?>
	<footer id="footer" class="content-container">

		<div id="copyright" class="col-left">
		<?php if( isset( $woo_options['woo_footer_left'] ) && $woo_options['woo_footer_left'] == 'true' ) {
				echo stripslashes( $woo_options['woo_footer_left_text'] );
		} else { ?>
			<p><?php bloginfo(); ?> &copy; <?php echo date( 'Y' ); ?>. <?php _e( 'All Rights Reserved.', 'woothemes' ); ?></p>
		<?php } ?>
		</div>

		<div id="credit" class="col-right">
        <?php if( isset( $woo_options['woo_footer_right'] ) && $woo_options['woo_footer_right'] == 'true' ) {
        	echo stripslashes( $woo_options['woo_footer_right_text'] );
		} else { ?>
			<p><?php _e( 'Powered by', 'woothemes' ); ?> <a href="<?php echo esc_url( 'http://www.wordpress.org' ); ?>"><?php _e( 'WordPress', 'woothemes' ); ?></a>. <?php _e( 'Designed by', 'woothemes' ); ?> <a href="<?php echo esc_url( 'http://www.woothemes.com/' ); ?>"><img src="<?php echo esc_url( get_template_directory_uri() . '/images/woothemes.png' ); ?>" width="74" height="19" alt="WooThemes" /></a></p>
		<?php } ?>
		</div>

	</footer><!-- /#footer  -->
	</div><!-- /#inner-wrapper -->
</div><!-- /#wrapper -->
<?php wp_footer(); ?>
<?php woo_foot(); ?>
</body>
</html>
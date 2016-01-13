<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * The default template for displaying content
 */

	global $woo_options;

/**
 * The Variables
 *
 * Setup default variables, overriding them if the "Theme Options" have been saved.
 */

 	$settings = array(
					'thumb_w' => 700,
					'thumb_h' => 700,
					'thumb_align' => 'alignleft'
					);

	$settings = woo_get_dynamic_values( $settings );

?>

	<article <?php post_class(); ?>>

	    <?php
	    	if ( isset( $woo_options['woo_post_content'] ) && $woo_options['woo_post_content'] != 'content' ) {
	    		woo_image( 'quality=100&width=' . $settings['thumb_w'] . '&height=' . $settings['thumb_h'] . '&class=thumbnail ' . $settings['thumb_align'] );
	    	}
	    ?>

		<header class="post-header">
			<span class="post-date"><?php echo human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) . __( ' ago', 'woothemes' ); ?></span>
			<h1><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>

			<?php do_action( 'woo_loop_post_header' ); ?>

		</header>

		<footer class="post-more">
			<span class="categories"><?php the_category( ', ' ); ?></span>
			<span class="comments"><?php comments_popup_link( __( '0', 'woothemes' ), __( '1', 'woothemes' ), __( '%', 'woothemes' ) ); ?></span>
		</footer>

	</article><!-- /.post -->
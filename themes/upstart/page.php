<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Page Template
 *
 * This template is the default page template. It is used to display content when someone is viewing a
 * singular view of a page ('page' post_type) unless another page template overrules this one.
 * @link http://codex.wordpress.org/Pages
 *
 * @package WooFramework
 * @subpackage Template
 */
	get_header();
	global $woo_options;

/**
 * The Variables
 *
 * Setup default variables, overriding them if the "Theme Options" have been saved.
 */

    $settings = array(
                    'thumb_single' => 'false',
                    'single_w' => 1250,
                    'single_h' => 1250,
                    'thumb_single_align' => 'aligncenter'
                    );

    $settings = woo_get_dynamic_values( $settings );

    if ( get_the_post_thumbnail( $post->ID ) ) {
        $featured_image = get_the_post_thumbnail( $post->ID );
    } else {
        $featured_image = false;
    }
?>

    <div id="content" class="page content-container">

    	<?php woo_main_before(); ?>

		<section id="main">

        <?php echo woo_embed( 'width=1250' ); ?>

        <?php if ( $featured_image ) : ?>

            <div class="featured-image">
                <?php if ( $settings['thumb_single'] == 'true' && ! woo_embed( '' ) ) { woo_image( 'width=' . $settings['single_w'] . '&height=' . $settings['single_h'] . '&class=thumbnail ' . $settings['thumb_single_align'] ); } ?>
            </div><!--/.featured-image-->

        <?php endif; ?>

        <?php
        	if ( have_posts() ) { $count = 0;
        		while ( have_posts() ) { the_post(); $count++;
        ?>
            <article <?php post_class(); ?>>

				<header>
			    	<h1><?php the_title(); ?></h1>
				</header>

                <section class="entry">
                	<?php the_content(); ?>

					<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'woothemes' ), 'after' => '</div>' ) ); ?>
               	</section><!-- /.entry -->

                <?php
                	comments_template();

    				} // End WHILE Loop
    			} else {
        		?>
        			<article <?php post_class(); ?>>
                    	<p><?php _e( 'Sorry, no posts matched your criteria.', 'woothemes' ); ?></p>
                    </article><!-- /.post -->
                <?php } // End IF Statement ?>

            </article><!-- /.post -->

		</section><!-- /#main -->

		<?php woo_main_after(); ?>

    </div><!-- /#content -->

<?php get_footer(); ?>
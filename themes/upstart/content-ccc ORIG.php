<?php
if ( ! defined( 'ABSPATH' ) ) exit;


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

?>

    <div style="background:#e7e7e7" id="content" class="content-container">

		<section id="main">

		<?php echo woo_embed( 'width=1250' ); ?>

		<div class="content-box">

			<article <?php post_class(); ?>>

                <header class="post-header">

                	<div class="avatar-comments">
                		<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" class="avatar-link">
		                	<?php echo get_avatar( get_the_author_meta( 'ID' ), '200' ); ?>
		                </a>
	                	<a href="#comments" class="comment-count">
	                		<span class="count"><?php comments_number( '0', '1', '%' ); ?></span>
	                	</a>
	                </div>

	                <h1><?php the_title(); ?></h1>

                	<?php woo_post_meta(); ?>

                </header>

                <section class="entry fix">

	       			<?php if ( $featured_image ) : ?>

			            <div class="featured-image">
			                <?php if ( $settings['thumb_single'] == 'true' && ! woo_embed( '' ) ) { woo_image( 'width=' . $settings['single_w'] . '&height=' . $settings['single_h'] ); } ?>
			            </div><!--/.featured-image-->

	    			<?php endif; ?>
			
					<?php the_content(); ?>
				</section>

            </article><!-- .post -->

				<?php woo_subscribe_connect(); ?>

       </div><!-- /.content-box -->

		</section><!-- #main -->

    </div><!-- #content -->
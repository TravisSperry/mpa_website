<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Single Post Template
 *
 * This template is the default page template. It is used to display content when someone is viewing a
 * singular view of a post ('post' post_type).
 * @link http://codex.wordpress.org/Post_Types#Post
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

    <div id="content" class="content-container">

    	<?php woo_main_before(); ?>

		<section id="main">

		<?php echo woo_embed( 'width=1250' ); ?>

		<div class="content-box">

        <?php
        	if ( have_posts() ) { $count = 0;
        		while ( have_posts() ) { the_post(); $count++;
        ?>
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
					<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'woothemes' ), 'after' => '</div>' ) ); ?>
				</section>

				<?php the_tags( '<p class="tags">'.__( 'Tags: ', 'woothemes' ), ', ', '</p>' ); ?>

            </article><!-- .post -->

				<?php if ( isset( $woo_options['woo_post_author'] ) && $woo_options['woo_post_author'] == 'true' ) { ?>
				<aside id="post-author" class="content-box-fullwidth">
					<div class="profile-image"><?php echo get_avatar( get_the_author_meta( 'ID' ), '400' ); ?></div>
					<div class="profile-content">
						<h3 class="title"><?php printf( esc_attr__( 'About %s', 'woothemes' ), get_the_author() ); ?></h3>
						<?php the_author_meta( 'description' ); ?>
						<div class="profile-link">
							<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
								<?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', 'woothemes' ), get_the_author() ); ?>
							</a>
						</div><!-- #profile-link	-->
					</div><!-- .post-entries -->
				</aside><!-- .post-author-box -->
				<?php } ?>

				<?php woo_subscribe_connect(); ?>

	        <nav id="post-entries" class="fix">
	            <div class="nav-prev fl"><?php previous_post_link( '%link', '%title' ); ?></div>
	            <div class="nav-next fr"><?php next_post_link( '%link', '%title' ); ?></div>
	        </nav><!-- #post-entries -->
            <?php
            	comments_template();

				} // End WHILE Loop
			} else {
		?>
			<article <?php post_class(); ?>>
            	<p><?php _e( 'Sorry, no posts matched your criteria.', 'woothemes' ); ?></p>
			</article><!-- .post -->
       	<?php } ?>

       </div><!-- /.content-box -->

		</section><!-- #main -->

		<?php woo_main_after(); ?>

    </div><!-- #content -->

<?php get_footer(); ?>
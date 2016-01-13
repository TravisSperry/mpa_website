<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Template Name: Blog
 *
 * The blog page template displays the "blog-style" template on a sub-page.
 *
 * @package WooFramework
 * @subpackage Template
 */

 global $woo_options, $wp_query, $post;
 get_header();

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

    if ( $post->post_content != ""  ) {
        $has_content = true;
    } else {
        $has_content = false;
    }

?>
    <!-- #content Starts -->
    <div id="content" class="content-container">

        <?php woo_main_before(); ?>

        <section id="main">

        <?php
        if ( have_posts() ) {
            while ( have_posts() ) { the_post(); ?>
        <header class="archive-header content-container-fullwidth">
            <h1 class="fl"><?php the_title(); ?></h1>
            <span class="fr archive-rss"><?php echo '<a href="' . esc_url( get_bloginfo( 'rss2_url' ) ) . '">' . __( 'RSS feed', 'woothemes' ) . '</a>'; ?></span>
        </header>
        <?php if ( $has_content ) : ?>
            <div class="archive-description content-container-fullwidth">
                <?php the_content(); ?>
            </div><!--/.archive-description -->
        <?php endif; ?>
        <?php
            } // end while
        } // endif ?>

		<?php woo_loop_before(); ?>

        <?php
            if ( get_query_var( 'paged') ) { $paged = get_query_var( 'paged' ); } elseif ( get_query_var( 'page') ) { $paged = get_query_var( 'page' ); } else { $paged = 1; }

            $query_args     = array(
                                'post_type' => 'post',
                                'paged' => $paged
                            );
            $query_args     = apply_filters( 'woo_blog_template_query_args', $query_args ); // Do not remove. Used to exclude categories from displaying here.

            $old_query      = $wp_query;
            $query          = new WP_Query( $query_args );
            $wp_query       = $query;

            if ( $query->have_posts() ) {

                $count = 0;

                while ( $query->have_posts() ) {

                    $query->the_post();
                    $count++;

                    /* Include the Post-Format-specific template for the content.
                     * If you want to overload this in a child theme then include a file
                     * called content-___.php (where ___ is the Post Format name) and that will be used instead.
                     */
                    get_template_part( 'content', get_post_format() );

                } // End WHILE Loop

            } else {
        ?>

            <article <?php post_class(); ?>>
                <p><?php _e( 'Sorry, no posts matched your criteria.', 'woothemes' ); ?></p>
            </article><!-- /.post -->

        <?php } // End IF Statement ?>

        <?php woo_loop_after(); ?>

        <?php woo_pagenav(); ?>

		<?php wp_reset_postdata(); ?>

        <?php $wp_query = $old_query; ?>

        </section><!-- /#main -->

        <?php woo_main_after(); ?>

    </div><!-- /#content -->

<?php get_footer(); ?>
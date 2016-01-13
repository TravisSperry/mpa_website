<?php get_header(); ?>
<?php genesis_home(); ?>
<script type="text/javascript">var _kiq = _kiq || [];</script>
<script type="text/javascript" src="//s3.amazonaws.com/ki.js/41514/7-l.js" async="true"></script>
<div id="home-top-bg">
	<div id="home-top">
<!--	<center><h1>STEM Enrichment that Kids LOVE!</h1></center>
			<div class="home-top-left">
			<?php if (!dynamic_sidebar('Home Top Left')) : ?>
			<div class="widget">
				<?php if( function_exists('wp_cycle') ) : ?>
					<?php wp_cycle(); ?>
				<?php endif; ?>
			</div>			
			<?php endif; ?>
		</div><!- - end .home-top-left - ->
-->
		<div class="home-top-right">
			<?php if (!dynamic_sidebar('Home Top Right')) : ?>
			<div class="widget">
				<h4><?php _e("Home Top Right", 'genesis'); ?></h4>
				<p><?php _e("This is a widgeted area which is called Home Top Right. It is using the Genesis - Featured Page widget to display what you see on the Metric child theme demo site. To get started, log into your WordPress dashboard, and then go to the Appearance > Widgets screen. There you can drag the Genesis - Featured Page widget into the Home Top widget area on the right hand side.", 'genesis'); ?></p>
			</div>			
			<?php endif; ?>
		</div><!-- end .home-top-right -->
	</div><!-- end #home-top -->
</div><!-- end #home-top-bg -->

<div id="home-top-bar-bg">
	<div class="home-top-bar">
		<?php if (!dynamic_sidebar('Home Top Bar')) : ?>
		<div class="widget">
			<h4><?php _e("Home Top Bar", 'genesis'); ?></h4>
			<p><?php _e("This is a widgeted area which is called Home Top Bar. It is using the Genesis - Featured Page widget to display what you see on the Metric child theme demo site. To get started, log into your WordPress dashboard, and then go to the Appearance > Widgets screen. There you can drag the Genesis - Featured Page widget into the Home Top widget area on the right hand side.", 'genesis'); ?></p>
		</div>			
		<?php endif; ?>
	</div><!-- end .home-top-bar -->
</div><!-- end #home-top-bar-bg -->

<div id="home-middle-bg">
	<div id="home-middle">
		<div class="home-middle-1">
			<?php if (!dynamic_sidebar('Home Middle #1')) : ?>
			<div class="widget">
				<h4><?php _e("Home Middle #1 Widget", 'genesis'); ?></h4>
				<p><?php _e("This is a widgeted area which is called Home Middle #1. It is using the Genesis - Featured Page widget to display what you see on the Metric child theme demo site. To get started, log into your WordPress dashboard, and then go to the Appearance > Widgets screen. There you can drag the Genesis - Featured Page widget into the Home Middle #1 widget area on the right hand side. To get the image to display, simply upload an image through the media uploader on the edit post screen and publish your page. The Featured Page widget will know to display the post image as long as you select that option in the widget interface.", 'genesis'); ?></p>
			</div>		
			<?php endif; ?>
		</div><!-- end .home-middle-1 -->
		<div class="home-middle-2">
			<?php if (!dynamic_sidebar('Home Middle #2')) : ?>
			<div class="widget">
				<h4><?php _e("Home Middle #2 Widget", 'genesis'); ?></h4>
				<p><?php _e("This is a widgeted area which is called Home Middle #2. It is using the Genesis - Featured Page widget to display what you see on the Metric child theme demo site. To get started, log into your WordPress dashboard, and then go to the Appearance > Widgets screen. There you can drag the Genesis - Featured Page widget into the Home Middle #1 widget area on the right hand side. To get the image to display, simply upload an image through the media uploader on the edit post screen and publish your page. The Featured Page widget will know to display the post image as long as you select that option in the widget interface.", 'genesis'); ?></p>
			</div>		
			<?php endif; ?>
		</div><!-- end .home-middle-2 -->
		<div class="home-middle-3">
			<?php if (!dynamic_sidebar('Home Middle #3')) : ?>
			<div class="widget">
				<h4><?php _e("Home Middle #3 Widget", 'genesis'); ?></h4>
				<p><?php _e("This is a widgeted area which is called Home Middle #3. It is using the Genesis - Featured Page widget to display what you see on the Metric child theme demo site. To get started, log into your WordPress dashboard, and then go to the Appearance > Widgets screen. There you can drag the Genesis - Featured Page widget into the Home Middle #1 widget area on the right hand side. To get the image to display, simply upload an image through the media uploader on the edit post screen and publish your page. The Featured Page widget will know to display the post image as long as you select that option in the widget interface.", 'genesis'); ?></p>
			</div>		
			<?php endif; ?>
		</div><!-- end .home-middle-3 -->
    </div><!-- end #home-middle -->
</div><!-- end #home-middle-bg -->

<div id="home-main-content-bg">
	<div class="home-main-content">
		<?php if (!dynamic_sidebar('Home Main Content')) : ?>
		<div class="widget">
			<h4><?php _e("Home Main Content", 'genesis'); ?></h4>
			<p><?php _e("This is a widgeted area which is called Home Main Content. It is using the Genesis - Featured Page widget to display what you see on the Metric child theme demo site. To get started, log into your WordPress dashboard, and then go to the Appearance > Widgets screen. There you can drag the Genesis - Featured Page widget into the Home Top widget area on the right hand side.", 'genesis'); ?></p>
		</div>			
		<?php endif; ?>
	</div><!-- end .home-main-content -->
</div><!-- end #home-main-content-bg -->

<?php get_footer(); ?>
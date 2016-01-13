/*-----------------------------------------------------------------------------------*/
/* GENERAL SCRIPTS */
/*-----------------------------------------------------------------------------------*/
jQuery(document).ready(function($){

	// Table alt row styling
	jQuery( '.entry table tr:odd' ).addClass( 'alt-table-row' );

	// FitVids - Responsive Videos
	jQuery( '.post, .widget, .panel, .page, #main' ).fitVids();

	// Add class to parent menu items with JS until WP does this natively
	jQuery( 'ul.sub-menu, ul.children' ).parents( 'li' ).addClass( 'parent' );


	/**
	 * Navigation
	 */
	// Add the 'show-nav' class to the body when the nav toggle is clicked
	jQuery( '.nav-toggle' ).click(function(e) {

		// Prevent default behaviour
		e.preventDefault();

		// Add the 'show-nav' class
		jQuery( 'body' ).toggleClass( 'show-nav' );

		// Check if .top-navigation already exists
		if ( jQuery( '#navigation' ).find( '.top-navigation' ).size() ) return;

		// If it doesn't, clone it (so it will still appear when resizing the browser window in desktop orientation) and add it.
		jQuery( '#top .top-navigation' ).clone().appendTo( '#navigation .menus' );
	});

	// Remove the 'show-nav' class from the body when the nav-close anchor is clicked
	jQuery('.nav-close').click(function(e) {

		// Prevent default behaviour
		e.preventDefault();

		// Remove the 'show-nav' class
		jQuery( 'body' ).removeClass( 'show-nav' );
	});

	// Remove 'show-nav' class from the body when user tabs outside of #navigation on handheld devices
	var hasParent = function(el, id) {
        if (el) {
            do {
                if (el.id === id) {
                    return true;
                }
                if (el.nodeType === 9) {
                    break;
                }
            }
            while((el = el.parentNode));
        }
        return false;
    };
	if (jQuery(window).width() < 767) {
		if (jQuery('body')[0].addEventListener){
			document.addEventListener('touchstart', function(e) {
			if ( jQuery( 'body' ).hasClass( 'show-nav' ) && !hasParent( e.target, 'navigation' ) ) {
				// Prevent default behaviour
				e.preventDefault();

				// Remove the 'show-nav' class
				jQuery( 'body' ).removeClass( 'show-nav' );
			}
		}, false);
		} else if (jQuery('body')[0].attachEvent){
			document.attachEvent('ontouchstart', function(e) {
			if ( jQuery( 'body' ).hasClass( 'show-nav' ) && !hasParent( e.target, 'navigation' ) ) {
				// Prevent default behaviour
				e.preventDefault();

				// Remove the 'show-nav' class
				jQuery( 'body' ).removeClass( 'show-nav' );
			}
		});
		}
	}

	// Fix dropdowns in Android
	if ( /Android/i.test( navigator.userAgent ) && jQuery( window ).width() > 769 ) {
		jQuery( '.nav li:has(ul)' ).doubleTapToGo();
	}

	/**
	 * Full screen homepage slider on page load
	 */
	var windowHeight		= jQuery( window ).height();
	var topHeight			= jQuery( '#top' ).height();
	var headerHeight		= jQuery( '#header' ).height();
	var sliderHeight		= windowHeight - topHeight - headerHeight;

	if ( jQuery(window).width() > 768 ) {

		jQuery( '.home #featured-slider, .home .slide, .home .slide-media, .home .slide-video' ).css( 'height', sliderHeight );

		// Offset 28px for admin-bar
		jQuery( '.home.admin-bar #featured-slider, .home.admin-bar .slide, .home.admin-bar .slide-media, .home.adminbar .slide-video' ).css( 'height', sliderHeight - 28 );

	}

	// Slider
	jQuery( '.home .slide-content' ).addClass( 'animated fadeInUp' );

	// Blog posts
	jQuery( '.blog-posts-grid .post.has-image' ).hover( function() {
		jQuery( this ).children( '.post-header' ).removeClass( 'bounceOutRight' ).addClass( 'animated bounceInLeft' );
		jQuery( this ).children( '.post-more' ).removeClass( 'bounceOutRight' ).addClass( 'animated bounceInLeft' );
	}, function() {
		jQuery( this ).children( '.post-header' ).removeClass( 'bounceInLeft' ).addClass( 'bounceOutRight' );
		jQuery( this ).children( '.post-more' ).removeClass( 'bounceInLeft' ).addClass( 'bounceOutRight' );
	});

	// blog post IE8 compatibility
	jQuery( '.ie8 .blog-posts-grid .post.has-image .post-header, .ie8 .blog-posts-grid .post.has-image .post-more' ).hide();
	jQuery( '.ie8 .blog-posts-grid .post.has-image' ).hover( function() {
		jQuery( this ).find( '.post-header, .post-more' ).fadeToggle();
		jQuery( this ).find( 'img' ).animate( { "opacity" : 0.5 }, 250 );
	}, function() {
		jQuery( this ).find( 'img' ).animate( { "opacity" : 1 }, 250 );
	});

	// Products IE8 compatibility
	jQuery( '.ie8 ul.products li.product .details, .ie8 ul.products li.product .button' ).hide();
	jQuery( '.ie8 ul.products li.product' ).hover( function() {
		jQuery( this ).find( '.details, .button' ).fadeToggle();
		jQuery( this ).find( 'img' ).animate( { "opacity" : 0.5 }, 250 );
	}, function() {
		jQuery( this ).find( 'img' ).animate( { "opacity" : 1 }, 250 );
	});

});
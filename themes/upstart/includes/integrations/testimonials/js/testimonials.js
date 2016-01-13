jQuery(document).ready(function($){
	// Testimonials
	if ( jQuery( window ).width() > 769 ) {
		jQuery( '#main .widget_woothemes_testimonials .quote:first-of-type .avatar' ).addClass( 'active' );
		jQuery( '#main .widget_woothemes_testimonials .quote:first-of-type .testimonials-text' ).addClass( 'animated bounceInLeft' );

		// ( <= IE8 Compatibility )
		jQuery( '.no-csstransitions #main .widget_woothemes_testimonials .quote:first-of-type .testimonials-text' ).fadeIn();

		jQuery( '#main .widget_woothemes_testimonials .quote' ).not( ':first-of-type' ).hover( function() {
			jQuery( '#main .widget_woothemes_testimonials .quote-content .avatar' ).removeClass( 'active' );
			jQuery( '#main .widget_woothemes_testimonials .quote:first-of-type .testimonials-text' ).removeClass( 'bounceInLeft' );

			// ( <= IE8 Compatibility )
			jQuery( '.no-csstransitions #main .widget_woothemes_testimonials .quote:first-of-type .testimonials-text' ).fadeOut();
		});

		jQuery( '#main .widget_woothemes_testimonials' ).hover( function() {
			return;
		}, function() {
			jQuery( '#main .widget_woothemes_testimonials .quote:first-of-type .avatar' ).addClass( 'active' );
			jQuery( '#main .widget_woothemes_testimonials .quote:first-of-type .testimonials-text' ).removeClass( 'bounceOutLeft' ).addClass( 'bounceInLeft' );

			// ( <= IE8 Compatibility )
			jQuery( '.no-csstransitions #main .widget_woothemes_testimonials .quote:first-of-type .testimonials-text' ).fadeIn();
		});


		jQuery( '#main .widget_woothemes_testimonials .quote-content' ).hover( function() {
			jQuery( this ).children( '.testimonials-text' ).removeClass( 'bounceOutLeft' ).addClass( 'animated bounceInLeft' );
		}, function() {
			jQuery( this ).children( '.testimonials-text' ).removeClass( 'bounceInLeft' ).addClass( 'bounceOutLeft' );
		});

		// <= IE8 Compatibility
		jQuery( '.no-csstransitions #main .widget_woothemes_testimonials .quote-content' ).hover( function() {
			jQuery( this ).children( '.testimonials-text' ).fadeIn();
		}, function() {
			jQuery( this ).children( '.testimonials-text' ).fadeOut();
		});
	}
});
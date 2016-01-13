jQuery(document).ready(function($){
	jQuery( '#main .widget_woothemes_features .feature:nth-child(2n)' ).addClass( 'alt' );
	jQuery( '#main .widget_woothemes_features .feature' ).not( ':nth-child(2n)' ).addClass( 'not-alt' );
	jQuery( '#main .widget_woothemes_features .feature:last' ).addClass( 'final-feature' );

	jQuery( '.home .type-page' ).waypoint(function() {
		jQuery( '#main .widget_woothemes_features .feature.not-alt' ).addClass( 'animated bounceInLeft' );
		jQuery( '#main .widget_woothemes_features .feature.alt' ).addClass( 'animated bounceInRight' );
	}, { offset: '45%' });
});
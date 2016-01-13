jQuery(document).ready(function($){
	// Products
	jQuery( 'ul.products li.product' ).hover( function() {
		jQuery( this ).find( '.details' ).removeClass( 'bounceOutRight' ).addClass( 'animated bounceInLeft' );
		jQuery( this ).children( '.button' ).removeClass( 'bounceOutRight' ).addClass( 'animated bounceInLeft' );
	}, function() {
		jQuery( this ).find( '.details' ).removeClass( 'bounceInLeft' ).addClass( 'bounceOutRight' );
		jQuery( this ).children( '.button' ).removeClass( 'bounceInLeft' ).addClass( 'bounceOutRight' );
	});

	// WooCommerce message animation
	jQuery( '.woocommerce-message, .woocommerce-error' ).addClass( 'animated bounce' );
});
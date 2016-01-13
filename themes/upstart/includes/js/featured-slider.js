jQuery( window ).load( function() {
    if ( 'true' == woo_localized_data.slideshow ) { woo_localized_data.slideshow = true; } else { woo_localized_data.slideshow = false; }
    if ( 'true' == woo_localized_data.directionNav ) { woo_localized_data.directionNav = true; } else { woo_localized_data.directionNav = false; }
    if ( 'true' == woo_localized_data.controlNav ) { woo_localized_data.controlNav = true; } else { woo_localized_data.controlNav = false; }
    if ( 'true' == woo_localized_data.pauseOnHover ) { woo_localized_data.pauseOnHover = true; } else { woo_localized_data.pauseOnHover = false; }
    if ( 'true' == woo_localized_data.pauseOnAction ) { woo_localized_data.pauseOnAction = true; } else { woo_localized_data.pauseOnAction = false; }
    if ( 'true' == woo_localized_data.touch ) { woo_localized_data.touch = true; } else { woo_localized_data.touch = false; }

    // Cast the speed variables as floats.
    woo_localized_data.slideshowSpeed = parseFloat( woo_localized_data.slideshowSpeed );
    woo_localized_data.animationSpeed = parseFloat( woo_localized_data.animationSpeed );

    jQuery( '#featured-slider' ).flexslider({
        animation: 'fade',
        controlsContainer: woo_localized_data.controlsContainer,
        smoothHeight: false,
        directionNav: true,
        controlNav: true,
        manualControls: woo_localized_data.manualControls,
        slideshow: woo_localized_data.slideshow,
        pauseOnHover: woo_localized_data.pauseOnHover,
        slideshowSpeed: woo_localized_data.slideshowSpeed,
        animationSpeed: woo_localized_data.animationSpeed,
        touch: woo_localized_data.touch,
        pauseOnHover: woo_localized_data.pauseOnHover,
        pauseOnAction: woo_localized_data.pauseOnAction
    });
});
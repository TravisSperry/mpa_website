jQuery(document).ready(function (e) {
    jQuery("ul.products li.product").hover(function () {
		console.log(jQuery(this).find(".button").text());
      	if(jQuery(this).find(".button").text() === "Sold Out"){
      		jQuery(this).find(".button").css('color', '#ff6c00');
          jQuery(this).find(".button").css('border', '0.202em solid #ff6c00');
          jQuery(this).find(".button").on('hover', function(){
             jQuery(this).css('background-color', '#ff6c00');
             jQuery(this).css('color', 'white');
          });
      	}
        jQuery(this).find(".button").removeClass("bounceOutRight").addClass("animated bounceInLeft")
    }, function () {
        jQuery(this).find(".button").removeClass("bounceInLeft").addClass("bounceOutRight")
        if(jQuery(this).find(".button").text() === "Sold Out"){
          jQuery(this).find(".button").css('background', 'none');
        };
    });
    jQuery(".woocommerce-message, .woocommerce-error").addClass("animated bounce")
});
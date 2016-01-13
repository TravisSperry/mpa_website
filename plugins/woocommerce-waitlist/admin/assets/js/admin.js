function waitlist_isChecked(e) {

	'use strict';

	var $r;

    if ( e.attr("checked") !== undefined ) {

        $r = 1;
    }

    else {

        $r = 0;
    }

    return $r;
}

function waitlist_sender_emailAddress(){

	"use strict";

	var sel = jQuery('#woowaitilist-default-email-options');

	if( waitlist_isChecked( sel ) ){

		jQuery('.woowaitilist-custom-email').slideUp( 'fast', function(){
			jQuery(this).addClass("hidden");
		});
	}
	else{

		jQuery('.woowaitilist-custom-email').slideDown( 'fast', function(){
			jQuery(this).removeClass("hidden");
		});
	}
}

(function ( $ ) {
	
	"use strict";

	$(function () {

		waitlist_sender_emailAddress();

		jQuery('#woowaitilist-default-email-options').on('click', function(){

			waitlist_sender_emailAddress();
		});


		jQuery('.woowaitlist-settings-save').live( 'click', function(){

			var form_data = jQuery('.woowaitlist-settings-form').serialize();

			jQuery('.woowaitlist-settings-form .updated').slideUp(function(){

				jQuery(this).remove();
			});

			jQuery.ajax({
				type: "post",
				url: woowaitlist_back_ajax_object.ajax_url,
				dataType: 'json',
				data: {
					action: 'woowaitlist_save_settings',
					p: form_data
				},
				success:function(data, textStatus, XMLHttpRequest){

					if( data && data.message ){

						jQuery('.woowaitlist-settings-form').prepend( data.message );

						jQuery('html, body').animate({
					        scrollTop: jQuery("#woowaitlist-top").offset().top
					    }, 500);
					}

				},
				error:function(data, textStatus, XMLHttpRequest){

					console.log('error ajax - woowaitilist save settings');
				}
			});

			return false;
		});



	});

}(jQuery));
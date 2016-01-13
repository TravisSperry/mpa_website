function capitaliseFirstLetter( string ){
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function update_notification_field( reset ){

	"use strict";

	var pid, logged_in, d;

	pid = jQuery('.wew-id-for-notify').val();
	logged_in = jQuery('#wew-is-logged-in').val();

	if( reset ){

		jQuery('.wew-notification-action_wrapper.variations').html("");
	}
	else{

		if( logged_in ){

			d = '<button id="wew-submit-email-to-notify" class="add_to_cart_button button logged">' + wew_ajax_object.texts.subscribe + '</button>';
		}
		else{

			d = '<input type="text" name="wew-email-to-notify" class="wew-email-to-notify" placeholder="' + wew_ajax_object.texts.insert + '" />';
			d += '<button id="wew-submit-email-to-notify" class="add_to_cart_button button">' + wew_ajax_object.texts.proceed + '</button>';
			d += '<input type="hidden" class="wew-id-for-notify" value="' + pid + '" />';
		}
			
		setTimeout( function(){

			jQuery('.wew-notification-action_wrapper.variations').html( d );

		}, 100 );
	}
}

(function ( $ ) {
	
	"use strict";

	$(function () {

		var $productVariations, $oosType, $oosParent;

		$oosType = $.trim( $('#oos-type').val() );

		$oosParent = $('#oos-parentid').length ? $('#oos-parentid').val() : false;

		$('#wew-submit-email-to-notify').live('click', function(){

			var pid, uemail, mesgarea;

			if( $(this).parents('tr').length ){	// Grouped products

				pid = $(this).parents('tr').find('.wew-id-for-notify').val();
				uemail = $.trim( $(this).parents('tr').find('.wew-email-to-notify').val() );
				mesgarea = $(this).parents('tr').find('.wew-notification-action_wrapper');
			}
			else{

				pid = $.trim( $('.wew-id-for-notify').val() );
				uemail = $.trim( $('.wew-email-to-notify').val() );
				mesgarea = $('.wew-notification-action_wrapper');
			}

			jQuery.ajax({
				type: "post",
				url: wew_ajax_object.ajax_url,
				dataType: 'json',
				data: {
					action:'wew_save_to_db_callback',
					pid : pid,
					uemail : uemail,
					is_grouped: $oosType == "grouped" ? 1 : 0,
					parent_id: $oosParent,
					is_variation: $oosType == "variation" ? 1 : 0,
					variation_id: $oosType == "variation" ? parseInt( $('input[name="variation_id"]').val() ) : 0
				},
				success:function(data, textStatus, XMLHttpRequest){
					
					if( data && ( data.error === false  || ( data.error === true && parseInt( data.code ) === 3 ) ) ){
						
						mesgarea.html( '<strong>' + data.message + '</strong>' );
					}
					else{

						if( data.message !== undefined ){
							alert( data.message );
						}
					}
				},
				error:function(data, textStatus, XMLHttpRequest){

					console.log('error ajax - save wew');
				}
			});

			return false;
		});

		$('.wew-email-to-notify').keyup(function(event){

			 if( event.keyCode === 13 ){

				$('#wew-submit-email-to-notify').click();

				return false;
			}
		
		});

		if( $('.variations_form').length ){

			$productVariations = $('.variations_form').data('product_variations');

			$('input[name="variation_id"]').on('change', function(){

				var selectedVal,
					cnt, 
					cnnt = 0,
					c,
					productInStock,
					okey,
					varTit = [],
					varDTitle = "";

				selectedVal = parseInt( $(this).val() ) > 0 ? parseInt( $(this).val() ) : false ;

				if( selectedVal ){

					for( cnt = 0; cnt < $productVariations.length; cnt++ ){

						if( parseInt( $productVariations[cnt].variation_id ) === selectedVal ){
							
							productInStock = $productVariations[cnt].is_in_stock;

							if( productInStock === true && !$productVariations[cnt].backorders_allowed ){

								update_notification_field( true );
							}
							else{

								update_notification_field( false );
								
							}

						}

					}

				}
				else{

					update_notification_field( true );
				}

			});
		}

	});

}(jQuery));
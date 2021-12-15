jQuery( document ).ready(
	function () {
		// Delete address list
		jQuery( "[id^=delete_address_list_]" ).click(
			function (click) {
				var currency = jQuery( this ).attr( "id" ).replace( 'delete_address_list_', '' );

				var data = {
					'action': 'cw_delete_address_list',
					'currency': currency,
					'nonce' : ajax_data.nonce,
				};
				console.log( data );
				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				jQuery.post(
					ajaxurl,
					data,
					function (response) {
						alert( response.replace( /<(?:.|\n)*?>/gm, '' ) );
						jQuery( "#unused_addresses_" + currency ).html( '' );
					}
				);
				// Cancel the default action
				click.preventDefault();
			}
		);
	}
);

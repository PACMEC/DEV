/**
 * Updated the payment currency select field when clicking on one of the prices ont he checkout page
 */
(function ( $ ) {
	'use strict';
	$(
		function () {
			$( document.body ).on(
				'click',
				"[id^=price-]",
				function () {
					var currency = this.id.replace( 'price-', '' );
					$( '#cw_payment_currency' ).val( currency );
					$( "[id^=price-]" ).removeClass( 'selected_currency' );
					$( this ).addClass( 'selected_currency' );
				}
			);
		}
	);
}( jQuery ));

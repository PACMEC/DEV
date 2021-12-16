jQuery(document).ready(function ( $ ) {

	// Check order status response
	function cwCheckResponse(response) {

		if (response.received == null) {
			response.received = 0.00;
		}

		if (response.unconfirmed == null) {
			response.unconfirmed = 0.00;
		} else if (response.unconfirmed > 0) {
			jQuery('#amount-incoming').removeClass('cw-hidden');
			jQuery('#countdown-timer').hide();
			jQuery('#progress').hide();
		}

		if (response.redirect) {
			window.location.href = response.redirect;
		}
		jQuery( '#cw-confirmed' ).replaceWith( '<div id="cw-confirmed">'+response.received+'</div>' );
		jQuery( '#cw-unconfirmed' ).replaceWith( '<div id="cw-unconfirmed">'+response.unconfirmed+'</div>' );
	}

	// Customer clicked the "Check payment status button"
	jQuery("#check").click(function( c ) {
		c.preventDefault();
		setTimeout(function(){
							jQuery( '#cw-loading' ).addClass( 'fa-spin' );
		}, 250);
		setTimeout(function(){
							jQuery( '#cw-loading' ).removeClass( 'fa-spin' );
		}, 2000);

			var vars    = {
				'action': 'check_receipt',
				'order_key': CryptoPay.order_key,
				'wp_nonce': CryptoPay.wp_nonce
		};
			var ajaxurl = CryptoPay.admin_url;
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: vars,
					dataType: 'json',
					success: function(response) {
						cwCheckResponse(response);
					}
				});
	});

	// Poll payment info from database
	(function poll() {
		setTimeout(function () {
			jQuery('#cw-loading').addClass( 'fa-spin' );
		}, 14000);
		setTimeout(function () {
			var vars    = {
				'action': 'check_receipt',
				'order_key': CryptoPay.order_key,
				'wp_nonce': CryptoPay.wp_nonce
			};
			var ajaxurl = CryptoPay.admin_url;
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: vars,
				dataType: 'json',
				success: function (response) {
					cwCheckResponse(response);
					//Setup the next poll recursively
					poll();
					jQuery('#cw-loading').removeClass( 'fa-spin' );
				}
			});
		}, 16500);
	})();

	// Our countdown plugin takes a duration, and an optional message
	jQuery.fn.cw_countdown = function (duration, message) {

		// Maybe hide countdown and progress bar
		if (CryptoPay.show_countdown == 0) {
			jQuery('#countdown-timer').hide();
			jQuery('#progress').hide();
		}

		// Progress bar
		var options = {
			bg: '#666',
			// leave target blank for global nanobar
			target: document.getElementById('progress'),
			// id for new nanobar
			id: 'timeoutBar'
		};
		var nanobar = new Nanobar(options);

		// If no message is provided, we use an empty string
		message = message || "";
		// Get reference to container, and set initial content
		var container = jQuery(this[0]).html(duration + message);
		// Get reference to the interval doing the countdown
		var countdown = setInterval(function () {
			// If seconds remain
			if (--duration) {
				// Update our container's message
				if (duration < 2) {
					clearInterval(countdown);
					setTimeout(function () {
						container.html("<span class='cryptopay-warning'>" + CryptoPay.please_wait + " <i class='fa fa-refresh fa-spin'></i>");
					}, 3000);
					// Wait 3 seconds to make sure the order has really timed out, then force processing of orders in database
					var vars    = {
						'action': 'cw_force_processing',
						'wp_nonce': CryptoPay.wp_nonce
					};
					var ajaxurl = CryptoPay.admin_url;
					jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: vars,
						dataType: 'json',
						success: function () {
							window.location.href = CryptoPay.redirect;
						}
					});
				} else {
					// Calculate percentage
					var progress = (duration / CryptoPay.total_time) * 100; //
					// Update progressbar with percentage
					nanobar.go(progress);
					// Update countdown time
					container.html(secondsTimeSpanToHMS(duration));
				}
				// Otherwise
			} else {
				// Clear the countdown interval
				clearInterval(countdown);
			}
			// Run interval every 1000ms (1 second)
		}, 1000);
	};

	// Format seconds to hh:mm:ss
	function secondsTimeSpanToHMS(s) {
		var h = Math.floor(s/3600); //Get whole hours
		s    -= h*3600;
		var m = Math.floor(s/60); //Get remaining minutes
		s    -= m*60;
		return h+":"+(m < 10 ? '0'+m : m)+":"+(s < 10 ? '0'+s : s); //zero padding on minutes and seconds
	}

	// Use .cw-countdown as container, duration, and optional message
	jQuery(".cw-countdown").cw_countdown(CryptoPay.time_left, '');

	// Create QR code

	//If a plugin exists with the javascript function-name for conversion logic, use that QR-code instead of this.
	if (typeof window["change_addr_" + CryptoPay.currency] !== "function") {
		var qrcode = new QRCode(document.getElementById("qrcode"), {
			text: CryptoPay.qr_data,
			width: 250,
			height: 250,
			colorDark : "#000000",
			colorLight : "#ffffff",
			correctLevel : QRCode.CorrectLevel.M
		});
	}

	// TrezorConnect is injected as inline script in html
	// therefore it doesn't need to included into node_modules
	// get reference straight from window object
	if ( typeof window.TrezorConnect !== 'undefined') {
		const TrezorConnect = window.TrezorConnect;

		console.log('TrezorConnect detected');

		// Listen to DEVICE_EVENT
		// this event will be emitted only after user grants permission to communicate with this app
		TrezorConnect.on('DEVICE_EVENT', event => {
			console.log(event);
		});

		// Initialize TrezorConnect
		TrezorConnect.init({
			webusb: true, // webusb is not supported in electron
			debug: false, // see whats going on inside iframe
			lazyLoad: true, // set to "false" (default) if you want to start communication with bridge on application start (and detect connected device right away)
			// set it to "true", then trezor-connect will not be initialized until you call some TrezorConnect.method()
			// this is useful when you don't know if you are dealing with Trezor user
			manifest: {
				email: 'email@developer.com',
				appUrl: 'electron-app-boilerplate',
			},
		}).then(() => {
			console.log('TrezorConnect is ready!');
			jQuery('#pay-with-trezor').show();
		}).catch(error => {
			console.log('TrezorConnect init error: ' + error);
		});

		function send_from_trezor() {
			TrezorConnect.composeTransaction({
				outputs: [
					{amount: CryptoPay.crypto_amount, address: CryptoPay.payment_address}
				],
				coin: CryptoPay.lc_currency,
				push: true
			}).then(function(response) {
				console.log(response);
			}).catch(error => {
				console.log('TrezorConnect error: ' + error);
			});
		}
	}
	jQuery("[id^=cwhd-connect-trezor-]").click(function( c ) {
		c.preventDefault();
		console.log('Paying with Trezor');
		send_from_trezor();
	});

});

// Select full content on payment page
function selectText(containerid) {
	if (document.selection) {
		var range = document.body.createTextRange();
		range.moveToElementText(document.getElementById(containerid));
		range.select();
	} else if (window.getSelection) {
		var range = document.createRange();
		range.selectNode(document.getElementById(containerid));
		window.getSelection().removeAllRanges();
		window.getSelection().addRange(range);
	}
	jQuery('#addr_img').removeClass('cw-hidden');
}

document.querySelector("#copy_address_a").addEventListener("click", function(event) {
  copyToClipboard('#payment-address')
  jQuery('#copy_success').fadeIn(200).fadeOut(200);
  selectText('payment-address');
  event.preventDefault();
}, false);

function copyToClipboard(element) {
  var temp = jQuery('<input>');
  jQuery('body').append(temp);
  temp.val(jQuery(element).text()).select();
  document.execCommand('copy');
  temp.remove();
}

function switchPaymentAddress(){
	var functionName = "change_addr_" + CryptoPay.currency;
	if (typeof window[functionName] === "function") {
		window["change_addr_" + CryptoPay.currency]();
	}

}



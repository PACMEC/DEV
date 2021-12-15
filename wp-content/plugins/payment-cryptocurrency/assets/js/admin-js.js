jQuery(document).ready(function () {

	// Exchange rate updates
	jQuery("#update-exchange-rates").click(function(click) {
		jQuery( '#cw-rates-loading' ).replaceWith( '<div id="cw-rates-loading"><i class="fa fa-2x fa-refresh fa-spin"></i> Getting exchange rates via API...</div>' );
		var data = {
			'action': 'cw_update_exchange_rates'
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {
			jQuery( '#cw-rates-loading' ).replaceWith( '<div id="cw-rates-loading"></div>' );
			jQuery( '#cw-rates-response' ).replaceWith( '<div id="cw-rates-response">'+ response+'</div>' );
			//alert('Got this from the server: ' + response);
		});
		// Cancel the default action
		click.preventDefault();
	});
	// Reset error counter
	jQuery("#reset-error-counter").click(function(click) {
		var data = {
			'action': 'cw_reset_error_counter'
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {
			jQuery( '#reset-error-counter-response' ).replaceWith( '<p><div id="reset-error-counter-response">'+ response+'</div></p>' );
		});
		// Cancel the default action
		click.preventDefault();
	});
	// Reset exchange rate table
	jQuery("#reset-exchange-rate-table").click(function(click) {
		var data = {
			'action': 'cw_reset_exchange_rate_table'
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {
			jQuery( '#reset-table-response' ).replaceWith( '<p><div id="reset-table-response">'+ response+'</div></p>' );
		});
		// Cancel the default action
		click.preventDefault();
	});
	// Reset payments table
	jQuery("#reset-payments-table").click(function(click) {
		var data = {
			'action': 'cw_reset_payments_table'
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {
			jQuery( '#reset-table-response' ).replaceWith( '<p><div id="reset-table-response">'+ response+'</div></p>' );
		});
		// Cancel the default action
		click.preventDefault();
	});

	// Update TX details
	jQuery("#update-tx-details").click(function(click) {
		var vars = {
			'action': 'cw_update_tx_details'
		};
		//alert('vars: '+vars);
		var ajaxurl = CryptoWooAdmin.admin_url;
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: vars,
			dataType: 'json',
			success: function (response) {
				//console.log(response);
				if (response.info) {
					var content = response.info;
				} else {
					var content = JSON.stringify(response, null, 2);
				}
				var find    = ['{', '}', '"', '_'];
				var replace = ['', '', '', ' '];
				alert(content.replaceArray(find, replace));
				jQuery('#cw-processing-response').replaceWith('<p><div id="cw-processing-response" class="notice notice-info"><ul><li>' + content + '</li></ul></div></p>');
			}
		});
		// Cancel the default action
		click.preventDefault();
	});

	// Beautify JSON response
	String.prototype.replaceArray = function(find, replace) {
		var replaceString = this;
		var regex;
		for (var i = 0; i < find.length; i++) {
			regex         = new RegExp(find[i], "g");
			replaceString = replaceString.replace(regex, replace[i]);
		}
		return replaceString;
	};

	// Process open orders
	jQuery("#process-open-orders").click(function(click) {
		var vars = {
			'action': 'cw_process_open_orders',
			'admin' : true
		};
		//alert('vars: '+vars);
		var ajaxurl = CryptoWooAdmin.admin_url;
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: vars,
			dataType: 'json',
			success: function (response) {

				//alert('Payments processed. Result: '+JSON.stringify(response, null, 2));
				//console.log(response);
				if (response == 'No unpaid addresses found') {
					var addresses       = response;
					var paymentRequests = '';
					alert(response);
				} else {
					// Beautify output TODO there are better ways to do this
					var find         = ['{', '}', '"', '_'];
					var replace      = ['', '', '', ' '];
					var jsonResponse = JSON.stringify(response, null, 2);
					alert('Payments processed: '+jsonResponse.replaceArray(find, replace));

					if (response.unpaid_addresses) {
						var addresses       = '<li>Unpaid addresses: '+response.unpaid_addresses+'</li>';
						var paymentRequests = '<li>Processing Data: <br>'+JSON.stringify(response.payment_requests, null, 2)+'</li>';
					} else {
						var addresses       =  '<li>'+JSON.stringify(response, null, 2)+ '</li>';
						var paymentRequests = '';
					}
				}
				jQuery('#cw-processing-response').replaceWith('<p><div id="cw-processing-response" class="notice notice-info"><ul>'+addresses+paymentRequests+'</ul></div></p>');
			}
		});
		// Cancel the default action
		click.preventDefault();
	});
});

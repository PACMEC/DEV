(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	 $(document).ready(function() {

		const MDCText = mdc.textField.MDCTextField;
        const textField = [].map.call(document.querySelectorAll('.mdc-text-field'), function(el) {
            return new MDCText(el);
        });
        const MDCRipple = mdc.ripple.MDCRipple;
        const buttonRipple = [].map.call(document.querySelectorAll('.mdc-button'), function(el) {
            return new MDCRipple(el);
        });
        const MDCSwitch = mdc.switchControl.MDCSwitch;
        const switchControl = [].map.call(document.querySelectorAll('.mdc-switch'), function(el) {
            return new MDCSwitch(el);
        });

        $(document).on('click', '.mwb-password-hidden', function() {
            if ($('.mwb-form__password').attr('type') == 'text') {
                $('.mwb-form__password').attr('type', 'password');
            } else {
                $('.mwb-form__password').attr('type', 'text');
            }
        });

		//remove notices.
		$(document).on('click', '.notice-dismiss', function(){
			$(this).parent().remove();
		});


		$(document).find('.mwb-pfw-colorpicker').wpColorPicker();

		$(document).on('click', '#mwb_pfw_header_logo_upload', function (e) {
			 e.preventDefault();
			 var image = wp.media({
				 title: 'Upload Image',
				 // mutiple: false can't upload multiple files at once.
				 multiple: false
			 }).open()
				 .on('select', function (e) {
					 // It will return the selected image from the Media Uploader, the result is an object
					 var uploaded_image = image.state().get('selection').first();
					 var image_url = uploaded_image.toJSON().url;
					 // Assign the url value to the input field
					 $(document).find('#mwb_pfw_header_logo').val(image_url);
					 $(document).find('#mwb_pfw_header_logo').prev().attr('src', image_url);
				 });
		});
		 
		 $(document).on('click', '#mwb_pfw_login_logo_upload', function (e) {
			 e.preventDefault();
			 var image = wp.media({
				 title: 'Upload Image',
				 // mutiple: false can't upload multiple files at once.
				 multiple: false
			 }).open()
				 .on('select', function (e) {
					 // It will return the selected image from the Media Uploader, the result is an object
					 var uploaded_image = image.state().get('selection').first();
					 var image_url = uploaded_image.toJSON().url;
					 // Assign the url value to the input field
					 $(document).find('#mwb_pfw_login_logo').val(image_url);
					 $(document).find('#mwb_pfw_login_logo').prev().attr('src', image_url);
				 });
		 });
		// Generate product barcode.
		 var mwb_pfw_product_barcode = null;
		 $(document).on('click', '#mwb_pfw_barcode_generate', function(){
			 mwb_pfw_product_barcode = $.ajax({
				 url: pfw_admin_param.ajaxurl,
				type : 'POST',
				cache : false,
				data : {
					action : 'mwb_pfw_generate_pro_barcode',
					security: pfw_admin_param.mwb_pfw_nonce,
				},success : function(params) {
					console.log(params);
					if( params == 'success' ){
						jQuery(document).find('.mwb-pos-notify').css('display', 'flex', 'important');
					}
				},beforeSend : function(){
					if ( mwb_pfw_product_barcode != null ) {
						mwb_pfw_product_barcode.abort();
					}
				}
			 });
		 });
	});

	$(window).load(function(){
		// add select2 for multiselect.
		if( $(document).find('.mwb-defaut-multiselect').length > 0 ) {
			$(document).find('.mwb-defaut-multiselect').select2();
		}
	});

	})( jQuery );

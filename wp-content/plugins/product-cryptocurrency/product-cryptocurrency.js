function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_select_cryptocurrency_option_change_hide_all() {
    jQuery('._text_input_cryptocurrency_balance_field').addClass('hidden').addClass('hide-all');

    jQuery('._text_input_cryptocurrency_minimum_value_field').addClass('hidden').addClass('hide-all');
//    jQuery('#_text_input_cryptocurrency_minimum_value').attr('type', 'hidden');
    jQuery('._text_input_cryptocurrency_step_field').addClass('hidden').addClass('hide-all');
//    jQuery('#_text_input_cryptocurrency_step').attr('type', 'hidden');
}

function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_select_cryptocurrency_option_change(cb) {
    if ('function' !== typeof cb) {
        cb = function () {};
    }
    var v = jQuery( '#_select_cryptocurrency_option' ).val();
    if ('Ether' === v) {
        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_select_cryptocurrency_option_change_hide_all();
        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_showEtherBalance(cb);
        jQuery('._text_input_cryptocurrency_minimum_value_field').removeClass('hidden').removeClass('hide-all');
//        jQuery('#_text_input_cryptocurrency_minimum_value').attr('type', 'number');
        jQuery('._text_input_cryptocurrency_step_field').removeClass('hidden').removeClass('hide-all');
//        jQuery('#_text_input_cryptocurrency_step').attr('type', 'number');
    } else if ('' === v) {
        CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_select_cryptocurrency_option_change_hide_all();
    }
}

function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_select_cryptocurrency_product_type() {
    var is_cryptocurrency = jQuery( '#_cryptocurrency_product_for_woocommerce_cryptocurrency_product_type' ).prop('checked');
    if (is_cryptocurrency) {
        jQuery( '.cryptocurrency_options' ).show();
        jQuery( '.cryptocurrency-product-for-woocommerce-settings-wrapper' ).show();
        jQuery( '.show_if_cryptocurrency_product_for_woocommerce_cryptocurrency_product_type' ).show();
    } else {
        jQuery( '.cryptocurrency_options' ).hide();
        jQuery( '.general_options > a' ).trigger('click');
        jQuery( 'li > a.general' ).trigger('click');
        jQuery( '.cryptocurrency-product-for-woocommerce-settings-wrapper' ).hide();
        jQuery( '.show_if_cryptocurrency_product_for_woocommerce_cryptocurrency_product_type' ).hide();
    }
}

function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_showEtherBalance(cb) {
    if ('function' !== typeof cb) {
        cb = function () {};
    }
    var v = jQuery( '#_select_cryptocurrency_option' ).val();
    if ('Ether' !== v) {
        cb.call(null, null, null);
        return;
    }
    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_ether_balance_by_account(window.cryptocurrency.walletAddress, function(err, balanceWei) {
        if (err) {
            console.log(err); 
            cb.call(null, err, null);
            return;
        }
        var balance = (new BigNumber(balanceWei)).div((new BigNumber(10)).pow(18));
        console.log("Ether balance: ", balance.toNumber());
        jQuery('#_text_input_cryptocurrency_balance').val(balance.toNumber());
        jQuery('._text_input_cryptocurrency_balance_field').removeClass('hidden').removeClass('hide-all');
        cb.call(null, null, balanceWei);
    });
}

function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_get_ether_balance_by_account(account, callback) {
	window.cryptocurrency.web3.eth.getBalance(account, callback);
}

function CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_init() {
	if ("undefined" !== typeof window.cryptocurrency && window.cryptocurrency.initialized === true) {
        return;
    }
	if ("undefined" !== typeof window.cryptocurrency.web3Endpoint) {
        var url = new window.URL(window.cryptocurrency.web3Endpoint);
        var options = {
            withCredentials: false
        };
        var auth = url.username + ":" + url.password;
        var web3Endpoint = window.cryptocurrency.web3Endpoint;
        if (auth !== ":") {
            options.headers = [{
                name: "authorization", 
                value: 'Basic ' + (btoa ? btoa(auth) : auth)
            }];
            web3Endpoint = url.origin;
        }
        window.cryptocurrency.web3 = new Web3(new Web3.providers.HttpProvider(web3Endpoint, options));
        // @see https://github.com/INFURA/infura/issues/189#issuecomment-535937835
        window.cryptocurrency.web3.eth.defaultAccount = "0x0000000000000000000000000000000000000000";
	}
    window.cryptocurrency.initialized = true;
}

jQuery(document).ready(CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_init);
// proper init if loaded by ajax
jQuery(document).ajaxComplete(function( event, xhr, settings ) {
    CRYPTOCURRENCY_PRODUCT_FOR_WOOCOMMERCE_init();
});

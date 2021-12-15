document.addEventListener("DOMContentLoaded", function(event) {
    jQuery(document.body).on('updated_wc_div', pwgc_bind_redeem_form);
    jQuery(document.body).on('updated_shipping_method', pwgc_bind_redeem_form);
    pwgc_bind_redeem_form();

    jQuery(document.body).on('updated_wc_div', pwgc_bind_remove_link);
    jQuery(document.body).on('updated_checkout', pwgc_bind_remove_link);
    jQuery(document.body).on('updated_shipping_method', pwgc_bind_remove_link);
    pwgc_bind_remove_link();

    jQuery('#pwgc-message').on('input propertychange', function() {
        pwgc_message_characters_remaining();
    });
    pwgc_message_characters_remaining();

    jQuery('form.variations_form').on('show_variation', function() {
        jQuery('#pwgc-purchase-container').show();
        pwgc_toggle_quantity();
    });

    jQuery('form.variations_form').on('hide_variation', function() {
        jQuery('#pwgc-purchase-container').hide();
        pwgc_toggle_quantity();
    });

    jQuery('#pwgc-to').on('blur', function() {
        var recipients = jQuery(this).val();
        if (recipients) {
            // For clarity, ensure we do a comma followed by a space.
            // Babel translation of this line:
            // jQuery(this).val(recipients.trim().split(/[ ,]+/).map(item=>item.trim()).join(', '));

            // This is compatible with IE11
            jQuery(this).val(recipients.trim().split(/[ ,]+/).map(function (item) {
                return item.trim();
            }).join(', '));
        }

        pwgc_toggle_quantity();
    });

    jQuery('.variations_form').on( 'found_variation.wc-variation-form', function() {
        pwgc_toggle_quantity();
    });

    jQuery('.variations_form').on( 'reset_data', function() {
        pwgc_toggle_quantity();
    });

    jQuery('.variations_form').on( 'submit', function(e) {
        if (jQuery('#pwgc-to').length) {
            var recipients = jQuery('#pwgc-to').val().split(/[ ,]+/);
            var badRecipients = [];

            for (var i = 0; i < recipients.length; i++) {
                if (!pwgc_is_email(recipients[i])) {
                    badRecipients.push(recipients[i]);
                }
            }

            if (badRecipients.length) {
                alert(pwgc.i18n.invalid_recipient_error + '\n\n' + badRecipients.join('\n'));
                e.preventDefault();
                return false;
            }
        }
    });

    jQuery('.show-pw-gift-card').off('click.pwgc').on('click.pwgc', function(e) {
        jQuery('.checkout_pw_gift_card').slideToggle(400, function() {
            jQuery('.checkout_pw_gift_card').find(':input:eq(0)').focus();
        });

        e.preventDefault();
        return false;
    });

    jQuery('#pwgc-apply-gift-card-checkout').off('click.pwgc').on('click.pwgc', function(e) {
        pwgc_checkout_redeem_gift_card(jQuery(this));
        e.preventDefault();
        return false;
    });

    jQuery('#pwgc-redeem-gift-card-number').off('keypress.pwgc').on('keypress.pwgc', function(e) {
        if (e.keyCode == 13) {
            pwgc_checkout_redeem_gift_card(jQuery('#pwgc-apply-gift-card-checkout'));

            e.preventDefault();
            return false;
        }
    });
});

function pwgc_is_email(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}

function pwgc_toggle_quantity() {
    if (jQuery('#pwgc-to').length) {
        var recipients = jQuery('#pwgc-to').val().split(/[ ,]+/);
        if (recipients.length > 1) {
            jQuery('#pwgc-recipient-count').text(recipients.length);
            jQuery('#pwgc-quantity-one-per-recipient').show();
            jQuery('input.qty').val('1');
            jQuery('.quantity').hide();
        } else {
            jQuery('#pwgc-quantity-one-per-recipient').hide();
            jQuery('.quantity').show();
        }
    }
}

function pwgc_bind_remove_link() {
    jQuery('.pwgc-remove-card').off('click.pwgc').on('click.pwgc', function(e) {
        var cardNumber = jQuery(this).attr('data-card-number');
        var checkoutContainer = jQuery(this).parents( '.woocommerce-checkout-review-order' );

        if (checkoutContainer.length) {
            checkoutContainer.addClass( 'processing' ).block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });
        }

        jQuery.post(pwgc.ajaxurl, {'action': 'pw-gift-cards-remove', 'card_number': cardNumber, 'security': pwgc.nonces.remove_card}, function( result ) {
            if (checkoutContainer.length) {
                jQuery( '.woocommerce-error, .woocommerce-message' ).remove();
                checkoutContainer.removeClass( 'processing' ).unblock();
                jQuery( document.body ).trigger( 'update_checkout', { update_shipping_method: false } );
            } else {
                jQuery( document.body ).trigger( 'wc_update_cart' );
            }
        }).fail(function(xhr, textStatus, errorThrown) {
            if (errorThrown) {
                console.log(errorThrown);
            } else {
                console.log('Unknown Error');
            }
        });

        e.preventDefault();
        return false;
    });
}

function pwgc_bind_redeem_form() {
    jQuery('#pwgc-redeem-form').off('submit.pwgc').on('submit.pwgc', function(e) {
        var redeemButton = jQuery('#pwgc-redeem-button');

        pwgc_redeem_gift_card(redeemButton);

        e.preventDefault();
        return false;
    });

    jQuery('#pwgc-apply-gift-card,#pwgc-redeem-button').off('click.pwgc').on('click.pwgc', function(e) {
        pwgc_redeem_gift_card(jQuery(this));

        e.preventDefault();
        return false;
    });

    jQuery('#pwgc-redeem-gift-card-number').off('keypress.pwgc').on('keypress.pwgc', function(e) {
        if (e.keyCode == 13) {
            pwgc_redeem_gift_card(jQuery('#pwgc-apply-gift-card'));

            e.preventDefault();
            return false;
        }
    });
}

function pwgc_redeem_gift_card(redeemButton) {
    var cardNumber = jQuery('#pwgc-redeem-gift-card-number');
    var errorContainer = jQuery('#pwgc-redeem-error');

    errorContainer.text('');
    redeemButton.attr('data-apply-text', redeemButton.attr('value')).attr('value', redeemButton.attr('data-wait-text')).prop('disabled', true);

    jQuery.post(pwgc.ajaxurl, {'action': 'pw-gift-cards-redeem', 'card_number': cardNumber.val(), 'security': pwgc.nonces.apply_gift_card}, function( result ) {
        if (result.success) {
            jQuery( document.body ).trigger( 'wc_update_cart' );
        } else {
            errorContainer.text(result.data.message);
            redeemButton.attr('value', redeemButton.attr('data-apply-text')).prop('disabled', false);
            cardNumber.focus();
        }
    }).fail(function(xhr, textStatus, errorThrown) {
        if (errorThrown) {
            errorContainer.text(errorThrown);
        } else {
            errorContainer.text('Unknown Error');
        }
        redeemButton.attr('value', redeemButton.attr('data-apply-text')).prop('disabled', false);
        cardNumber.focus();
    });
}

function pwgc_message_characters_remaining() {
    var charsRemaining = pwgc.max_message_characters;

    var messageElement = jQuery('#pwgc-message').val();
    if (messageElement) {
        charsRemaining -= messageElement.length;
    }

    jQuery('#pwgc-message-characters-remaining').text(charsRemaining);
}

function pwgc_checkout_redeem_gift_card(redeemButton) {
    var errorContainer = jQuery('#pwgc-redeem-error');
    var cardNumber = jQuery('#pwgc-redeem-gift-card-number');

    errorContainer.text('');
    redeemButton.attr('data-apply-text', redeemButton.attr('value')).attr('value', redeemButton.attr('data-wait-text')).prop('disabled', true);

    jQuery.post(pwgc.ajaxurl, {'action': 'pw-gift-cards-redeem', 'card_number': cardNumber.val(), 'security': pwgc.nonces.apply_gift_card}, function( result ) {
        var form = jQuery('.checkout_pw_gift_card');
        if (result.success) {
            form.slideUp();
            cardNumber.val('');
            jQuery( document.body ).trigger( 'update_checkout', { update_shipping_method: false } );
        } else {
            errorContainer.text(result.data.message);
            cardNumber.focus();
        }
        redeemButton.attr('value', redeemButton.attr('data-apply-text')).prop('disabled', false);
    }).fail(function(xhr, textStatus, errorThrown) {
        if (errorThrown) {
            errorContainer.text(errorThrown);
        } else {
            errorContainer.text('Unknown Error');
        }
        redeemButton.attr('value', redeemButton.attr('data-apply-text')).prop('disabled', false);
        cardNumber.focus();
    });
}

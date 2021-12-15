jQuery(function() {
    var addButton = jQuery('<div id="pwgc-add-amount-button" class="button button-primary">' + pwgc.i18n.add + '</div>');
    addButton.click(pwgc_add_amount);

    jQuery('#pwgc_new_amount').after(addButton);

    jQuery('#pwgc_new_amount').on('keypress', function (e) {
        if (e.keyCode == 13) {
            jQuery('#pwgc-add-amount-button').click();
            e.preventDefault();
            return false;
        }
    });

    jQuery('.pwgc-remove-amount-button').on('click', function() {
        pwgc_remove_amount(jQuery(this));
    });
});

function pwgc_add_amount() {
    var newAmount = jQuery('#pwgc_new_amount');
    if (newAmount.val()) {

        jQuery('#pwgc-add-amount-button').text(pwgc.i18n.wait + '...');

        jQuery.post(ajaxurl, {'action': 'pw-gift-cards-add_gift_card_amount', 'product_id': jQuery('#post_ID').val(), 'amount': newAmount.val(), 'security': pwgc.nonces.add_gift_card_amount}, function( result ) {
            if (result.success) {
                var prettyAmount = result.data.amount;
                var amountContainer = jQuery('#pwgc-amount-container-template').clone().removeAttr('id').removeClass('pwgc-hidden');
                amountContainer.find('.pwgc-amount').text(prettyAmount);
                amountContainer.attr('data-variation_id', result.data.variation_id);
                amountContainer.find('.pwgc-remove-amount-button').click(function() {
                    pwgc_remove_amount(jQuery(this));
                });
                newAmount.val('').focus();
                jQuery('#pwgc-amounts-container').append(amountContainer);

                jQuery('#pwgc-add-amount-button').text(pwgc.i18n.add);

            } else {
                jQuery('#pwgc-add-amount-button').text(pwgc.i18n.add);
                alert(result.data.message);
                newAmount.focus();
            }

        }).fail(function(xhr, textStatus, errorThrown) {
            if (errorThrown) {
                alert(pwgc.i18n.error + ': ' + errorThrown + '\n\n pw-gift-cards-add_gift_card_amount');
                jQuery('#pwgc-add-amount-button').text(pwgc.i18n.add);
            }
        });
    }
}

function pwgc_remove_amount(element) {
    var amountContainer = jQuery(element).closest('.pwgc-amount-container');
    var amount = amountContainer.find('.pwgc-amount').text();

    if (confirm(pwgc.i18n.remove + ' ' + amount + '?')) {
        amountContainer.fadeOut(400, function() {
            var productId = jQuery('#post_ID').val();
            var variationId = amountContainer.attr('data-variation_id');

            jQuery.post(ajaxurl, {'action': 'pw-gift-cards-remove_gift_card_amount', 'product_id': productId, 'variation_id': variationId, 'security': pwgc.nonces.remove_gift_card_amount}, function( result ) {
                if (result.success) {
                    amountContainer.remove();
                } else {
                    amountContainer.show();
                    alert(result.data.message);
                }

            }).fail(function(xhr, textStatus, errorThrown) {
                if (errorThrown) {
                    amountContainer.show();
                    alert(pwgc.i18n.error + ': ' + errorThrown + '\n\n pw-gift-cards-remove_gift_card_amount');
                }
            });
        });
    }
}

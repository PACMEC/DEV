jQuery(function() {

    pwgcAdminBalanceSearch();

    jQuery('#pwgc-balance-search-form').on('submit', function(e) {
        pwgcAdminBalanceSearch();

        e.preventDefault();
        return false;
    });

    if (jQuery('#pwgc-balance-search').val()) {
        pwgcAdminBalanceSearch();
    }

    jQuery('#pwgc-save-settings-form').on('submit', function(e) {
        var messageContainer = jQuery('#pwgc-save-settings-message');
        var saveButton = jQuery('#pwgc-save-settings-button');
        var form = jQuery('#pwgc-save-settings-form').serialize();

        saveButton.hide();
        messageContainer.html('<i class="fas fa-cog fa-spin fa-3x"></i>');

        jQuery.post(ajaxurl, {'action': 'pw-gift-cards-save_settings', 'form': form, 'security': pwgc.nonces.save_settings }, function(result) {
            saveButton.show();
            messageContainer.html(result.data.html);
        }).fail(function(xhr, textStatus, errorThrown) {
            saveButton.show();
            if (errorThrown) {
                messageContainer.html(errorThrown);
            } else {
                messageContainer.text('Unknown ajax error');
            }
        });

        e.preventDefault();
        return false;
    });

    jQuery('#pwgc-setup-create-product').click(function(e) {
        var button = jQuery(this);
        button.html('<i class="fas fa-cog fa-spin"></i>');

        jQuery.post(ajaxurl, {'action': 'pw-gift-cards-create_product', 'security': pwgc.nonces.create_product }, function(result) {
            if (result.success) {
                button.hide();
                jQuery('#pwgc-setup-create-product-success').show();
            } else {
                button.text(button.attr('data-text'));
                jQuery('#pwgc-setup-error').text('Unknown error');
            }
        }).fail(function(xhr, textStatus, errorThrown) {
            button.text(button.attr('data-text'));
            if (errorThrown) {
                jQuery('#pwgc-setup-error').text(errorThrown);
            } else {
                jQuery('#pwgc-setup-error').text('Unknown ajax error');
            }
        });

        e.preventDefault();
        return false;
    });

    jQuery('.pwgc-dashboard-item').click(function(e) {
        jQuery('.pwgc-dashboard-item').removeClass('pwgc-dashboard-item-selected');
        jQuery(this).addClass('pwgc-dashboard-item-selected');
        var section = jQuery(this).attr('data-section');
        jQuery('.pwgc-section').hide();
        jQuery('#pwgc-section-' + section).show();
    });
});

var pwgcPickrOptions = {
    theme: 'nano',

    swatches: [
        'rgba(244, 67, 54, 1)',
        'rgba(233, 30, 99, 0.95)',
        'rgba(156, 39, 176, 0.9)',
        'rgba(103, 58, 183, 0.85)',
        'rgba(63, 81, 181, 0.8)',
        'rgba(33, 150, 243, 0.75)',
        'rgba(3, 169, 244, 0.7)',
        'rgba(0, 188, 212, 0.7)',
        'rgba(0, 150, 136, 0.75)',
        'rgba(76, 175, 80, 0.8)',
        'rgba(139, 195, 74, 0.85)',
        'rgba(205, 220, 57, 0.9)',
        'rgba(255, 235, 59, 0.95)',
        'rgba(255, 193, 7, 1)'
    ],

    useAsButton: true,
    defaultRepresentation: 'HEX',

    components: {

        // Main components
        preview: true,
        opacity: true,
        hue: true,

        // Input / output Options
        interaction: {
            hex: false,
            rgba: false,
            hsla: false,
            hsva: false,
            cmyk: false,
            input: true,
            clear: false,
            cancel: true,
            save: true
        }
    }
};

function pwgcAssignColorPicker(formElement, designerElement, designerCssAttribute) {
    pwgcPickrOptions.el = document.querySelector(formElement);
    pwgcPickrOptions.default = pwgcPickrOptions.el.value;

    const giftCardColorPickr = Pickr.create(pwgcPickrOptions);
    giftCardColorPickr.on('save', (color, instance) => {
        instance.hide();
    }).on('change', (color, instance) => {
        jQuery(designerElement).css(designerCssAttribute, color.toRGBA() );
        jQuery(instance.options.el).val(color.toHEXA().toString(0));
        jQuery(instance.options.el).css('background-color', color.toHEXA().toString(0));
        jQuery(instance.options.el).css('color', color.toHEXA().toString(0));
        instance.applyColor(true);
    }).on('cancel', instance => {
        jQuery(designerElement).css(designerCssAttribute, instance.getSelectedColor().toRGBA() );
        instance.hide();
    });
}

function pwgcAdminLoadBalanceSummary() {
    var balanceSummary = jQuery('#pwgc-balance-summary-container');

    jQuery.post(ajaxurl, {'action': 'pw-gift-cards-balance_summary', 'security': pwgc.nonces.balance_summary}, function(result) {
        balanceSummary.html(result);
    }).fail(function(xhr, textStatus, errorThrown) {
        if (errorThrown) {
            balanceSummary.html(errorThrown);
        } else {
            balanceSummary.html('Unknown Error');
        }
    });
}

function pwgcAdminBalanceSearch() {
    jQuery('#pwgc-balance-search-results,#pwgc-balance-card-activity').text('');
    jQuery('#pwgc-balance-search-results').html('<i class="fas fa-cog fa-spin fa-3x"></i>');

    var searchTerms = jQuery('#pwgc-balance-search');

    jQuery.post(ajaxurl, {'action': 'pw-gift-cards-search', 'search_terms': searchTerms.val(), 'security': pwgc.nonces.search}, function(result) {
        jQuery('#pwgc-balance-search-results').html(result.html);
        searchTerms.focus();
    }).fail(function(xhr, textStatus, errorThrown) {
        if (errorThrown) {
            alert(errorThrown);
        } else {
            alert('Unknown Error');
        }
        searchTerms.focus();
    });
}

function pwgcAdminGiftCardActivityLoadStart(row) {
    var buttonCell = row.find('.pwgc-search-result-buttons').first();
    var activity = buttonCell.find('.pwgc-balance-activity-container');
    if (activity.length == 0) {
        activity = jQuery('<div class="pwgc-balance-activity-container"></div>').appendTo(buttonCell);
    }
    activity.html('<i class="fas fa-cog fa-spin fa-2x"></i>');
}

function pwgcAdminGiftCardActivity(row) {
    pwgcAdminGiftCardActivityLoadStart(row);

    var cardNumber = row.attr('data-gift-card-number');
    var buttonCell = row.find('.pwgc-search-result-buttons').first();
    var activity = buttonCell.find('.pwgc-balance-activity-container');

    jQuery.post(ajaxurl, {'action': 'pw-gift-cards-view_activity', 'card_number': cardNumber, 'security': pwgc.nonces.view_activity}, function(result) {
        activity.html(result.html);
    }).fail(function(xhr, textStatus, errorThrown) {
        if (errorThrown) {
            alert(errorThrown);
        } else {
            alert('Unknown Error');
        }
    });
}

function pwgcSaveDesign() {
    var messageContainer = jQuery('#pwgc-save-design-message');
    var saveButton = jQuery('#pwgc-save-design-button');
    var form = jQuery('#pwgc-designer-form').serialize();

    saveButton.attr('disabled', true);
    messageContainer.clearQueue().html('<i class="fas fa-cog fa-spin fa-3x"></i>').show();

    jQuery.post(ajaxurl, {'action': 'pw-gift-cards-save_design', 'form': form, 'security': pwgc.nonces.save_design }, function(result) {
        saveButton.attr('disabled', false);
        messageContainer.html(result.html).delay(2000).fadeOut('slow');
    }).fail(function(xhr, textStatus, errorThrown) {
        saveButton.attr('disabled', false);
        if (errorThrown) {
            messageContainer.html(errorThrown);
        } else {
            messageContainer.text('Unknown ajax error');
        }
    });
}

function pwgcSendEmailDesignPreview() {
    var emailAddress = prompt(pwgc.i18n.preview_email_notice + '\n\n' + pwgc.i18n.preview_email_prompt, jQuery('#pwgc-preview-email-button').attr('data-email'));
    if (emailAddress) {
        // Save it for later.
        jQuery('#pwgc-preview-email-button').attr('data-email', emailAddress);

        var previewButton = jQuery('#pwgc-preview-email-button');
        var messageContainer = jQuery('#pwgc-preview-email-message');

        previewButton.attr('disabled', true);
        messageContainer.clearQueue().html('<i class="fas fa-cog fa-spin"></i>').show();

        jQuery.post(ajaxurl, {'action': 'pw-gift-cards-preview_email', 'email_address': emailAddress, 'security': pwgc.nonces.preview_email}, function(result) {
            messageContainer.html(result.html).delay(2000).fadeOut('slow');
            previewButton.attr('disabled', false);
        }).fail(function(xhr, textStatus, errorThrown) {
            previewButton.attr('disabled', false);
            if (!errorThrown) {
                errorThrown = 'Unknown Error';
            }
            messageContainer.html(errorThrown);
        });
    }
}

function pwgcDelete(row) {
    var cardNumber = row.attr('data-gift-card-number');

    jQuery.post(ajaxurl, {'action': 'pw-gift-cards-delete', 'card_number': cardNumber, 'security': pwgc.nonces.delete}, function(result) {
        row.find('.pwgc-buttons-inactive, .pwgc-inactive-card').removeClass('pwgc-hidden');
        row.find('.pwgc-buttons-active').addClass('pwgc-hidden');
        pwgcAdminLoadBalanceSummary();
    }).fail(function(xhr, textStatus, errorThrown) {
        if (errorThrown) {
            alert(errorThrown);
        } else {
            alert('Unknown ajax error');
        }
    });
}

function pwgcRestore(row) {
    var cardNumber = row.attr('data-gift-card-number');

    jQuery.post(ajaxurl, {'action': 'pw-gift-cards-restore', 'card_number': cardNumber, 'security': pwgc.nonces.restore}, function(result) {
        row.find('.pwgc-buttons-inactive, .pwgc-inactive-card').addClass('pwgc-hidden');
        row.find('.pwgc-buttons-active').removeClass('pwgc-hidden');
        pwgcAdminLoadBalanceSummary();
    }).fail(function(xhr, textStatus, errorThrown) {
        if (errorThrown) {
            alert(errorThrown);
        } else {
            alert('Unknown ajax error');
        }
    });
}

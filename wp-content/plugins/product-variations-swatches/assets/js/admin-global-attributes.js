'use strict';
jQuery(document).ready(function () {
    let vi_wpvs_woo_wrap = jQuery('.wrap');
    if (vi_wpvs_woo_wrap.length > 0) {
        if (vi_wpvs_woo_wrap.hasClass('woocommerce')) {
            vi_wpvs_woo_wrap.find('h1').append('<button class="button primary vi_wpvs_global_setting_url"><a  href="' + vi_wpvs_admin_global_attributes.global_setting_url + '" target="_blank">' + vi_wpvs_admin_global_attributes.global_setting_title + '</a></button>');
            vi_wpvs_woo_wrap.find('.attributes-table .row-actions span.edit > a').each(function (k, item) {
                let term_name = jQuery(item).closest('td').find('strong a').text();
                jQuery(item).after(' | </span><span class="vi_wpvs_edit"><a  href="' + vi_wpvs_admin_global_attributes.global_setting_url + '&vi_wvps_search=' + term_name + '" target="_blank">' + vi_wpvs_admin_global_attributes.global_item_setting_title + '</a></span>')
            });
        } else if (vi_wpvs_admin_global_attributes.taxonomy) {
            vi_wpvs_woo_wrap.find('h1').append('<button class="button primary vi_wpvs_global_setting_url"><a  href="' + vi_wpvs_admin_global_attributes.global_setting_url + '&vi_wvps_search=' + vi_wpvs_admin_global_attributes.taxonomy + '" target="_blank">' + vi_wpvs_admin_global_attributes.global_attr_setting_title + '</a></button>');
        }
    }
    jQuery('.vi-wpvs-term-image-remove').unbind().on('click', function (e) {
        let wrap = jQuery(this).closest('.vi-wpvs-swatches-setting-wrap');
        let src_placeholder=wrap.find('.vi-wpvs-term-image-preview img').data('src_placeholder');
        wrap.find('.vi_wpvs_term_image').val('');
        wrap.find('.vi-wpvs-term-image-preview img').attr('src',src_placeholder);
        jQuery(this).addClass('vi-wpvs-hidden');
    });
    if (jQuery('.vi-wpvs-term-image-add-new').length) {
        var vi_img_uploader;
        jQuery('.vi-wpvs-term-image-add-new').off().on('click', function (e) {
            e.preventDefault();
            jQuery('.vi_attribute_image-editing').removeClass('vi_attribute_image-editing');
            jQuery(this).closest('.vi-wpvs-swatches-setting-wrap').addClass('vi_attribute_image-editing');
            //If the uploader object has already been created, reopen the dialog
            if (vi_img_uploader) {
                vi_img_uploader.open();
                return false;
            }
            //Extend the wp.media object
            vi_img_uploader = wp.media.frames.file_frame = wp.media({
                title: 'Choose Image',
                button: {
                    text: 'Choose Image'
                },
                multiple: true
            });

            //When a file is selected, grab the URL and set it as the text field's value
            vi_img_uploader.on('select', function () {
                let attachment = vi_img_uploader.state().get('selection').first().toJSON();
                jQuery('.vi_attribute_image-editing').find('.vi_wpvs_term_image').val(attachment.id);
                jQuery('.vi_attribute_image-editing').find('.vi-wpvs-term-image-preview img').attr('src',attachment.url);
                jQuery('.vi_attribute_image-editing').find('.vi-wpvs-term-image-remove').removeClass('vi-wpvs-hidden');
                jQuery('.vi_attribute_image-editing').removeClass('vi_attribute_image-editing');
            });

            //Open the uploader dialog
            vi_img_uploader.open();
        });
    }
    if (jQuery('.vi-wpvs-term-color-container-wrap').length) {
        var default_color = vi_wpvs_admin_global_attributes.settings_default_color;
        handleFilterColor();
        handleColorPicker();
        duplicateItem();
        removeItem();
    }

    function handleFilterColor() {
        jQuery('#tag-name, #name').unbind().on('change', function () {
            if (jQuery('.vi_wpvs_term_color').length === 1) {
                let color_name = jQuery(this).val();
                if (!color_name) {
                    return false;
                }
                color_name = color_name.toLowerCase();
                if (default_color[color_name]) {
                    jQuery('.vi_wpvs_term_color').val(default_color[color_name]).css({'background': default_color[color_name]});
                }
            }
        });
    }

    function handleColorPicker() {
        jQuery('.vi-wpvs-color').each(function () {
            jQuery(this).css({backgroundColor: jQuery(this).val()});
        });
        jQuery('.vi-wpvs-color').unbind().minicolors({
            change: function (value, opacity) {
                jQuery(this).parent().find('.vi-wpvs-color').css({backgroundColor: value});
            },
            animationSpeed: 50,
            animationEasing: 'swing',
            changeDelay: 0,
            control: 'wheel',
            defaultValue: '',
            format: 'rgb',
            hide: null,
            hideSpeed: 100,
            inline: false,
            keywords: '',
            letterCase: 'lowercase',
            opacity: true,
            position: 'bottom left',
            show: null,
            showSpeed: 100,
            theme: 'default',
            swatches: []
        });
    }

    function wpvs_term_color_preview() {
        let $colors_wrap = jQuery('.vi-wpvs-term-color-container-wrap .vi_wpvs_term_color'),
            separator = jQuery('.vi_wpvs_term_color_separator').val();
        if ($colors_wrap.length === 0) {
            return false;
        }
        if ($colors_wrap.length === 1 && !$colors_wrap.val()) {
            let color_name = jQuery('#name').val() || jQuery('#slug').val();
            if (!color_name) {
                return false;
            }
            color_name = color_name.toLowerCase();
            if (default_color[color_name]) {
                $colors_wrap.val(default_color[color_name]).css({'background': default_color[color_name]});
            }
            return false
        }
        if ($colors_wrap.length > 1) {
            let colors = $colors_wrap.map(function () {
                return jQuery(this).val();
            });
        }
    }

    // duplicate item

    function duplicateItem() {
        jQuery('.vi-wpvs-term-color-action-clone').unbind().on('click', function (e) {
            e.stopPropagation();
            var current = jQuery(this).parent().parent();
            var newRow = current.clone();
            newRow.find('.iris-picker').remove();
            newRow.insertAfter(current);
            duplicateItem();
            removeItem();
            handleColorPicker();
            e.stopPropagation();
        });

    }

    // remove item
    function removeItem() {
        jQuery('.vi-wpvs-term-color-action-remove').unbind().on('click', function (e) {
            if (jQuery('.vi-wpvs-term-color-action-remove').length === 1) {
                alert('You can not remove the last item.');
                return false;
            }
            if (confirm("Would you want to remove this?")) {
                jQuery(this).parent().parent().remove();
            }
            e.stopPropagation();
        });
    }
});
'use strict';
jQuery(document).ready(function () {
    /*Set paged to 1 before submitting*/
    jQuery('.search-box').find('input[type="submit"]').on('click', function () {
        let $form = jQuery(this).closest('form');
        $form.find('.current-page').val(1);
    });
    jQuery('.vi-ui.accordion.active').vi_accordion('refresh');
    jQuery('.vi-ui.dropdown').unbind().dropdown();
    jQuery('.vi-ui.checkbox').unbind().checkbox();
    jQuery('.vi-wpvs-accordion-taxonomy_type').unbind().dropdown({
        onChange: function (val) {
            let container = jQuery(this).closest('.vi-wpvs-accordion-attr-wrap').find('.vi-wpvs-accordion-term-wrap-wrap');
            container.addClass('vi-wpvs-accordion-term-wrap-close');
            container.find('.dropdown.icon, .vi-wpvs-attribute-value-content-wrap ').addClass('vi-wpvs-hidden');
            if (jQuery.inArray(val, ['color', 'image']) !== -1) {
                container.removeClass('vi-wpvs-accordion-term-wrap-close');
                container.find('.dropdown.icon').removeClass('vi-wpvs-hidden');
                if (val === 'color') {
                    container.find('.vi-wpvs-attribute-value-content-color-wrap').removeClass('vi-wpvs-hidden');
                } else {
                    container.find('.vi-wpvs-attribute-value-content-image-wrap').removeClass('vi-wpvs-hidden');
                }
            } else {
                container.find('.title, .content ').removeClass('active');
            }
        }
    });
    jQuery('input[type="checkbox"]').unbind().on('change', function () {
        if (jQuery(this).prop('checked')) {
            jQuery(this).parent().find('input[type="hidden"]').val('1');
        } else {
            jQuery(this).parent().find('input[type="hidden"]').val('');
        }
    });
    UploadImage();

    function UploadImage() {
        var viwpvs_img_uploader;
        jQuery('.vi-attribute-image-remove').off().on('click', function (e) {
            let wrap = jQuery(this).closest('.vi-wpvs-attribute-value-content-image-wrap');
            let src_placeholder=wrap.find('.vi-attribute-image-preview img').data('src_placeholder');
            wrap.find('.vi_attribute_image').val('');
            wrap.find('.vi-attribute-image-preview img').attr('src',src_placeholder);
            jQuery(this).addClass('vi-wpvs-hidden');
        });
        jQuery('.vi-attribute-image-add-new').off().on('click', function (e) {
            e.preventDefault();
            jQuery('.vi_attribute_image-editing').removeClass('vi_attribute_image-editing');
            jQuery(this).closest('.vi-wpvs-attribute-value-content-image-wrap').addClass('vi_attribute_image-editing');
            //If the uploader object has already been created, reopen the dialog
            if (viwpvs_img_uploader) {
                viwpvs_img_uploader.open();
                return false;
            }
            //Extend the wp.media object
            viwpvs_img_uploader = wp.media.frames.file_frame = wp.media({
                title: 'Choose Image',
                button: {
                    text: 'Choose Image'
                },
                multiple: true
            });

            //When a file is selected, grab the URL and set it as the text field's value
            viwpvs_img_uploader.on('select', function () {
                let attachment = viwpvs_img_uploader.state().get('selection').first().toJSON();
                jQuery('.vi_attribute_image-editing').find('.vi_attribute_image').val(attachment.id);
                jQuery('.vi_attribute_image-editing').find('.vi-attribute-image-preview img').attr('src',attachment.url);
                jQuery('.vi_attribute_image-editing').find('.vi-attribute-image-remove').removeClass('vi-wpvs-hidden');
                jQuery('.vi_attribute_image-editing').removeClass('vi_attribute_image-editing');
            });

            //Open the uploader dialog
            viwpvs_img_uploader.open();
        });
    }

    handleColorPicker();

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
            format: 'rgb',
            hide: null,
            hideSpeed: 100,
            inline: false,
            defaultValue: '',
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

    duplicateItem();

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

    removeItem();

    // remove item
    function removeItem() {
        jQuery('.vi-wpvs-term-color-action-remove').unbind().on('click', function (e) {
            if (jQuery(this).closest('.vi-wpvs-attribute-value-content-color-wrap').find('.vi-wpvs-term-color-action-remove').length === 1) {
                alert('You can not remove the last item.');
                return false;
            }
            if (confirm("Would you want to remove this?")) {
                jQuery(this).parent().parent().remove();
            }
            e.stopPropagation();
        });
    }

    //save one attribute
    jQuery('.vi-wvps-attrs-save-all-button').unbind().on('click', function (e) {
        e.stopPropagation();
        let button_import = jQuery(this);
        if (button_import.hasClass('loading')) {
            return;
        }
        if (!confirm(vi_woo_product_variation_swatches_admin_attrs_js.save_all_confirm)) {
            return;
        }
        if (jQuery('.vi-wvps-attr-taxonomy-save-button').length > 0) {
            jQuery('.vi-wvps-attr-taxonomy-save-button').trigger('click');
            button_import.addClass('loading');
        } else {
            alert(vi_woo_product_variation_swatches_admin_attrs_js.not_found_error);
        }
    });
    jQuery('.vi-wvps-attr-taxonomy-save-button').unbind().on('click', function (e) {
        e.stopPropagation();
        let $button = jQuery(this);
        let data = {}, term_data = {}, term_container,
            container = $button.closest('.vi-wpvs-accordion-attr-wrap');
        term_container = container.find('.vi-wpvs-accordion-term-wrap');
        data['action'] = 'vi_wvps_save_global_attrs';
        data['nonce'] = jQuery('#_vi_wvps_global_attrs_nonce').val();
        data['taxonomy_slug'] = container.find('[name="taxonomy_slug"]').val();
        data['taxonomy_display_type'] = container.find('[name="taxonomy_display_type"]').val();
        data['taxonomy_type'] = container.find('[name="taxonomy_type"]').val();
        data['taxonomy_profile'] = container.find('[name="taxonomy_profile"]').val();
        if (term_container.length) {
            term_container.each(function () {
                let term_id = jQuery(this).find('[name="term_id"]').val(), temp_color = [];
                jQuery(this).find('[name="vi_attribute_colors[]"]').map(function () {
                    temp_color.push(jQuery(this).val());
                });
                term_data[term_id] = {
                    color_separator: jQuery(this).find('[name="vi_attribute_color_separator"]').val(),
                    color: temp_color,
                    img_id: jQuery(this).find('[name="vi_attribute_images"]').val(),
                }
            });
        }
        data['term_data'] = term_data;
        jQuery.ajax({
            url: vi_woo_product_variation_swatches_admin_attrs_js.ajax_url,
            type: 'post',
            data: data,
            beforeSend: function () {
                $button.addClass('loading')
            },
            success: function (response) {
                if (response.status === 'successfully') {
                    if (jQuery('.vi-wvps-attr-taxonomy-save-button.loading').length === 1) {
                        jQuery('.vi-wvps-attrs-save-all-button').removeClass('loading');
                        jQuery('.vi-wvps-save-sucessful-popup').animate({'bottom': '45px'}, 500);
                        setTimeout(function () {
                            jQuery('.vi-wvps-save-sucessful-popup').animate({'bottom': '-300px'}, 200);
                        }, 5000);
                    }
                } else {
                    alert(response.message);
                    location.reload();
                }
            },
            error: function (err) {
            },
            complete: function () {
                $button.removeClass('loading')
            }
        });
    });
});
'use strict';
jQuery(document).ready(function () {
    jQuery('.vi-ui.vi-ui-main.tabular.menu .item').vi_tab({
        history: true,
        historyType: 'hash'
    });
    /*Setup tab*/
    let tabs,
        tabEvent = false,
        initialTab = 'swatches_profile',
        navSelector = '.vi-ui.vi-ui-main.menu',
        panelSelector = '.vi-ui.vi-ui-main.tab',
        panelFilter = function () {
            jQuery(panelSelector + ' a').filter(function () {
                return jQuery(navSelector + ' a[title=' + jQuery(this).attr('title') + ']').size() != 0;
            });
        };
    // Initializes plugin features
    jQuery.address.strict(false).wrap(true);

    if (jQuery.address.value() == '') {
        jQuery.address.history(false).value(initialTab).history(true);
    }
    // Address handler
    jQuery.address.init(function (event) {

        // Adds the ID in a lazy manner to prevent scrolling
        jQuery(panelSelector).attr('id', initialTab);

        panelFilter();

        // Tabs setup
        tabs = jQuery('.vi-ui.vi-ui-main.menu')
            .vi_tab({
                history: true,
                historyType: 'hash'
            });

        // Enables the plugin for all the tabs
        jQuery(navSelector + ' a').on('click', function (event) {
            tabEvent = true;
            tabEvent = false;
            return true;
        });

    });

    jQuery('.ui-sortable').sortable({
        placeholder: 'wpvs-place-holder',
    });
    handleInit();

    function handleInit() {
        jQuery('.vi-ui.accordion').vi_accordion('refresh');
        jQuery('.vi-ui.dropdown').unbind().dropdown();
        handleValueChange();
        handleCheckBox();
        handleColorPicker();
    }

    // change name
    function handleValueChange() {
        jQuery('.vi-wpvs-names').unbind().on('keyup', function () {
            jQuery(this).parent().parent().parent().find('.vi-wpvs-accordion-name').html(jQuery(this).val());
        });
        jQuery('input[type = "number"]').unbind().on('change', function () {
            let min = parseFloat(jQuery(this).attr('min')) || 0,
                max = parseFloat(jQuery(this).attr('max')),
                val = parseFloat(jQuery(this).val()) || 0;
            if (min > val) {
                jQuery(this).val(min);
            } else {
                jQuery(this).val(val);
            }
            if (max && max < val) {
                jQuery(this).val(max);
            }
        });
    }

    function handleCheckBox() {
        jQuery('.vi-ui.checkbox').unbind().checkbox();

        jQuery('input[type="checkbox"]').unbind().on('change', function () {
            if (jQuery(this).prop('checked')) {
                jQuery(this).parent().find('input[type="hidden"]').val('1');
                if (jQuery(this).hasClass('vi-wpvs-single_attr_title-checkbox')) {
                    jQuery('.vi-wpvs-single_attr_title-enable').removeClass('vi-wpvs-hidden');
                }
            } else {
                jQuery(this).parent().find('input[type="hidden"]').val('');
                if (jQuery(this).hasClass('vi-wpvs-single_attr_title-checkbox')) {
                    jQuery('.vi-wpvs-single_attr_title-enable').addClass('vi-wpvs-hidden');
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


    jQuery(document).on('click','.vi-wpvs-reset', function () {
        if (confirm('All settings will be deleted. Are you sure you want to reset yours settings?')){
            jQuery(this).attr('type','submit');
        }
    });
    jQuery(document).on('click','.vi-wpvs-import', function () {
        jQuery('.vi-wpvs-import-wrap-wrap').toggleClass('vi-wpvs-hidden');
    });
    jQuery('.vi-wpvs-save').on('click', function () {
        jQuery(this).addClass('loading');
        let nameArr = jQuery('input[name="names[]"]');
        let z, v;
        for (z = 0; z < nameArr.length; z++) {
            if (!jQuery('input[name="names[]"]').eq(z).val()) {
                alert('Name cannot be empty!');
                if (!jQuery('.vi-wpvs-accordion').eq(z).hasClass('vi-wpvs-active-accordion')) {
                    jQuery('.vi-wpvs-accordion').eq(z).addClass('vi-wpvs-active-accordion');
                }
                jQuery('.vi-wpvs-save').removeClass('loading');
                return false;
            }
        }

        for (z = 0; z < nameArr.length - 1; z++) {
            for (v = z + 1; v < nameArr.length; v++) {
                if (jQuery('input[name="names[]"]').eq(z).val() === jQuery('input[name="names[]"]').eq(v).val()) {
                    alert("Names are unique!");
                    if (!jQuery('.vi-wpvs-accordion').eq(v).hasClass('vi-wpvs-active-accordion')) {
                        jQuery('.vi-wpvs-accordion').eq(v).addClass('vi-wpvs-active-accordion');
                    }
                    jQuery('.vi-wpvs-save').removeClass('loading');
                    return false;
                }
            }
        }

        jQuery(this).attr('type', 'submit');
    });
});
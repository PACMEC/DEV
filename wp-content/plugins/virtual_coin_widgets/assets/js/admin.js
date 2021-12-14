'use strict';

(function ($, VCW) {

    var builder = {
        hide_selector: '.id-row,.symbol1-row,.symbol2-row,.ids-row,.currency-row,.currency1-row,.currency2-row,.currency3-row,.url-row,.target-row,.initial-row,.count-row, .show_logo-row',

        all_fields: ['shortcode', 'color', 'fullwidth', 'id', 'symbol1', 'symbol2', 'ids', 'currency', 'currency1', 'currency2', 'currency3', 'url', 'target', 'initial', 'count', 'show_logo'],

        fields: {
            'price-label': '.id-row,.currency-row,.url-row,.target-row,.show_logo-row',
            'change-label': '.id-row,.url-row,.target-row,.show_logo-row',
            'price-big-label': '.id-row,.currency1-row,.currency2-row,.currency3-row,.url-row,.target-row,.show_logo-row',
            'change-big-label': '.id-row,.url-row,.target-row,.show_logo-row',
            'price-card': '.id-row,.currency1-row,.currency2-row,.currency3-row,.url-row,.target-row,.show_logo-row',
            'change-card': '.id-row,.url-row,.target-row,.show_logo-row',
            'full-card': '.id-row,.currency1-row,.currency2-row,.currency3-row,.url-row,.target-row,.show_logo-row',
            'converter': '.symbol1-row,.symbol2-row,.initial-row',
            'table': '.currency-row,.ids-row,.count-row',
            'small-table': '.currency-row,.ids-row,.count-row'
        },
        elements: null,

        init: function () {

            var id_list_sel = $('#id-list-selector'),
                id_list_add = $('#id-list-add'),
                ids = $('[name=ids]');

            id_list_add.click(function () {
                var id_val = id_list_sel.val(),
                    ids_val = ids.val();

                ids_val = ids_val.replace(/ /g,'');

                ids_val = ids_val.length ?
                    ids_val + ',' + id_val : id_val;

                ids.val(ids_val);
            })

            this.attach($('#vcw-admin-builder'), $('#vcw-admin-preview'), $('#vcw-admin-code'));
            this.refresh();
        },

        attach: function attach(form, preview, code) {
            form.submit(false);

            var elements = builder.elements = {
                form: form,
                submit: form.find('.render-submit'),
                shortcode: form.find('[name=shortcode]'),
                preview: preview,
                code: code
            };

            elements.shortcode.on('change keyup paste', function () {
                return builder.refresh();
            });
            elements.submit.click(function () {
                return builder.render();
            });
        },
        hideFields: function hideFields() {
            builder.elements.form.find(this.hide_selector).hide();
        },
        showFields: function showFields() {
            var elements = builder.elements,
                widget = elements.shortcode.val(),
                fields = builder.fields[widget];

            elements.form.find(builder.fields[widget]).show();
        },
        refresh: function refresh() {
            builder.hideFields();
            builder.showFields();
        },
        callRender: function callRender(cb) {
            var elements = builder.elements,
                req = {
                type: 'POST',
                url: VCW.ajax_url,
                data: {
                    action: 'vcw_render'
                }
            };

            builder.all_fields.forEach(function (field) {
                req.data[field] = elements.form.find('[name=' + field + ']').val();
            });

            $.ajax(req).done(function (response) {
                if (response && response.html && response.code) {
                    cb(response.html, response.code);
                }
            });
        },
        render: function render() {
            var elements = builder.elements;

            builder.callRender(function (html, code) {
                elements.preview.html(html);
                VCW.compile(elements.preview);
                elements.code.find('textarea').val(code);
            });
        }
    };

    $(function () {
        if ($('#vcw-admin-page').length) {
            builder.init();
        }
    });
})(jQuery, VirtualCoinWidgets);
'use strict';
jQuery(document).ready(function ($) {
    $('.vi-ui.accordion').vi_accordion('refresh');
    /*import product options*/
    $('.s2w-save-products-options').on('click', function (e) {
        let button = $(this);
        button.addClass('loading');
        let saving_overlay = $('.s2w-import-products-options-saving-overlay');
        saving_overlay.removeClass('s2w-hidden');
        _s2w_nonce = $('#_s2w_nonce').val();
        domain = $('#s2w-domain').val();
        product_status = $('#s2w-product_status').val();
        product_categories = $('#s2w-product_categories').val();
        download_images = $('#s2w-download_images').prop('checked') ? 1 : 0;
        products_per_request = $('#s2w-products_per_request').val();
        product_import_sequence = $('#s2w-product_import_sequence').val();
        $.ajax({
            url: s2w_params_admin.url,
            type: 'POST',
            dataType: 'JSON',
            data: {
                action: 's2w_save_settings_product_options',
                domain: domain,
                _s2w_nonce: _s2w_nonce,
                download_images: download_images,
                product_status: product_status,
                product_categories: product_categories ? product_categories : [],
                products_per_request: products_per_request,
                product_import_sequence: product_import_sequence,
            },
            success: function (response) {
                total_products = parseInt(response.total_products);
                total_pages = response.total_pages;
                current_import_id = response.current_import_id;
                current_import_product = parseInt(response.current_import_product);
                current_import_page = response.current_import_page;
                button.removeClass('loading');
                saving_overlay.addClass('s2w-hidden');
                s2w_product_options_close();
            },
            error: function (err) {
                button.removeClass('loading');
                saving_overlay.addClass('s2w-hidden');
                s2w_product_options_close();
            }
        })
    });
    $('.s2w-import-products-options-close').on('click', function (e) {
        s2w_product_options_close();
        s2w_product_options_cancel();
    });
    $('.s2w-import-products-options-overlay').on('click', function (e) {
        $('.s2w-import-products-options-close').click();
    });
    $('.s2w-import-products-options-shortcut').on('click', function (e) {
        if (!$('.s2w-accordion').find('.content').eq(0).hasClass('active')) {
            e.preventDefault();
            s2w_product_options_show();
            $('.s2w-import-products-options-main').append($('.s2w-import-products-options-content'));
        } else if (!$('#s2w-import-products-options-anchor').hasClass('active')) {
            $('#s2w-import-products-options-anchor').vi_accordion('open')
        }
    });

    function s2w_product_options_cancel() {
        $('#s2w-product_status').val(product_status);
        $('#s2w-download_images').prop('checked', (download_images == 1));
        $('#s2w-products_per_request').val(products_per_request);
        $('#s2w-product_import_sequence').val(product_import_sequence);
        if (product_categories) {
            $('#s2w-product_categories').val(product_categories).trigger('change');
        } else {
            $('#s2w-product_categories').val(null).trigger('change');
        }
    }

    $('.search-category').select2({
        closeOnSelect: false,
        placeholder: 'Please fill in your category title',
        ajax: {
            url: 'admin-ajax.php?action=s2w_search_cate',
            dataType: 'json',
            type: 'GET',
            quietMillis: 50,
            delay: 250,
            data: function (params) {
                return {
                    keyword: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) {
            return markup;
        }, // let our custom formatter work
        minimumInputLength: 2
    });
    $('.vi-ui.checkbox').checkbox();
    $('.vi-ui.dropdown').dropdown();

    $('.s2w-import-element-enable-bulk').on('change', function () {
        $('.s2w-import-element-enable').prop('checked', $(this).prop('checked'));
    });
    $('#s2w-domain').on('change', function () {
        let domain = $(this).val();
        domain = domain.replace(/https:\/\//g, '');
        domain = domain.replace(/\//g, '');

        $(this).val(domain);
    });
    let selected_elements = [];
    let progress_bars = {};

    function get_selected_elements() {
        selected_elements = [];
        progress_bars = [];
        $('.s2w-import-element-enable').map(function () {
            if ($(this).prop('checked')) {
                let element_name = $(this).data()['element_name'];
                selected_elements.push(element_name);
                progress_bars[element_name] = $('#s2w-' + element_name.replace('_', '-') + '-progress');
            }
        });
        console.log(progress_bars);
    }

    function s2w_import_element() {
        if (selected_elements.length) {
            let element = selected_elements.shift();
            progress_bars[element].progress('set label', 'Importing...');
            progress_bars[element].progress('set active');
            switch (element) {
                case 'products':
                    s2w_import_products();
                    break;
                case 'product_categories':
                    s2w_import_product_categories();
                    break;
            }
        } else {
            s2w_unlock_buttons();
            import_active = false;
            $('.s2w-sync').removeClass('loading');
            setTimeout(function () {
                alert('Import completed.');
            }, 400);
        }
    }

    let request_timeout = $('#s2w-request_timeout').val(),
        products_per_request = $('#s2w-products_per_request').val(),
        product_import_sequence = $('#s2w-product_import_sequence').val();

    let total_products = parseInt(s2w_params_admin.total_products),
        total_pages = s2w_params_admin.total_pages,
        current_import_id = s2w_params_admin.current_import_id,
        current_import_product = parseInt(s2w_params_admin.current_import_product),
        current_import_page = s2w_params_admin.current_import_page,
        product_percent_old = 0,

        imported_elements = s2w_params_admin.imported_elements,
        elements_titles = s2w_params_admin.elements_titles,
        _s2w_nonce = $('#_s2w_nonce').val(),
        domain = $('#s2w-domain').val(),
        api_key = $('#s2w-api_key').val(),
        api_secret = $('#s2w-api_secret').val(),
        download_images = $('#s2w-download_images').prop('checked') ? 1 : 0,
        product_status = $('#s2w-product_status').val(),
        product_categories = $('#s2w-product_categories').val();

    let save_active = false,
        import_complete = false,
        error_log = '',
        import_active = false;
    let warning;
    let warning_empty_store = s2w_params_admin.warning_empty_store,
        warning_empty_api_key = s2w_params_admin.warning_empty_api_key,
        warning_empty_api_secret = s2w_params_admin.warning_empty_api_secret;

    function s2w_validate_data() {
        warning = '';
        let validate = true;
        if (!$('#s2w-domain').val()) {
            validate = false;
            warning += warning_empty_store;
        }
        if (!$('#s2w-api_key').val()) {
            validate = false;
            warning += warning_empty_api_key;
        }
        if (!$('#s2w-api_secret').val()) {
            validate = false;
            warning += warning_empty_api_secret;
        }
        return validate;
    }

    $('.s2w-delete-history').on('click', function () {
        if (!confirm('You are about to delete import history of selected elements. Continue?')) {
            return false;
        }
    })
    $('.s2w-save').on('click', function () {
        if (!s2w_validate_data()) {
            alert(warning);
            return;
        }
        if (import_active || save_active) {
            return;
        }
        save_active = true;
        product_status = $('#s2w-product_status').val();
        product_categories = $('#s2w-product_categories').val();
        _s2w_nonce = $('#_s2w_nonce').val();
        domain = $('#s2w-domain').val();
        api_key = $('#s2w-api_key').val();
        api_secret = $('#s2w-api_secret').val();
        download_images = $('#s2w-download_images').prop('checked') ? 1 : 0;
        request_timeout = $('#s2w-request_timeout').val();
        products_per_request = $('#s2w-products_per_request').val();
        product_import_sequence = $('#s2w-product_import_sequence').val();
        let button = $(this);
        button.addClass('loading');
        $.ajax({
            url: s2w_params_admin.url,
            type: 'POST',
            dataType: 'JSON',
            data: {
                action: 's2w_save_settings',
                _s2w_nonce: _s2w_nonce,
                step: 'save',
                domain: domain,
                api_key: api_key,
                api_secret: api_secret,
                download_images: download_images,
                product_status: product_status,
                product_categories: product_categories ? product_categories : [],
                request_timeout: request_timeout,
                products_per_request: products_per_request,
                product_import_sequence: product_import_sequence,
            },
            success: function (response) {
                total_products = parseInt(response.total_products);
                total_pages = response.total_pages;
                current_import_id = response.current_import_id;
                current_import_product = parseInt(response.current_import_product);
                current_import_page = response.current_import_page;
                imported_elements = response.imported_elements;
                save_active = false;
                button.removeClass('loading');
                if (response.api_error) {
                    alert(response.api_error);
                    $('.s2w-import-container').hide();
                    $('.s2w-error-warning').show();
                } else if (response.validate) {
                    $('.s2w-import-element-enable').map(function () {
                        let element = $(this).data()['element_name'];

                        if (imported_elements[element] == 1) {
                            $(this).prop('checked', false);
                            $('.s2w-import-' + element.replace(/_/g, '-') + '-check-icon').addClass('green').removeClass('grey');
                        } else {
                            $(this).prop('checked', true);
                            $('.s2w-import-' + element.replace(/_/g, '-') + '-check-icon').addClass('grey').removeClass('green');
                        }
                    });
                    $('.s2w-import-container').show();
                    $('.s2w-error-warning').hide();
                    $('.s2w-accordion>.title').removeClass('active');
                    $('.s2w-accordion>.content').removeClass('active');
                }
            },
            error: function (err) {
                save_active = false;
                button.removeClass('loading');
                console.log(err)
            }
        })
    });
    $('.s2w-sync').on('click', function () {
        if (!s2w_validate_data()) {
            alert(warning);
            return;
        }
        get_selected_elements();
        if (selected_elements.length == 0) {
            alert('Please select which data you want to import.');
            return;
        } else {
            let imported = [];
            for (let i in selected_elements) {
                let element = selected_elements[i];
                if (imported_elements[element] == 1) {
                    imported.push(elements_titles[element]);
                }
            }
            if (imported.length > 0) {
                if (!confirm('You already imported ' + imported.join(', ') + '. Do you want to continue?')) {
                    return;
                }
            }
        }
        let button = $(this);
        if (import_active || save_active) {
            return;
        }
        $('.s2w-import-progress').css({'visibility': 'hidden'});
        for (let ele in progress_bars) {
            progress_bars[ele].css({'visibility': 'visible'});
            progress_bars[ele].progress('set label', 'Waiting...');
        }
        import_active = true;
        button.addClass('loading');
        s2w_lock_buttons();
        s2w_jump_to_import();
        s2w_import_element();
    });

    function s2w_import_products() {
        $.ajax({
            url: s2w_params_admin.url,
            type: 'POST',
            dataType: 'JSON',
            data: {
                action: 's2w_import_shopify_to_woocommerce',
                _s2w_nonce: _s2w_nonce,
                step: 'products',
                total_products: total_products,
                total_pages: total_pages,
                current_import_id: current_import_id,
                current_import_page: current_import_page,
                current_import_product: current_import_product,
                error_log: error_log,
            },
            success: function (response) {
                if (response.status === 'retry') {
                    total_products = parseInt(response.total_products);
                    total_pages = parseInt(response.total_pages);
                    current_import_id = response.current_import_id;
                    current_import_page = parseInt(response.current_import_page);
                    current_import_product = parseInt(response.current_import_product);
                    s2w_import_products();
                }else{
                    error_log = '';
                    progress_bars['products'].progress('set label', response.message.toString());

                    if (response.status === 'error') {
                        s2w_import_products();
                    } else {
                        current_import_id = response.current_import_id;
                        current_import_page = parseInt(response.current_import_page);
                        current_import_product = parseInt(response.current_import_product);
                        let imported_products = parseInt(response.imported_products);
                        let percent = Math.ceil(imported_products * 100 / total_products);
                        if (percent > 100) {
                            percent = 100;
                        }
                        progress_bars['products'].progress('set percent', percent);
                        let logs = response.logs;

                        if (logs) {
                            $('.s2w-logs').append(response.logs).scrollTop($('.s2w-logs')[0].scrollHeight);
                        }
                        if (response.status === 'successful') {
                            if (current_import_page <= total_pages) {
                                s2w_import_products();
                            } else {
                                import_complete = true;

                                progress_bars['products'].progress('complete');
                                s2w_import_element();
                            }
                        } else {
                            import_complete = true;

                            progress_bars['products'].progress('complete');
                            s2w_import_element();
                        }
                    }
                }
            },
            error: function (err) {
                error_log = 'error ' + err.status + ' : ' + err.statusText;
                console.log(err);
                // progress_bars['products'].progress('set error');
                if (!import_complete) {
                    selected_elements.unshift('products');
                }
                setTimeout(function () {
                    s2w_import_element();
                }, 3000)
            }
        })
    }

    let categories_current_page = 0;
    let total_categories = 0;

    function s2w_import_product_categories() {
        $.ajax({
            url: s2w_params_admin.url,
            type: 'POST',
            dataType: 'JSON',
            data: {
                action: 's2w_import_shopify_to_woocommerce',
                _s2w_nonce: _s2w_nonce,
                step: 'product_categories',
                categories_current_page: categories_current_page,
                total_categories: total_categories,
            },
            success: function (response) {
                if (response.status === 'retry') {
                    categories_current_page = parseInt(response.categories_current_page);
                    total_categories = parseInt(response.total_categories);
                    s2w_import_product_categories();
                } else if (response.status === 'success') {
                    categories_current_page = parseInt(response.categories_current_page);
                    total_categories = parseInt(response.total_categories);
                    let percent = categories_current_page * 100 / total_categories;
                    progress_bars['product_categories'].progress('set percent', percent);
                    s2w_import_product_categories();
                } else if (response.status === 'error') {
                    progress_bars['product_categories'].progress('set label', response.message.toString());
                    progress_bars['product_categories'].progress('set error');
                    setTimeout(function () {
                        s2w_import_product_categories();
                    }, 2000)
                } else {
                    categories_current_page = parseInt(response.categories_current_page);
                    total_categories = parseInt(response.total_categories);
                    progress_bars['product_categories'].progress('set label', response.message.toString());
                    progress_bars['product_categories'].progress('complete');
                    s2w_import_element();
                }
            },
            error: function (err) {
                console.log(err);
                progress_bars['product_categories'].progress('set error');
                setTimeout(function () {
                    s2w_import_element();
                }, 2000)
            },
        });

    }

    function s2w_lock_buttons() {
        $('.s2w-import-element-enable').prop('readonly', true);
    }

    function s2w_unlock_buttons() {
        $('.s2w-import-element-enable').prop('readonly', false);
    }

    function s2w_jump_to_import() {
        $('html').prop('scrollTop', $('.s2w-import-container').prop('offsetTop'))
    }
    function s2w_product_options_close() {
        s2w_product_options_hide();
        $('#s2w-import-products-options').append($('.s2w-import-products-options-content'));
    }

    function s2w_product_options_hide() {
        $('.s2w-import-products-options-modal').addClass('s2w-hidden');
        s2w_enable_scroll();
    }

    function s2w_product_options_show() {
        $('.s2w-import-products-options-modal').removeClass('s2w-hidden');
        s2w_disable_scroll();
    }
    function s2w_enable_scroll() {
        let html = $('html');
        let scrollTop = parseInt(html.css('top'));
        html.removeClass('s2w-noscroll');
        $('html,body').scrollTop(-scrollTop);
    }

    function s2w_disable_scroll() {
        let html = $('html');
        if ($(document).height() > $(window).height()) {
            let scrollTop = (html.scrollTop()) ? html.scrollTop() : $('body').scrollTop(); // Works for Chrome, Firefox, IE...
            html.addClass('s2w-noscroll').css('top', -scrollTop);
        }
    }
});

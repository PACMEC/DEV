'use strict';
jQuery(document).ready(function ($) {
    let $button_download = $('.s2w-action-download');
    let $button_download_all = $('.s2w-action-download-all');
    let $button_delete = $('.s2w-action-delete');
    let $button_delete_all = $('.s2w-action-delete-all');
    $button_download_all.on('click', function () {
        $('.s2w-action-download').click();
    });
    $button_delete_all.on('click', function () {
        if (confirm(s2w_params_admin_error_images.i18n_confirm_delete_all)) {
            $('.s2w-action-delete').map(function () {
                let $button = $(this);
                let $row = $button.closest('tr');
                let item_id = $button.data('item_id');
                if (!$button.hasClass('loading')) {
                    $button.addClass('loading');
                    $.ajax({
                        url: s2w_params_admin_error_images.url,
                        type: 'POST',
                        dataType: 'JSON',
                        data: {
                            action: 's2w_delete_error_product_images',
                            item_id: item_id
                        },
                        success: function (response) {
                            $button.removeClass('loading');
                            if (response.status === 'success') {
                                $row.remove();
                            }
                        },
                        error: function (err) {
                            console.log(err);
                            $button.removeClass('loading');
                        }
                    })
                }
            })
        }
    });
    $button_delete.on('click', function () {
        let $button = $(this);
        let $row = $button.closest('tr');
        let item_id = $button.data('item_id');
        if ($button.hasClass('loading')) {
            return;
        }
        if (confirm(s2w_params_admin_error_images.i18n_confirm_delete)) {
            $button.addClass('loading');
            $.ajax({
                url: s2w_params_admin_error_images.url,
                type: 'POST',
                dataType: 'JSON',
                data: {
                    action: 's2w_delete_error_product_images',
                    item_id: item_id
                },
                success: function (response) {
                    $button.removeClass('loading');
                    if (response.status === 'success') {
                        $row.remove();
                    }
                },
                error: function (err) {
                    console.log(err);
                    $button.removeClass('loading');
                }
            })
        }
    });
    $button_download.on('click', function () {
        let $button = $(this);
        let $row = $button.closest('tr');
        let item_id = $button.data('item_id');
        if ($button.hasClass('loading')) {
            return;
        }
        $button.addClass('loading');
        $.ajax({
            url: s2w_params_admin_error_images.url,
            type: 'POST',
            dataType: 'JSON',
            data: {
                action: 's2w_download_error_product_images',
                item_id: item_id
            },
            success: function (response) {
                $button.removeClass('loading');
                if (response.status === 'success') {
                    $row.remove();
                }
            },
            error: function (err) {
                console.log(err);
                $button.removeClass('loading');
            }
        })
    })
});

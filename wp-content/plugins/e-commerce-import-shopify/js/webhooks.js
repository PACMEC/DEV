'use strict';
jQuery(document).ready(function ($) {
    $('.vi-ui.dropdown').dropdown();
    $(document).on('click', '.s2w-webhooks-url-copy', function () {
        let $temp = $('<input>');
        $('body').append($temp);
        let $container = $(this).closest('.s2w-webhooks-url-container');
        let webhook_url = $container.find('.s2w-webhooks-url').val();
        $temp.val(webhook_url).select();
        document.execCommand('copy');
        $temp.remove();
        $container.find('.check').show().fadeOut(10000);
    });
});

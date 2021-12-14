'use strict';

(function ($, VCW, fx) {

    function logger(txt) {
        console.error('[VCW] ' + txt);
    }

    /* MoneyJS Setup */

    var Converter = {
        convert: function convert(from, to, value) {
            return fx(value).from(from).to(to);
        },
        priceFormat: function priceFormat(value) {
            value = parseFloat(value);

            var exp = Math.log10(value),
                price = 0;

            if(exp >= 4) price = value.toFixed(0);
            else if (exp >= 3) price = value.toFixed(1);
            else if (exp >= 2) price = value.toFixed(2);
            else if (exp >= 1) price = value.toFixed(2);
            else if (exp >= 0) price = value.toFixed(3);
            else if (exp >= -1) price = value.toFixed(4);
            else if (exp >= -2) price = value.toFixed(5);
            else if (exp >= -3) price = value.toFixed(6);
            else if (exp >= -4) price = value.toFixed(7);
            else if (exp >= -5) price = value.toFixed(8);
            else if (exp >= -6) price = value.toFixed(8);
            else if (exp >= -7) price = value.toFixed(8);
            else if (exp >= -8) price = value.toFixed(8);

            return price;
        },
        convertFormatted: function convertFormatted(from, to, value) {
            return this.priceFormat(this.convert(from, to, value));
        },

        displayFormat: function (value) {
            value = parseFloat(value);

            var exp = Math.log10(value),
                price = 0;

            if(exp >= 4) price = value.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            else if (exp >= 3) price = value.toFixed(1).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            else if (exp >= 2) price = value.toFixed(2);
            else if (exp >= 1) price = value.toFixed(2);
            else if (exp >= 0) price = value.toFixed(3);
            else if (exp >= -1) price = value.toFixed(4);
            else if (exp >= -2) price = value.toFixed(5);
            else if (exp >= -3) price = value.toFixed(6);
            else if (exp >= -4) price = value.toFixed(7);
            else if (exp >= -5) price = value.toFixed(8);
            else if (exp >= -6) price = value.toFixed(8);
            else if (exp >= -7) price = value.toFixed(8);
            else if (exp >= -8) price = value.toFixed(8);

            return price;
        }
    };

    var Socket = {
        channels: {},
        data: {},
        wss: null,

        connect: function connect() {
            var channels = Socket.channels,
                ids = Object.keys(Socket.channels);

            Socket.wss = new WebSocket('wss://ws.coincap.io/prices?assets='+ids.join(','));

            Socket.wss.onmessage = function (msg) {
                var data = JSON.parse(msg.data);

                Object.keys(data).forEach(function (id) {
                    channels[id].forEach(function (args) {
                        var elem = args[1],
                            text = Converter.displayFormat(Converter.convert('USD', args[0], data[id]));

                        if(text !== elem.text())
                            elem.fadeOut(200,function () {
                                elem.text(text)
                                    .fadeIn(200);
                            })
                    });
                });
            };
        },
        addToChannel: function addToChannel(id, currency, elem) {
            if(!Array.isArray(this.channels[id]))
                this.channels[id] = [];

            this.channels[id].push([currency, elem]);
        }
    };

    VCW.compile = function (parent) {
        parent.find('.vcw price-output').vcwPriceOutput();

        parent.find('.vcw-price-label, .vcw-change-label, .vcw-price-big-label, .vcw-change-big-label, .vcw-price-card, .vcw-change-card, .vcw-full-card').vcwLink();

        parent.find('.vcw-converter').vcwConverter();
    };

    $.fn.extend({
        vcwPriceOutput: function vcwPriceOutput() {
            this.each(function () {
                var elem = $(this),
                    id = elem.data('id'),
                    currency = elem.data('currency');

                Socket.addToChannel(id, currency, elem);
            });
        },
        vcwLink: function vcwLink() {
            this.each(function () {
                var elem = $(this),
                    url = elem.data('url'),
                    target = elem.data('target');

                if (url) elem.click(function () {
                    return window.open(url, target);
                });
            });
        },
        vcwConverter: function vcwConverter() {
            this.each(function () {
                var elem = $(this),
                    currency_1 = elem.find('.vcw-currency-1'),
                    currency_2 = elem.find('.vcw-currency-2'),
                    value_1 = elem.find('.vcw-value-1'),
                    value_2 = elem.find('.vcw-value-2');

                currency_1.on("change paste keyup", function () {
                    var v = Converter.convertFormatted(currency_2.val(), currency_1.val(), value_2.val());
                    value_1.val(v);
                });

                value_1.on("change paste keyup", function () {
                    var v = Converter.convertFormatted(currency_1.val(), currency_2.val(), value_1.val());
                    value_2.val(v);
                });

                currency_2.on("change paste keyup", function () {
                    var v = Converter.convertFormatted(currency_1.val(), currency_2.val(), value_1.val());
                    value_2.val(v);
                });

                value_2.on("change paste keyup", function () {
                    var v = Converter.convertFormatted(currency_2.val(), currency_1.val(), value_2.val());
                    value_1.val(v);
                });

                value_2.val(Converter.convertFormatted(currency_1.val(), currency_2.val(), value_1.val()));
            });
        }
    });


    $.ajax({
        type: 'POST',
        url: VCW.ajax_url,
        data: {
            action: 'vcw_rates'
        }
    }).done(function (rates) {
        fx.rates = rates;
        fx.base = 'USD';
    }).always(function () {
        $(function () {
            VCW.compile($(document));
            Socket.connect();
        });
    })


})(jQuery, VirtualCoinWidgets, fx);
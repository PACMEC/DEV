(function (CChartsConstants,google,jQuery) {

    var THEMES = {
        light: {
            parent: {
                'background-color': '#fff'
            },
            options: {
                titleTextStyle: {
                    color: '#333'
                },
                backgroundColor: '#fff',
                chartArea: {
                    backgroundColor: '#fff'
                },
                hAxis: {
                    gridlines: {
                        color: '#ddd'
                    },
                    textStyle: {
                        color: '#333'
                    }
                },
                vAxis: {
                    gridlines: {
                        color: '#ddd'
                    },
                    textStyle: {
                        color: '#333'
                    }
                },
                crosshair: {
                    color: '#999'
                },
                colors: ['#bbb'],
                annotations: {
                    textStyle: {
                        color: '#666'
                    }
                }
            },
            buttons: {
                selected: {
                    'background-color': '#eee',
                    'color': '#333'
                },
                other: {
                    'background-color': '#fff',
                    'color': '#333'
                }
            }
        },
        dark: {
            parent: {
                'background': '#000'
            },
            options: {
                titleTextStyle: {
                    color: '#ccc'
                },
                backgroundColor: '#111',
                chartArea: {
                    backgroundColor: '#111'
                },
                hAxis: {
                    gridlines: {
                        color: '#333'
                    },
                    textStyle: {
                        color: '#ccc'
                    }
                },
                vAxis: {
                    gridlines: {
                        color: '#333'
                    },
                    textStyle: {
                        color: '#ccc'
                    }
                },
                crosshair: {
                    color: '#ccc'
                },
                colors: ['#bbb'],
                annotations: {
                    textStyle: {
                        color: '#666'
                    }
                }
            },
            buttons: {
                selected: {
                    'background-color': '#666',
                    'color': '#ccc'
                },
                other: {
                    'background-color': '#444',
                    'color': '#ccc'
                }
            }
        }
    };

    var GCHART_CONFIGS = {
        hAxis: {
            textPosition: 'in',
            gridlines: {
                count: -1
            }
        },
        vAxis: {
            textPosition: 'in',
            gridlines: {
                count: 10
            }
        },
        legend: 'none',
        crosshair: { trigger: 'both' },
        chartArea: {width: '100%', height: '100%'},
        titlePosition: 'in', axisTitlesPosition: 'in',
        theme: 'material'
    };

    var CoinChart = function (parent) {
        var self = this;

        try{

        self.parent_element = parent;
        self.plot_element = parent.children('.coin-chart-plot');
        self.buttons_element = parent.children('.coin-chart-buttons');
        self.theme_obj = THEMES[parent.data('theme')] || THEMES.light;
        self.symbol = parent.data('symbol');
        self.chart = {
            google_chart: new google.visualization.AreaChart(self.plot_element[0]),
            config: jQuery.extend(true, {}, GCHART_CONFIGS, self.theme_obj.options),
            data: null,
            columns: [
                {type: 'date', label: 'Date'},
                {type: 'number', label: 'USD'}
            ],
            data_table: null,
            params: {
                interval: parent.data('window') || null
            }
        };
        self.old_dim = {
            width: self.plot_element.width(),
            height: self.plot_element.height()
        };

        self.setParentStyle();
        self.getAndUpdateChart();
        self.setButtonsActions();
        self.setDrawUpdate();
        }
        catch (e){

        }
    };

    CoinChart.prototype.setDrawUpdate = function () {
        var self = this;

        setInterval(function () {

            var current_dim = {
                width: self.plot_element.width(),
                height: self.plot_element.height()
            };

            if(current_dim.width != self.old_dim.width || current_dim.height != self.old_dim.height){
                self.old_dim = current_dim;
                self.drawChart();
            }
        },1000);
    };

    CoinChart.prototype.setParentStyle = function () {
        this.parent_element.css(this.theme_obj.parent);
    };

    CoinChart.prototype.setButtonsStyle = function () {
        var self = this;

        self.eachButton(function (btn,action) {
            var attrs = action == self.chart.params.interval ?
                self.theme_obj.buttons.selected : self.theme_obj.buttons.other;
            btn.css(attrs);
        });
    };

    CoinChart.prototype.eachButton = function (callback) {
        var buttons = this.buttons_element.children('.coin-button');

        buttons.each(function (i,button) {
            var btn_elem = jQuery(button);
            var action = btn_elem.data('action');
            callback && callback(btn_elem,action);
        })
    };

    CoinChart.prototype.setButtonsActions = function () {
        var self = this;
        self.eachButton(function (btn,action) {
            btn.click(function () {
                self.getAndUpdateChart(action);
            })
        });
    };

    CoinChart.prototype.getChartData = function (callback) {
        var self = this;
        var req = {
            type: 'POST',
            url: CChartsConstants.urls.ajax,
            data: {
                action: 'cc_chart_data',
                chart_options: {
                    symbol: self.symbol,
                    interval: self.chart.params.interval
                }
            }
        };

        jQuery.ajax(req).done(function (data) {
            if (data && data.points) {
                data.points.forEach(function (point, i) {
                    point[0] = new Date(point[0] * 1000);
                });

                self.chart.data = data;
                callback && callback();
            }
        });


    };

    CoinChart.prototype.drawChart = function () {
        var self = this;

        if(!self.chart.data || !self.chart.data.name || !self.chart.data_table)
            return;

        var configs = jQuery.extend(true, {}, {title: '\t'+self.chart.data.name + ' (USD)'}, self.chart.config);

        self.chart.google_chart.draw(
            self.chart.data_table,
            configs
        );
    };

    CoinChart.prototype.updateDataTable = function () {
        var self = this;

        if(!self.chart.data || !self.chart.data.points)
            return;

        var dt = new google.visualization.DataTable();
        self.chart.columns.forEach(function (col) {
            dt.addColumn(col);
        });
        dt.addRows(self.chart.data.points);
        self.chart.data_table = dt;

    };

    CoinChart.prototype.getAndUpdateChart = function (interval) {
        var self = this;

        self.chart.params.interval = interval || self.chart.params.interval || '7d';

        self.getChartData(function () {
            self.updateDataTable();
            self.drawChart();
        });

        self.setButtonsStyle();
    };


    jQuery.fn.extend({
        coinChart: function () {
            this.each(function () {
                new CoinChart(jQuery(this));
            });
        }
    });



    google.charts.load('current', {packages: ['corechart']});
    google.charts.setOnLoadCallback(function () {
        jQuery(function() {
            jQuery('.coin-chart').coinChart();
        });
    });


})(CChartsConstants,google,jQuery);
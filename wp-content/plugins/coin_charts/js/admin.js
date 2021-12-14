var cc_status_panel = jQuery('#cc-status-panel');


function ccResetPair(pair) {
    var req = {
        type: 'POST',
        url: CChartsAdmin.urls.ajax,
        data: {
            action: 'cc_reset_data',
            reset_target: pair
        }
    };

    jQuery.ajax(req).done(function (data) {
        ccUpdateTable();
    });
}

function ccUpdateTable() {
    var req = {
        type: 'POST',
        url: CChartsAdmin.urls.ajax,
        data: {
            action: 'cc_update_table'
        }
    };

    jQuery.ajax(req).done(function (data) {
        if (data && data.table) {

            var html = '';

            data.table.forEach(function (row) {
                html += '<tr><td>'+row.symbol+'</td>'+
                    '<td>'+row.name+'</td><td>'+row.last_update+'</td><td><div class="button button-primary button-large" onclick="ccResetPair(\''+row.symbol+'\')">Reset '+row.symbol+'</div></td></tr>'
            });

            jQuery('#cc-update-table-body').html(html);
        }
    });
}



if(cc_status_panel.length){
    ccUpdateTable();

    setInterval(function () {
        ccUpdateTable();
    },60000);
}

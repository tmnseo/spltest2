function fn_cp_change_statuses_update_timestamp(order_id, object)
{
    var new_timestamp = $(object).val();
    var url = $("input[name=redirect_url]").val();
    
    $.ceAjax('request', fn_url("cp_statuses_rules.update_timestamp"), {
        data: {
            new_timestamp: new_timestamp,
            order_id: order_id,
            result_ids: 'statuses_table',
            current_url: url
        },
        hidden: false,
        method: 'post',
        callback: function (response) {

        }
    });
}
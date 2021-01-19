(function(_, $){
    $(document).ready(function(){
        $('.cp-save').change(function(){
            fn_cp_update_order_data(this.value,this.id);
        });
    });

    $.ceEvent('on', 'ce.commoninit', function(context) {
        $('#cp_change_shipping_cost').click(function(){
            $('#cp_shipping_price_label').addClass('hidden');
            $('#cp_change_shipping_cost').addClass('hidden');
            $('#cp_shipping_price_input').removeClass('hidden');
            $('.cm-om-totals-recalculate').removeClass('hidden');
        });
    });

})(Tygh, Tygh.$);

function fn_cp_update_order_data(value,id)
{   var order_id = $("input[name='order_id']").val();
    var shipment_id = $("#cp_shipment_id").val();
    var shipping_id = $("#cp_shipping_id").val();
    var cp_data = {};
    cp_data[id] = value;

    $.ceAjax('request', fn_url('orders.update_details'), {
        data: {
            cp_data: cp_data,
            order_id: order_id,
            shipping_id: shipping_id,
            shipment_id: shipment_id
        },
        method: 'post'
    });
}

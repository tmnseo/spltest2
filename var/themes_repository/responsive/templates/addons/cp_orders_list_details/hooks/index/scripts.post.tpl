<script language="javascript">
    (function(_,$){
        $(document).on("click", ".cp-oc__get_details-click", function(){
            var order_id = $(this).attr('data-cp-orderid');
            var result_ids = $(this).attr('data-cp-result');
            var check_exist = $('#cp_order_details_exists_' + order_id);
            if (order_id && order_id.length > 0 && check_exist.length === 0) {
                $.ceAjax('request', fn_url('orders.cp_oc_get_details?order_id=' + order_id), {
                    method: 'get',
                    result_ids: result_ids
                });
            }
            var exist_icon = $(this).find('i');
            if (exist_icon.hasClass('ty-icon-down-open')) {
                exist_icon.removeClass('ty-icon-down-open');
                exist_icon.addClass('ty-icon-up-open');
                $('#' + result_ids).show();
            } else {
                exist_icon.removeClass('ty-icon-up-open');
                exist_icon.addClass('ty-icon-down-open');
                $('#' + result_ids).hide();
            }
        });
        $(document).on("click", ".cp-oc__expand_btns", function(){
            var order_id = $(this).attr('data-cp-orderid');
            $('#more_mobile_btns_' + order_id).show();
            $(this).remove();
        });
    })(Tygh,Tygh.$);
</script>
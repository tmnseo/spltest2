<div class="hidden" id="content_cp_ml_logs">
    <input class="hidden" id="val_order_id" value="{$order_info.order_id}" />
    {include file="addons/cp_megalog/components/order_logs.tpl"}
<!--content_cp_ml_logs--></div>

<script type="text/javascript">
    //<![CDATA[
        $.ceEvent('on', 'ce.update_object_status_callback', function() {
        
            var url = fn_url('orders.cp_update_order_logs?order_id=' + $("#val_order_id").val());
            $.ceAjax('request', url, {
                result_ids: 'cp_ml_order_logs'
            });
        });
    //]]>
</script>
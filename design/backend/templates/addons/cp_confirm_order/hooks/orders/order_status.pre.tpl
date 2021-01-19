{capture name="cp_confirmed"}
<div class="control-group" id="cp_confirmed_status_block">
    <div class="control-label">
        <h4 class="subheader" style="margin-top: 3px;">{__("cp_confirmed")}</h4>
    </div>
    <div class="controls">
        {include file="common/switcher.tpl"
            meta = "company-switch-storefront-status-button storefront__status"
            checked = $order_info.cp_confirm_status == 'Y'
            extra_attrs = [
                "data-ca-submit-url" => 'orders.update_confirm_status',
                "data-ca-storefront-id" => $order_info.order_id,
                "data-ca-opened-status" => 'Y',
                "data-ca-closed-status" => 'N',
                "data-ca-return-url" => "orders.details&order_id=`$order_info.order_id`"
            ]
        }
    </div>
</div>
{/capture}
<script type="text/javascript">
    if ($("#cp_confirmed_status_block").length == false){
        $(".orders-right-pane:first").prepend("{$smarty.capture.cp_confirmed|fn_cp_confirm_order_replace_text nofilter}");
    }
</script>
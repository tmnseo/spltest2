<td width="7%" class="center">
    {include file="common/switcher.tpl"
        meta = "company-switch-storefront-status-button storefront__status"
        checked = $o.cp_confirm_status == 'Y'
        extra_attrs = [
            "data-ca-submit-url" => 'orders.update_confirm_status',
            "data-ca-storefront-id" => $o.order_id,
            "data-ca-opened-status" => 'Y',
            "data-ca-closed-status" => 'N',
            "data-ca-return-url" => $extra_status
        ]
    }
</td>
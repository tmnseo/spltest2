{if $auth.user_type == 'V'}
<td class="row-status center" data-th="{__("cp_use_this_shipping")}">
    {include file="common/switcher.tpl"
        meta = "company-switch-storefront-status-button storefront__status"
        checked = $shipping.cp_use_this_shipping == 'Y'
        extra_attrs = [
            "data-ca-submit-url" => 'shippings.update_vendor_shippings',
            "data-ca-storefront-id" => $shipping.shipping_id,
            "data-ca-opened-status" => 'Y',
            "data-ca-closed-status" => 'N',
            "data-ca-return-url" => $return_url
        ]
    } 
</td>
{/if} 
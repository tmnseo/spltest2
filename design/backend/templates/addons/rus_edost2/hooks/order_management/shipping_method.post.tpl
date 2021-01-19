{if $product_groups}
    {foreach from=$product_groups key=group_key item=group}
        {if $group.shippings && !$group.shipping_no_required}
            {foreach from=$group.shippings item=shipping}
                {if $cart.chosen_shipping.$group_key == $shipping.shipping_id && $shipping.module == "edost2"}
					{include file="design/backend/templates/addons/rus_edost2/common/data.tpl" shipping=$group.chosen_shippings.$group_key mode="update"}
                {/if}
            {/foreach}
        {/if}
    {/foreach}
{/if}
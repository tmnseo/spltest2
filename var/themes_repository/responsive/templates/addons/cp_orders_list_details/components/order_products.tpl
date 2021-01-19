{if !$back_order_id}
    {$oc_order_id = $o.order_id}
{else}
    {$oc_order_id = $back_order_id}
{/if}
<div class="cp-orders-list-details" id="cp_orders_list_details_{$oc_order_id}">
    {if $cust_order_info}
        <span class="hidden" id="cp_order_details_exists_{$oc_order_id}">1</span>
        {if $cust_order_info.products}
            {foreach from=$cust_order_info.products item="ord_prod"}
                <div class="cp-oc__orders-item_bot_products">
                
                    <div class="cp-oc__orders-item_bot-column cp-oc__name">
                        <div class="cp-oc__orders-item_bot-column-title">{__("cp_oc_name")}</div>
                        <div><a href="{"products.view?product_id=`$ord_prod.product_id`"|fn_url}">{$ord_prod.product}</a></div>
                    </div>
                    <div class="cp-oc__orders-item_bot-column cp-oc__product-code">
                        <div class="cp-oc__orders-item_bot-column-title">{__("cp_ls_product_code")}</div>
                        <div>{$ord_prod.product_code}</div>
                    </div>
                    <div class="cp-oc__orders-item_bot-column cp-oc__price">
                        <div class="cp-oc__orders-item_bot-column-title">{__("price")}</div>
                        <div>{include file="common/price.tpl" value=$ord_prod.display_subtotal}</div>
                    </div>
                    <div class="cp-oc__orders-item_bot-column cp-oc__manufacturer">
                        <div class="cp-oc__orders-item_bot-column-title">{__("cp_oc_manufacturer")}</div>
                        <div>{$ord_prod.cp_oc_manufacturer}</div>
                    </div>
                    <div class="cp-oc__orders-item_bot-column cp-oc__quantity">
                        <div class="cp-oc__orders-item_bot-column-title">{__("quantity")}</div>
                        <div>{$ord_prod.amount}</div>
                    </div>
                    {hook name="cp_orders_list:product_data"}{/hook}
                </div>
            {/foreach}
        {else}
            <p class="ty-no-items">{__("text_no_products_found")}</p>
        {/if}
    {/if}
<!--cp_orders_list_details_{$oc_order_id}--></div>
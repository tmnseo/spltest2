<div class="cp-your-orders__item{if $order_completed} completed{/if}">
    <span class="cp-your-orders__item-title">
        {__("order")}&nbsp;{if count($carts) > 1 || $cp_completed_orders}№{$cp_order_number}{/if}
    </span>

    <span class="cp-your-orders__item-cost" data-ct-checkout-summary="order-total">
        {include file="common/price.tpl" value=$data.total}
    </span>

    <span class="cp-your-orders__item-product" data-ct-checkout-summary="items">
        <span  class="cp-your-orders__item-label">{__("products")}</span>
        <span  class="cp-your-orders__item-value">{include file="common/price.tpl" value=$data.display_subtotal}</span>
    </span>

     {if $data.taxes}
        {foreach from=$data.taxes item="tax"}
            <span class="cp-your-orders__item-tax" data-ct-checkout-summary="tax-name {$tax.description}">
                <span  class="cp-your-orders__item-label">{$tax.description}</span>
                <span  class="cp-your-orders__item-value">{include file="common/price.tpl" value=$tax.tax_subtotal}</span>
            </span>
        {/foreach}
    {/if}
    <span class="cp-your-orders__item-shipping" data-ct-checkout-summary="shipping">
        <span  class="cp-your-orders__item-label">{__("shipping")}</span>
        <span  class="cp-your-orders__item-value">{include file="common/price.tpl" value=$data.display_shipping_cost}</span>
    </span>
</div>
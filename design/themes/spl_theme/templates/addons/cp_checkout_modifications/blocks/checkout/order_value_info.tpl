<div class="order-value-info" id="checkout_info_summary_{$block.snapping_id}">
    {$taxes = $cart.taxes|current}
    <div class="order-value-info__cost" data-ct-checkout-summary="items">
        <span class="order-value-info__label">{__("cost_goods_order")}</span>
        <span class="order-value-info__value">{include file="common/price.tpl" value=$cart.display_subtotal}</span>
        <span class="order-value-info__tax">({__("including_vat_amount")} {include file="common/price.tpl" value=$taxes.applies.P}) </span>
    </div>

    <div class="order-value-info__delivery" data-ct-checkout-summary="shipping">
    {$chosen_shipping = $cart.chosen_shipping[0]}
        {if $cart.shipping.$chosen_shipping.service_code == "pickup"}
            <span class="order-value-info__label">
                {__("cost_of_delivery")}:
            </span>
        {else}
            <span id="sw_tooltip_{$block.block_id}" class="order-value-info__label cm-combination">
                    <span>{__("estimated_delivery_cost")}</span>
                    <span class="cp-tooltip">i</span>:
                    <span class="cp-tooltip-text_hover">{__("estimated_delivery_cost.tooltip")}</span>
            </span>
        {/if}
        <span class="order-value-info__value">
            {include file="common/price.tpl" value=$cart.display_shipping_cost}
        </span>
        <span class="order-value-info__tax">({__("including_vat_amount")} {include file="common/price.tpl" value=$taxes.applies.S})</span>
        <div id="tooltip_{$block.block_id}" class="cp-tooltip-box cp-tooltip-text_block hidden" >{__("estimated_delivery_cost.tooltip")}</div>
    </div>

    <div class="order-value-info__total-cost" data-ct-checkout-summary="order-total">
        <span class="order-value-info__label">
            {__("total_cost_order_including_delivery")}
        </span>
        <span class="order-value-info__value">{include file="common/price.tpl" value=$_total|default:$cart.total}</span>
        <span class="order-value-info__tax">({__("including_vat_amount")} {include file="common/price.tpl" value=$taxes.tax_subtotal})</span>
    </div>

<!--checkout_info_summary_{$block.snapping_id}--></div>
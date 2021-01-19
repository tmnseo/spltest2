{$order_number = 0}
{$total_cost_order = 0}
{$total_discount_value = 0}
<div id="checkout_info_summary_{$block.snapping_id}">
    <div class="cp-your-orders">
        {if $block.name}
            <h2 class="litecheckout__step-title">{$block.name}</h2>
        {/if}
        <div class="cp-your-orders__list">
            {if $cp_completed_orders}
                {$order_number = $cp_completed_orders|count}
                {foreach $cp_completed_orders as $order_id => $order_data}
                    {$total_cost_order = $total_cost_order + $order_data.total}
                    {$total_discount_value = $total_discount_value + $order_data.discount}
                    {include file="addons/cp_checkout_modifications/blocks/checkout/components/your_order_item.tpl" data=$order_data order_completed="Y" cp_order_number=$order_data.cp_checkout_order_number}
                {/foreach}
            {/if}
            {foreach $carts as $vendor_id => $vendor_cart}
                {$order_number = $order_number + 1}
                {$total_cost_order = $total_cost_order + $vendor_cart.total}
                {$total_discount_value = $total_discount_value + $vendor_cart.discount}
                {include file="addons/cp_checkout_modifications/blocks/checkout/components/your_order_item.tpl" data=$vendor_cart cp_order_number=$order_number}
            {/foreach}
        </div>
    </div>
{if count($carts) > 1 || $cp_completed_orders}    
    <div class="total-all-orders{if $total_discount_value} total-all-orders_discount{/if}">
        {if $total_discount_value}
            {$total_cost_without_discount = $total_discount_value + $total_cost_order}
            <div class="total-cost_without-discount">
                <span class="total-all-orders__label">{__("cost")}:</span>
                <span class="total-all-orders__value"> {include file="common/price.tpl" value=$total_cost_without_discount}</span>
            </div>
            <div class="total-cost_discount">
                <span class="total-all-orders__label">{__("discount")}:</span>
                <span class="total-all-orders__value"> -{include file="common/price.tpl" value=$total_discount_value}</span>
            </div>
            <div class="total-cost_order">
                <span class="total-all-orders__label">{__("total")}{if $cart.taxes && $settings.Appearance.cart_prices_w_taxes == "Y"} {__("cp_spl_theme.including_tax")}{/if}:</span>
                <span class="total-all-orders__value"> {include file="common/price.tpl" value=$total_cost_order}</span>
            </div>
        {else}
            <span class="total-all-orders__label">{__("total_all_orders")}{if $cart.taxes && $settings.Appearance.cart_prices_w_taxes == "Y"} {__("cp_spl_theme.including_tax")}{/if}:</span>
            <span class="total-all-orders__value"> {include file="common/price.tpl" value=$total_cost_order}</span>
        {/if}
    </div>
{/if}
<!--checkout_info_summary_{$block.snapping_id}--></div>
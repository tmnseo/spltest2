{capture name="buttons"}
    <div class="ty-float-left">
		{if $runtime.controller == "orders"}
			{$dispatch = "orders.search"}
			{$but_text = __("cp_complete")}
			<a class="ty-btn__secondary cm-notification-close ty-btn" href="{$dispatch|fn_url}" rel="nofollow">{$but_text}</a>
		{else}
			{$dispatch = "checkout.checkout"}
			{$but_text = __("continue")}
			{include file="buttons/button.tpl" but_text=$but_text but_meta="ty-btn__secondary cm-notification-close"}
		{/if}
    </div>
{/capture}
{capture name="info"}
    <div class="cp-order-popup__text clearfix">{__("cp_order_popup_info")}</div>
    {$group = $order_info.product_groups|current}
    {$shipping_data = $order_info.shipping|current}

	{include file="common/subheader.tpl" title=__("order")|cat:"№ `$order_info.order_id`"}

    <div class="cp-order-popup__info">
    	<div class="cp-order-popup__info-supplier">
    		<span class="cp-order-popup__label">{__("supplier")}:</span>
    		<span class="cp-order-popup__value">{$group.name}</span>
    	</div>
    	<div class="cp-order-popup__info-warehouse">
    		<span class="cp-order-popup__label">{__("cp_checkout_modifications_warehouse")}:</span>
    		<span class="cp-order-popup__value">{$warehouse_address}</span>
    	</div>
    	<div class="cp-order-popup__info-shipping">
    		<span class="cp-order-popup__label">{__("shipping_method")}:</span>
    		<span class="cp-order-popup__value">{$shipping_data.shipping}</span>
    	</div>
    </div>
   
    <div class="cp-order-popup__products">
    	<table class="cp-order-popup__products-table">
    	{foreach from=$order_info.products item='product_data'}
    		<tr class="cp-order-popup__products-item">
	    		<td class="cp-order-popup__product-name">{$product_data.product}</td>
	    		<td class="cp-order-popup__product-amount">{$product_data.amount}&nbsp;{__("items")}</td>
	    		<td class="cp-order-popup__product-price">{include file="common/price.tpl" value=$product_data.price}</td>
    		</tr>
    	{/foreach}
    	</table>
    </div>
    <div class="cp-order-popup__total">
    	<div class="cp-order-popup__total-subtotal">
    		<span class="cp-order-popup__label">{__("cp_checkout_modifications.order_total")}</span>
    		<span class="cp-order-popup__value">{include file="common/price.tpl" value=$order_info.subtotal}</span>
    	</div>
    	{if $order_info.taxes}
    		{$order_tax = 0}
    		{foreach from=$order_info.taxes item=tax_data}
    			{$order_tax = $order_tax + $tax_data.tax_subtotal}
    		{/foreach}
	    	<div class="cp-order-popup__total-taxes">
	    		<span class="cp-order-popup__label">{__("cp_checkout_modifications.taxes")}</span>
	    		<span class="cp-order-popup__value">{include file="common/price.tpl" value=$order_tax}</span>
	    	</div>
    	{/if}
    	<div class="cp-order-popup__total-shipping">
    		<span class="cp-order-popup__label">{__("cp_np_delivery_txt")}</span>
    		<span class="cp-order-popup__value">{include file="common/price.tpl" value=$order_info.shipping_cost}</span>
    	</div>
    	<div class="cp-order-popup__total-total">
    		<span class="cp-order-popup__label">{__("total")}</span>
    		<span class="cp-order-popup__value">{include file="common/price.tpl" value=$order_info.total}</span>
    	</div>
    </div>   
{/capture}
{include file="views/products/components/notification.tpl" product_buttons=$smarty.capture.buttons product_info=$smarty.capture.info notification_class=testick}
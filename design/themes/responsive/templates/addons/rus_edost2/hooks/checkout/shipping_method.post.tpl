{if $cart.chosen_shipping.$group_key == $shipping.shipping_id && $shipping.module == "edost2"}
	{include file="design/backend/templates/addons/rus_edost2/common/data.tpl" mode="checkout"}
{/if}
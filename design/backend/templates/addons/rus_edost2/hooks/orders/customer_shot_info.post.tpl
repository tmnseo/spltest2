{foreach from=$order_info.shipping item="shipping" key="shipping_id" name="f_shipp"}
	{if $shipping.module == "edost2"}
		{include file="design/backend/templates/addons/rus_edost2/common/data.tpl" mode="info"}
	{/if}
{/foreach}
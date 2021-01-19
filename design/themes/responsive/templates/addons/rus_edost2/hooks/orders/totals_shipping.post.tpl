{foreach from=$order_info.shipping item="shipping"}
	{if $shipping.module == "edost2"}
		{include file="design/backend/templates/addons/rus_edost2/common/data.tpl" mode="info" style="margin-top: 10px;"}
	{/if}
{/foreach}
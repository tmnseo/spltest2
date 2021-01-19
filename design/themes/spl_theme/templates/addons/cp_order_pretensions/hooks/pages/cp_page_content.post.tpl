{if $page.page_id == $addons.cp_order_pretensions.pretension_page_id}
<div class="pseudo-button_close hidden"><span class="icon-spl-close"></span></div>
<div class="order-pretension-popup__banner">
    <img src="images/banner/sofi_claim.png" width="391" height="247">
</div>
<div class="order-pretension-popup__text">{__("cp_order_pretensions.pretension_info", 
		[
			"[order_id]" => "{$order_info.order_id}",
			"[price]" => "{$order_info.total}",
			"[currency]" => "{$order_info.currency}",
			"[product_amount]" => "{$order_info.amount}",
			"[vendor]" => "{$order_info.company_name}"
		]
	)}
</div>
<div class="order-pretension-popup__text">{__("cp_order_pretensions.pretension_additional_info")}</div>
{/if}
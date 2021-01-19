{if $cp_allowed_pretension && in_array($o.status, $cp_allowed_pretension)}
	<div class="cp-oc__orders-item_top-link">
	    <a class="cm-dialog-opener cm-dialog-auto-size" href="{"pages.view?page_id={$addons.cp_order_pretensions.pretension_page_id}&order_id=`$o.order_id`"|fn_url}" data-ca-target-id="order_pretension_popup_{$o.order_id}" data-ca-dialog-class="order-pretension-popup">{__("cp_oc_order_pretension")}</a>
	</div>

	<div id="order_pretension_popup_{$o.order_id}" class="hidden">
	</div>
{/if}
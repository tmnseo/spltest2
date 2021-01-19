{if $o.cp_confirm_status == "Y"}
    <a class="cp-oc__orders-btns-item cm-confirm" href="{"orders.print_invoice?order_id=`$o.order_id`"|fn_url}" target="_blank">{__("print_invoice")}</a>
{/if}
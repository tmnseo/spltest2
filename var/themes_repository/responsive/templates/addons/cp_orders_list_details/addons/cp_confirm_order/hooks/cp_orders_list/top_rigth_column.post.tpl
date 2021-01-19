{if $o.cp_confirm_status == "Y"}
    <div class="cp-oc__orders-item_top-link">
        <a class="cm-confirm" href="{"orders.print_invoice?order_id=`$o.order_id`"|fn_url}" target="_blank">{__("print_invoice")}</a>
    </div>
{/if}
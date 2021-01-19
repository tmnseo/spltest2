{$order_number = 0}
<div class="litecheckout-progressbar__wrap" id="litecheckout_progressbar_reload">
    <div class="litecheckout-progressbar">
        {if $cp_completed_orders}
            {$order_number = $cp_completed_orders|count}
            {foreach $cp_completed_orders as $order_id => $order_data}
                <span class="litecheckout-progressbar__item">
                    {__("order")}&nbsp;{$order_data.cp_checkout_order_number}
                        &nbsp;<span class="icon-spl-check"></span>
                </span>
            {/foreach}
        {/if}

        {foreach $carts as $vendor_id => $vendor_cart}
            {$order_number = $order_number + 1}
            <span class="litecheckout-progressbar__item{if $vendor_cart.order_active || count($carts) == 1} active{/if}">
                {__("order")}&nbsp;{if count($carts) > 1 || $cp_completed_orders}{$order_number}{/if}
            </span>
        {/foreach}
    </div>
<!--litecheckout_progressbar_reload--></div>


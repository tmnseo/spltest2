{if $carts}
<div class="ty-checkout-complete__iv-pending-carts-notice">

    {if !$is_customer_want_to_place_al_orders}
        {__("direct_payments.pending_carts_notice", ["[cart_url]" => fn_url("checkout.place_all_orders")])}


      {else}
        {__("cp_direct_payments.pending_carts_notice", ["[cart_url]" => fn_url("checkout.place_all_orders")])}
    {/if}
</div>
{/if}
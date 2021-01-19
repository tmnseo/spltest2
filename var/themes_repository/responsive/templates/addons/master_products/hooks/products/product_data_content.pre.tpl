{if !$product.company_id}
    {if $show_add_to_cart}
        {$show_view_offers_btn=true scope=parent}
    {/if}

    {$show_old_price=false scope=parent}
    {$show_list_discount=false scope=parent}
    {$show_product_labels=false scope=parent}
    {$show_discount_label=false scope=parent}
    {$show_shipping_label=false scope=parent}
    {$show_product_amount=false scope=parent}
    {$show_add_to_cart=false scope=parent}
{/if}
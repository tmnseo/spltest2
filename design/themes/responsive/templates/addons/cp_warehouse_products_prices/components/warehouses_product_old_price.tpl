{if ($warehouse_data.base_price|floatval || $product.zero_price_action == "P" || $product.zero_price_action == "A" || (!$warehouse_data.base_price|floatval && $product.zero_price_action == "R"))}
    {assign var="show_price_values" value=true}

{else}
    {assign var="show_price_values" value=false}
{/if}

{if $show_price_values && $show_old_price && ($product.promotions || $product.list_discount || $warehouse_data.price != $warehouse_data.base_price)}
    <span class="ty-price_old cm-reload-{$obj_prefix}{$obj_id}" id="old_price_update_{$obj_prefix}{$obj_id}">
        {hook name="products:old_price"}
        {if $product.promotions || $warehouse_data.price != $warehouse_data.base_price}
            <span class="ty-list-price ty-nowrap" id="line_old_price_{$obj_prefix}{$obj_id}">{include file="common/price.tpl" value=$warehouse_data.base_price span_id="old_price_`$obj_prefix``$obj_id`" class="ty-list-price ty-nowrap"}</span>
        {elseif $product.list_discount}
            <span class="ty-list-price ty-nowrap" id="line_list_price_{$obj_prefix}{$obj_id}">{if $details_page}<span class="list-price-label">{__("list_price")}:</span> {/if}<span class="ty-strike">{include file="common/price.tpl" value=$warehouse_data.base_price span_id="list_price_`$obj_prefix``$obj_id`" class="ty-list-price ty-nowrap"}</span></span>
        {/if}
        {/hook}
    <!--old_price_update_{$obj_prefix}{$obj_id}--></span>
{/if}


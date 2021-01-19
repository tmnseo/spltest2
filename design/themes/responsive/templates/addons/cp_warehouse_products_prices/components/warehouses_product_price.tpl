{if ($warehouse_data.price|floatval || $product.zero_price_action == "P" || $product.zero_price_action == "A" || (!$warehouse_data.price|floatval && $product.zero_price_action == "R")) && !($settings.Checkout.allow_anonymous_shopping == "hide_price_and_add_to_cart" && !$auth.user_id)}
    {assign var="show_price_values" value=true}
{else}
    {assign var="show_price_values" value=false}
{/if}

<span class="cm-reload-{$obj_prefix}{$obj_id} ty-price-update" id="price_update_{$obj_prefix}{$obj_id}">
    <input type="hidden" name="appearance[show_price_values]" value="{$show_price_values}" />
    <input type="hidden" name="appearance[show_price]" value="{$show_price}" />
    {if $show_price_values} 
        {if $show_price}
            {if $warehouse_data.price|floatval || $product.zero_price_action == "P" || ($hide_add_to_cart_button == "Y" && $product.zero_price_action == "A")}
                <span class="ty-price{if !$warehouse_data.price|floatval && !$product.zero_price_action} hidden{/if}" id="line_discounted_price_{$obj_prefix}{$obj_id}">{include file="common/price.tpl" value=$warehouse_data.price span_id="discounted_price_`$obj_prefix``$obj_id`" class="ty-price-num" live_editor_name="product:price:{$product.product_id}" live_editor_phrase=$product.base_price}</span>
            {elseif $product.zero_price_action == "A" && $show_add_to_cart}
                {assign var="base_currency" value=$currencies[$smarty.const.CART_PRIMARY_CURRENCY]}
                <span class="ty-price-curency"><span class="ty-price-curency__title">{__("enter_your_price")}:</span>
                <div class="ty-price-curency-input">
                    {if $base_currency.after != "Y"}{$base_currency.symbol nofilter}{/if}
                    <input class="ty-price-curency__input" type="text" size="3" name="product_data[{$obj_id}][price]" value="" />
                    {if $base_currency.after == "Y"}{$base_currency.symbol nofilter}{/if}
                </div>
                </span>

            {elseif $product.zero_price_action == "R"}
                <span class="ty-no-price">{__("contact_us_for_price")}</span>
                {assign var="show_qty" value=false}
            {/if}
            {if $product.tax_ids}
                <span class="ty-list__clean">{__("cp_spl_theme.including_tax")}</span>
            {/if}
        {/if}
    {elseif $settings.Checkout.allow_anonymous_shopping == "hide_price_and_add_to_cart" && !$auth.user_id}
        <span class="ty-price">{__("sign_in_to_view_price")}</span>
    {/if}
<!--price_update_{$obj_prefix}{$obj_id}--></span>
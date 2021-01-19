<div class="cp-order-info" id="cp_checkout_info_products_{$block.snapping_id}">
    <h3 class="cp-order-info__heading cp-order-info_padding"> 
        {$block.name}
    </h3>
    
    <div class="cp-order-info__price cp-order-info_padding">
        <span>{__("cp_checkout_modifications.order_total")}</span> 
        <strong>{include file="common/price.tpl" value=$cart.subtotal}</strong>
    </div>

    {if $cart.taxes}
        {foreach from=$cart.taxes item="tax"}
        <div class="cp-order-info__price cp-order-info_padding">
            <span>{__("cp_checkout_modifications.taxes")} {if $tax.rate_type == 'P'}{$tax.rate_value|round}%{/if}</span> 
            <strong>{include file="common/price.tpl" value=$tax.tax_subtotal}</strong>
        </div>
        {/foreach}
    {/if}

    {$show_shipping_estimation = ($location != "cart" || $settings.Checkout.estimate_shipping_cost == "YesNo::YES"|enum)}
    {if $cart.shipping_required == true}
        <div class="cp-order-info__price cp-order-info_padding">
            <span>{__("shipping")}</span> 
            {if $cart.shipping}
                <strong>{include file="common/price.tpl" value=$cart.display_shipping_cost}</strong>
            {elseif $show_shipping_estimation}
                <strong>{$smarty.capture.shipping_estimation nofilter}</strong>
            {/if}
        </div>
    {/if}

    <div class="cp-order-info__price cp-order-info__price_total cp-order-info_padding">
        <span>{__("total")}</span> 
        <strong>{include file="common/price.tpl" value=$cart.total}</strong>
    </div>

    {foreach from=$product_groups key='group_id' item="group"}
        <div class="cp-order-info__vendor-info">
            <div class="cp-order-info__vendor">
                {__("vendor")}: <strong>{$group.name}</strong>
            </div>
            <div class="cp-order-info__warehouse">
                {__("cp_checkout_modifications_warehouse")}: <strong>{$warehouse_address}</strong>
            </div>
        </div>

        <div class="cp-order-info__amount-total cp-order-info_padding cm-combination open" id="sw_products_list_{$block.snapping_id}">
            <span>
                {__("cp_checkout_modifications_product_total")} 
                {foreach from=$group.package_info.packages item='package_info'}
                    <strong>{$package_info.amount} {__("items")}</strong>
                {/foreach}
            </span>
            <span class="cp-order-info__title-toggle">
                <i class="ty-icon-down-open"></i>
                <i class="ty-icon-up-open"></i>
            </span>
        </div>
    {/foreach}
    {* <div class="cp-order-info__price cp-order-info__price_bottom cp-order-info_padding">
        <span>{__("order_total")}</span> 
        <strong>{include file="common/price.tpl" value=$cart.total}</strong>
    </div> *}
    <div class="cp-order-info__product-list_wrapp" id="products_list_{$block.snapping_id}"> 
        <ul class="cp-order-info__product-list" >
        {foreach from=$cart_products key="key" item="product" name="cart_products"}
            {if !$cart.products.$key.extra.parent}
                <li class="cp-order-info__product-item">
                    <bdi><a class="cp-order-info__product-name" href="{"products.view?product_id=`$product.product_id`"|fn_url}">{$product.product nofilter}</a></bdi>
                    {if !$product.exclude_from_calculate}
                        {include file="buttons/button.tpl" but_href="checkout.delete?cart_id=`$key`&redirect_mode=`$runtime.mode`" but_meta="cp-order-info__item-delete delete" but_target_id="cart_status*" but_role="delete" but_name="delete_cart_item"}
                    {/if}
                    <span class="cp-order-info__product-info">
                        <span class="cp-order-info__product-price">
                            {$product.amount}&nbsp;x&nbsp;{include file="common/price.tpl" value=$product.display_price}
                        </span>
                        <span class="cp-order-info__product-specifications">
                            <span class="cp-order-info__product-feature">
                                <span class="label">{__("cp_checkout_modifications_manufacturer")}</span>
                                <span class="value">{$product.manufacturer}</span>
                            </span>
                            <span class="cp-order-info__product-feature">
                                <span class="label">{__("cp_checkout_modifications_manufacturer_code")}</span>
                                <span class="value">{$product.manufacturer_code}</span>
                            </span>
                        </span>
                    </span>
                    <span class="cp-order-info__product-options">
                        {include file="common/options_info.tpl" product_options=$product.product_options no_block=true}
                    </span>
                </li>
            {/if}
        {/foreach}
        </ul>
    </div>
    
<!--cp_checkout_info_products_{$block.snapping_id}--></div>
{script src="js/addons/cp_checkout_modifications/func_init_scroll.js"}

<div class="ty-compact-list__item">
    <form {if !$config.tweaks.disable_dhtml}class="cm-ajax cm-ajax-full-render"{/if} action="{""|fn_url}" method="post" name="short_list_form{$obj_prefix}">
        <input type="hidden" name="result_ids" value="cart_status*,wish_list*,account_info*" />
        <input type="hidden" name="redirect_url" value="{$config.current_url}" />
        <input type="hidden" name="product_data[{$product.product_id}][extra][warehouse_id]" value="{$warehouse_data.warehouse_id}"/>
        <div class="ty-compact-list__content">
            <div class="ty-compact-list__image">
                <a href="{"products.view?product_id=`$product.product_id`&warehouse_id=`$key`"|fn_url}">
                    {include file="common/image.tpl" image_width=$image_width image_height=$image_height images=$product.main_pair obj_id=$obj_id_prefix}
                </a>
                {assign var="product_labels" value="product_labels_`$obj_prefix``$obj_id`"}
                {$smarty.capture.$product_labels nofilter}
            </div>
            <div class="ty-compact-list__title">
                {if $show_name}
                    {if $hide_links}<strong>{else}<a href="{"products.view?product_id=`$product.product_id`&warehouse_id=`$warehouse_data.warehouse_id`"|fn_url}" class="product-title" title="{$product.product|strip_tags}" {live_edit name="product:product:{$product.product_id}" phrase=$product.product}>{/if}{$product.product nofilter}{if $hide_links}</strong>{else}</a>{/if}
                {elseif $show_trunc_name}
                    {if $hide_links}<strong>{else}<a href="{"products.view?product_id=`$product.product_id`&warehouse_id=`$warehouse_data.warehouse_id`"|fn_url}" class="product-title" title="{$product.product|strip_tags}" {live_edit name="product:product:{$product.product_id}" phrase=$product.product}>{/if}{$product.product|truncate:44:"...":true nofilter}{if $hide_links}</strong>{else}</a>{/if}
                {/if}
                <div class="wrapp">
                    <div class="brand">
                        {foreach from=$product_features_cp item="features"}
                            {if $features.feature_id == $addons.cp_editing_a_product_block.brand}
                                <span class="label">{$features.description}:</span>
                                <span class="name">{$features.prefix}{$features.variant}</span>
                            {/if}
                        {/foreach}
                    </div>
                    {if $product.weight}
                        <div class="brand">
                            <span class="label">{__("weight")}:&nbsp;</span>
                            <span class="name">{$product.weight}</span>
                        </div>
                    {/if}
                </div>
            </div>
            <div class="ty-compact-list__controls">
                <div class="ty-compact-list__manufacturer">
                    <span class="title">{__("yml2_offer_feature_common_vendor")}</span>
                    {foreach from=$product_features_cp item="features"}
                        {if $features.feature_id == $addons.cp_editing_a_product_block.manufacturer}
                            <span class="company">{$features.variant}</span>
                        {/if}
                    {/foreach}
                    {foreach from=$product_features_cp item="features"}
                        {if $features.feature_id == $addons.cp_editing_a_product_block.producing_country}
                            <span class="country-origin">{$features.description} 
                                <span class="country">{$features.variant}</span>
                            </span>
                        {/if}
                    {/foreach}
                </div>

                <div class="ty-compact-list__manufacturer-code">
                    <span class="title">{__("cp_manufacturer_code")}</span>
                    {foreach from=$product_features_cp item="features"}
                        {if $features.feature_id == $addons.cp_editing_a_product_block.manufacturer_code}
                            {if $features.variant}
                                <span class="country">{$features.variant}</span>
                            {/if}
                        {/if}
                    {/foreach}
                </div>

                <div class="ty-compact-list__vendor">
                    <span class="title">{__("vendor")}</span>
                    <a href="{"companies.products?company_id=`$product.company_id`"|fn_url}" class="company">{$product.company_name}</a>
                    {if $company_data.city}
                        <span class="city-vendor">{__("city")}
                            <span class="city">{$warehouse_data.warehouse_city}</span>
                        </span>
                    {else}
                        {foreach from=$product_features_cp item="features"}
                            {if $features.feature_id == $addons.cp_editing_a_product_block.city}
                                <span class="city-vendor">{__("city")}
                                    <span class="city">{$features.variant}</span>
                                </span>
                            {/if}
                        {/foreach}
                    {/if}
                </div>
                <div class="ty-compact-list__amount">
                    <span class="title">{__("qty")}</span>
                    {if !$smarty.capture.capt_options_vs_qty}
                        {assign var="product_options" value="product_options_`$obj_id`"}
                        {$smarty.capture.$product_options nofilter}

                        {include file="addons/cp_warehouse_products_prices/components/warehouses_qty.tpl"}
                    {/if}
                    <span class="balance">{__("stock_balance")} <span class="balance-amount">{$warehouse_data.amount}</span></span>
                </div>
                <div class="ty-compact-list__price">
                    <span class="title">{__("cp_cost_of_one")}</span>
                    <div class="wrapp">
                        <div class="price_cp">
                            {*assign var="old_price" value="old_price_`$obj_id`"}
                            {if $smarty.capture.$old_price|trim}
                                {$smarty.capture.$old_price nofilter}
                            {/if*}
                            {include file="addons/cp_warehouse_products_prices/components/warehouses_product_old_price.tpl"}
                            {include file="addons/cp_warehouse_products_prices/components/warehouses_product_price.tpl"}

                            {assign var="clean_price" value="clean_price_`$obj_id`"}
                            {$smarty.capture.$clean_price nofilter}
                        </div>
                        <div class="ty-compact-list__buttons">
                            {* <span class="title"></span> 
                            {if $show_add_to_cart}
                                {assign var="add_to_cart" value="add_to_cart_`$obj_id`"}
                                {$smarty.capture.$add_to_cart nofilter}
                            {/if}
                            *}
                            {include file="addons/cp_warehouse_products_prices/components/warehouses_add_to_cart_button.tpl"}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
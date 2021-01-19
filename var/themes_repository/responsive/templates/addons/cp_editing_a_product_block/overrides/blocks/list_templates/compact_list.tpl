{if $products}

    {script src="js/tygh/exceptions.js"}

    {if !$no_pagination}
        {include file="common/pagination.tpl"}
    {/if}

    {if !$no_sorting}
        {include file="views/products/components/sorting.tpl"}
    {/if}

    {assign var="image_width" value=$image_width|default:60}
    {assign var="image_height" value=$image_height|default:60}

    <div class="ty-compact-list ty-compact-list_cp">
        <div class="ty-compact-list__controls ty-compact-list__controls_header">
            <div class="ty-compact-list__product">
                <span class="title">{__("product")}</span>
            </div>
            <div class="ty-compact-list__controls">
                <div class="ty-compact-list__manufacturer">
                    <span class="title">{__("yml2_offer_feature_common_vendor")}</span>
                </div>
                <div class="ty-compact-list__manufacturer-code">
                    <span class="title">Kод производителя</span>
                </div>
                <div class="ty-compact-list__vendor">
                    <span class="title">{__("vendor")}</span>
                </div>
                <div class="ty-compact-list__amount">
                    <span class="title">{__("availability")}</span>
                </div>
                <div class="ty-compact-list__price">
                    <span class="title">{__("price")}</span>
                </div>
            </div>
        </div>
        {foreach from=$products item="product" key="key" name="products"}
        {$company_data = $product.company_id|fn_get_company_data}
        {assign var="company_country_cp" value=$company_data.country|default:"RU"}
            {assign var="obj_id" value=$product.product_id}
            {assign var="obj_id_prefix" value="`$obj_prefix``$product.product_id`"}
            {assign var="product_features_cp" value=$product|fn_get_product_features_list}
            {include file="common/product_data.tpl" product=$product show_descr=true}
            {hook name="products:product_compact_list"}
                <div class="ty-compact-list__item">
                {* {$product_features_cp|fn_print_r} *}
                    <form {if !$config.tweaks.disable_dhtml}class="cm-ajax cm-ajax-full-render"{/if} action="{""|fn_url}" method="post" name="short_list_form{$obj_prefix}">
                        <input type="hidden" name="result_ids" value="cart_status*,wish_list*,account_info*" />
                        <input type="hidden" name="redirect_url" value="{$config.current_url}" />
                        <div class="ty-compact-list__content">
                            <div class="ty-compact-list__image">
                                <a href="{"products.view?product_id=`$product.product_id`"|fn_url}">
                                    {include file="common/image.tpl" image_width=$image_width image_height=$image_height images=$product.main_pair obj_id=$obj_id_prefix}
                                </a>
                                {assign var="product_labels" value="product_labels_`$obj_prefix``$obj_id`"}
                                {$smarty.capture.$product_labels nofilter}
                            </div>
                            <div class="ty-compact-list__title">
                                {assign var="name" value="name_$obj_id"}<bdi>{$smarty.capture.$name nofilter}</bdi>
                                {* {assign var="prod_descr" value="prod_descr_$obj_id"}<bdi>{$smarty.capture.$prod_descr nofilter}</bdi> *}
                                <div class="wrapp">
                                    {* {$sku = "sku_`$obj_id`"}
                                    {$smarty.capture.$sku nofilter} *}
                                    <div class="brand">
                                        {foreach from=$product_features_cp item="features"}
                                            {if $features.description == "Бренд"}
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
                                        {if $features.description == "Производитель"}
                                            <span class="company">{$features.variant}</span>
                                        {/if}
                                        {if $features.description == "Страна производитель"}
                                            <span class="country-origin">{$features.description} 
                                                <span class="country">{$features.variant}</span>
                                            </span>
                                            
                                        {/if}
                                    {/foreach}
                                </div>

                                <div class="ty-compact-list__manufacturer-code">
                                    <span class="title">Kод производителя</span>
                                    {foreach from=$product_features_cp item="features"}
                                        {if $features.description == "Код производителя"}
                                            {if $features.variant}
                                                <span class="country">{$features.variant}</span>
                                            {/if}
                                        {/if}
                                    {/foreach}
                                </div>

                                <div class="ty-compact-list__vendor">
                                    <span class="title">{__("vendor")}</span>
                                    <a href="{"companies.products?company_id=`$product.company_id`"|fn_url}" class="company">{$product.company_name}</a>
                                    {foreach from=$product_features_cp item="features"}
                                        {if $features.description == "Город"}
                                            <span class="city-vendor">{__("city")}
                                                <span class="city">{$features.variant}</span>
                                            </span>
                                        {/if}
                                    {/foreach}
                                </div>
                                <div class="ty-compact-list__amount">
                                    <span class="title">Кол-во</span>
                                    {if !$smarty.capture.capt_options_vs_qty}
                                        {assign var="product_options" value="product_options_`$obj_id`"}
                                        {$smarty.capture.$product_options nofilter}

                                        {assign var="qty" value="qty_`$obj_id`"}
                                        {$smarty.capture.$qty nofilter}
                                    {/if}
                                    <span class="balance">{__("stock_balance")} <span class="balance-amount">{$product.amount}</span></span>
                                </div>
                                <div class="ty-compact-list__price">
                                    <span class="title">Стоимость 1шт</span>
                                    <div class="wrapp">
                                        <div class="price_cp">
                                            {assign var="old_price" value="old_price_`$obj_id`"}
                                            {if $smarty.capture.$old_price|trim}
                                                {$smarty.capture.$old_price nofilter}
                                            {/if}

                                            {assign var="price" value="price_`$obj_id`"}
                                            {$smarty.capture.$price nofilter}

                                            {assign var="clean_price" value="clean_price_`$obj_id`"}
                                            {$smarty.capture.$clean_price nofilter}
                                        </div>
                                        <div class="ty-compact-list__buttons">
                                            {* <span class="title"></span> *}
                                            {if $show_add_to_cart}
                                                {assign var="add_to_cart" value="add_to_cart_`$obj_id`"}
                                                {$smarty.capture.$add_to_cart nofilter}
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            {/hook}
        {/foreach}
    </div>

{if !$no_pagination}
    {include file="common/pagination.tpl" force_ajax=$force_ajax}
{/if}

{/if}
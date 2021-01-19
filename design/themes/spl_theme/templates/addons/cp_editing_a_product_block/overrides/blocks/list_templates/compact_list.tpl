{if $products}
    {script src="js/tygh/exceptions.js"}
    <div class="cp-compact-list__header hidden-phone">
        <div class="cp-compact-list__name">{__("product")}</div>
        <div class="cp-compact-list__manufacturer">{__("cp_np_manufacturer")}</div>
        <div class="cp-compact-list__manufacturer-code">{__("cp_ls_product_code")}</div>
        <div class="cp-compact-list__quantity">
            <a class="cm-ajax" href="{"`$c_url`&cp_np_sorting_run=1&cp_np_type=A&cp_np_sort_by=cp_np_amount&sort_order=`$o_search.sort_order_rev`"|fn_url}" data-ca-target-id="products_search_np_product_other_block">
                {__("quantity")}{if $o_search.cp_np_sort_by == "cp_np_amount"}<i class="icon-spl-{if $o_search.sort_order_rev == "asc"}reverse-sort{else}sort{/if}"></i>{/if}
            </a>
        </div>
        <div class="cp-compact-list__price">
            <a class="cm-ajax" href="{"`$c_url`&cp_np_sorting_run=1&cp_np_type=C&cp_np_sort_by=cp_np_price&sort_order=`$o_search.sort_order_rev`"|fn_url}" data-ca-target-id="products_search_np_product_other_block">
                {__("price")}&nbsp;{$currencies.$secondary_currency.symbol nofilter} {if $o_search.cp_np_sort_by == "cp_np_price"}<i class="icon-spl-{if $o_search.sort_order_rev == "asc"}reverse-sort{else}sort{/if}"></i>{/if}
            </a>
        </div>
        <div class="cp-compact-list__button">&nbsp;</div>
    </div>
    
    {if !$no_pagination}
        {include file="common/pagination.tpl"}
    {/if}

   {* {if !$no_sorting}
        {include file="views/products/components/sorting.tpl"}
    {/if} *}

    {assign var="image_width" value=$image_width|default:60}
    {assign var="image_height" value=$image_height|default:60}

    <div class="ty-compact-list cp-compact-list">
        
        <div class="cp-compact-list__body">
            {foreach from=$products item="product" key="key" name="products"}
                {assign var="obj_id" value=$product.product_id}
                {assign var="obj_id_prefix" value="`$obj_prefix``$product.product_id`"}

                {$company_data = $product.company_id|fn_get_company_data}
                {assign var="company_country_cp" value=$company_data.country|default:"RU"}

                {assign var="features" value=$product|fn_get_product_features_list}
                {include file="common/product_data.tpl" product=$product show_descr=true}

                {if $addons.cp_editing_a_product_block.manufacturer}
                    {$manuf_feat_id=$addons.cp_editing_a_product_block.manufacturer}
                {/if}

                {if $addons.cp_editing_a_product_block.manufacturer_code}
                    {$manuf_code_feat_id=$addons.cp_editing_a_product_block.manufacturer_code}
                {/if}

                {hook name="products:product_compact_list"}
                    {*addons/cp_warehouse_products_prices/components/warehouses_compact_list.tpl*}
                    {* <div class="ty-compact-list__item">
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
                                                <span class="city">{$company_data.city}</span>
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

                                            {assign var="qty" value="qty_`$obj_id`"}
                                            {$smarty.capture.$qty nofilter}
                                        {/if}
                                        <span class="balance">{__("stock_balance")} <span class="balance-amount">{$product.amount}</span></span>
                                    </div>
                                    
                                    <div class="ty-compact-list__price">
                                        <span class="title">{__("cp_cost_of_one")}</span>
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
                    </div> *}
                {/hook}
            {/foreach}
        </div>
    </div>

{if !$no_pagination}
    {include file="common/pagination.tpl" force_ajax=$force_ajax}
{/if} 

{/if}
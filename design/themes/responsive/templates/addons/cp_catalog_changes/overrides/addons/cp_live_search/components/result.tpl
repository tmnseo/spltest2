<div id="live_reload_box{if $search_input_id}{$search_input_id}{/if}">
    {if $search.cp_live_search}
        {assign var="cp_query_params" value=""}
        {if $search_id}
            {$cp_query_params="&search_id=`$search_id`"}
        {/if}
        <div class="live-search-box cm-popup-box">
            <div class="live-search-content" {if $addons.cp_live_search.popup_mheight}style="max-height: {$addons.cp_live_search.popup_mheight};"{/if}>
                {if $suggestions}
                    <div class="live-section live-suggestion">
                        <div class="cp-ls-header"> {__("cp_related_searches")}</div>
                        <ul class="live-suggestion">
                            {foreach from=$suggestions item=suggestion}
                                <li class="cp-ls-section-li">
                                    <a onclick="fill_live_input(this, '{$suggestion}');">{$suggestion}</a>
                                </li>
                            {/foreach}
                        </ul>
                    </div>
                {elseif $mean_words && !$live_result}
                    <div class="live-section live-suggestion">
                    <div class="cp-ls-header">{__("cp_ls_mb_mean")}</div>
                        <ul class="live-suggestion">
                            {foreach from=$mean_words item=word}
                                <li class="cp-ls-section-li">
                                    <a onclick="fill_live_input(this, '{$word}');">{$word}</a>
                                </li>
                            {/foreach}
                        </ul>
                    </div>
                {/if}
                {if $brands}
                    <div class="live-section live-brands">
                        <div class="cp-ls-header"> {__("cp_brands")}</div>
                        <ul>
                            {foreach from=$brands item=brand}
                            <li class="cp-ls-section-li">
                                <a href="{"product_features.view&variant_id=`$brand.variant_id`"|fn_url}">
                                    {$brand.variant}
                                </a>
                            </li>
                            {/foreach}
                        </ul>
                    </div>
                {/if}
                {if $categories}
                    <div class="live-section live-categories">
                        <div class="cp-ls-header">{__("categories")}</div>
                        <ul>
                            {foreach from=$categories item=category}
                            <li class="cp-ls-section-li">
                                <a href="{"categories.view&category_id=`$category.category_id`"|fn_url}">
                                    {$category.category}
                                </a>
                            </li>
                            {/foreach}
                        </ul>
                    </div>
                {/if}
                {if $vendors}
                    <div class="live-section live-vendors">
                        <div class="cp-ls-header">{__("vendors")}</div>
                        <ul>
                            {foreach from=$vendors item=vendor}
                            <li class="cp-ls-section-li">
                                <a href="{"companies.products&company_id=`$vendor.company_id`"|fn_url}">
                                    {$vendor.company}
                                </a>
                            </li>
                            {/foreach}
                        </ul>
                    </div>
                {/if}
                {if $f_products}
                    <div class="live-section live-featured-products">
                        <div class="cp-ls-header">{__("cp_featured_products")}</div>
                        {include file="addons/cp_live_search/components/products_list.tpl" live_result=$f_products category_names=[] search_prefix="_recomended"}
                    </div>
                {/if}
                
                {if $cp_is_best_offer && $addons.cp_live_search.show_thumbnails == "Y"}
                    <a class="cp-ls__best-offer"  href="{"products.view?product_id=`$cp_is_best_offer.product_id`&warehouse_id=`$cp_is_best_offer.cp_wh_id`"|fn_url}">
                        {if $cp_is_best_offer.main_pair}
                        <div class="cp-ls__best-offer_image">
                            {include file="common/image.tpl" 
                                images=$cp_is_best_offer.main_pair 
                                image_width=$addons.cp_live_search.thumbnails_width|default:50
                                image_height=$addons.cp_live_search.thumbnails_height|default:50
                            }
                        </div>
                        {/if}
                        <div class="cp-ls__best-offer_item">
                            <div class="cp-ls__best-offer_item-name">
                                <span>{$cp_is_best_offer.product nofilter}</span>
                                {if $cp_is_best_offer.cp_np_manuf_code}
                                    <span class="live-product-name">{$cp_is_best_offer.cp_np_manuf_code}</span>
                                {/if}
                            </div>
                            <div class="cp-np__best-offer-label">
                                <span>{__("cp_pg_best_offer")}</span>
                                {if $cp_is_best_offer.is_analog == true && !$cp_is_best_offer.is_pname_search}
                                    <span>{__("cp_analog")}</span>
                                {/if}
                            </div>
                            {if $cp_is_best_offer.cp_wh_id && $cp_is_best_offer.extra_warehouse_data}
                                {$bo_wh_id=$cp_is_best_offer.cp_wh_id}
                                {if $cp_is_best_offer.extra_warehouse_data.$bo_wh_id}
                                    {$bo_price = $cp_is_best_offer.extra_warehouse_data.$bo_wh_id.price}
                                    {$bo_base_price = $cp_is_best_offer.extra_warehouse_data.$bo_wh_id.base_price}
                                    {$bo_qty = $cp_is_best_offer.extra_warehouse_data.$bo_wh_id.amount}
                                    {$bo_promotions = $cp_is_best_offer.extra_warehouse_data.$bo_wh_id.promotions}
                                {/if}
                            {/if}
                            {if $bo_price}
                                <div class="cp-ls__best-offer_item-data cp-ls__best-offer_item-data_price">
                                    <span class="cp-ls__best-offer_item-data_label">{__("cp_cc_price_from")}:</span>
                                    <span class="cp-ls__best-offer_item-data_val">
                                        {if $bo_promotions}
                                            <span class="ty-price_old">
                                                {include file="common/price.tpl" value=$bo_base_price}
                                            </span>
                                        {/if}
                                        <span class="ty-price_real">
                                            {include file="common/price.tpl" value=$bo_price}
                                        </span>
                                    </span>
                                </div>
                            {/if}
                            {if $bo_qty}
                                <div class="cp-ls__best-offer_item-data">
                                    <span class="cp-ls__best-offer_item-data_label">{__("in_stock")}:</span>
                                    <span class="cp-ls__best-offer_item-data_val">{$bo_qty}</span>
                                </div>
                            {/if}
                        </div>
                    </a>
                {elseif $cp_is_no_results}
                    {if $is_pname_search}
                        <div class="ty-no-items cp-ls-no-items">{__("text_no_products_found")}</div>
                    {else}
                        <div class="ty-no-items cp-ls-no-items">{__("cp_cc_article_not_found")}</div>
                    {/if}
                {/if}
            
                {if $live_result}
                    <div class="live-section live-products">
                        <div class="cp-ls-header">
                            {if $cp_is_no_results}{__("cp_cc_you_found_before")}{else}{__("cp_cc_similar_products")}{/if} {if $search.items_per_page}
                            <span class="cp-ls-header-total">{$search.total_items} {__("cp_ls_product_codes")}</span>{/if}
                        </div>
                        {include file="addons/cp_live_search/components/products_list.tpl"}
                    </div>
                {else}
                    <div class="live-section">
                        <div class="cp-ls-header">{__("cp_cc_similar_products")} {if $search.items_per_page}
                            <span class="cp-ls-header-total">{$search.total_items} {__("cp_ls_product_codes")}</span>{/if}
                        </div>
                        <div class="ty-no-items cp-ls-no-items">{__("cp_cc_similar_prod_not_found")}</div>
                    </div>
                {/if}
            </div>
            {if $live_result}
                {assign var="total_pages" value="`$search.total_items / $search.items_per_page`"|ceil}
                <div class="live-bottom-buttons clearfix" id="live_search_buttons">
                    {assign var="search_dispatch" value="products.search"}
                    {if "MULTIVENDOR"|fn_allowed_for && $company_id}
                        {$search_dispatch = "companies.products&company_id=`$company_id`"}
                    {/if}
                    {* cp_catalog_changes: hide show all button *}
                    {*
                    <a class="cp-ls-view-all {if $total_pages <= 1}cp-ls-full{/if}" href="{"`$search_dispatch`&q=`$search.q`&subcats=Y&status=A&pshort=`{$addons.cp_live_search.search_in_short_description}`&pfull=`{$addons.cp_live_search.search_in_full_description}`&pname=Y&pkeywords=`{$addons.cp_live_search.search_in_keywords}`&search_performed=Y`$extra_req`"|fn_url}">{__("cp_view_all")}</a>
                    *}
                    {* cp_catalog_changes:end *}
                    {if $total_pages > 1}
                        <a class="cp-ls-load-more cm-ls-load-more" data-ca-page="{$search.page + 1}" data-ca-total-pages="{$total_pages}" data-ca-href="{"products.cp_live_search&q=`$search.q``$cp_query_params`"|fn_url}" data-ca-target-id="live_search_products{if $search_input_id}{$search_input_id}{/if}" data-ca-input-id="{if $search_input_id}{$search_input_id}{/if}">{__("cp_load_more")}</a>
                    {/if}
                </div>
            {/if}
        </div>
    {/if}
<!--live_reload_box{if $search_input_id}{$search_input_id}{/if}--></div>

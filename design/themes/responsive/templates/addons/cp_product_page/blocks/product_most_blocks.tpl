{** block-description:block_cp_np_most_blocks **}

{if $items}
    {$block_res_ids = "product_filters_*,products_search_*,category_products_*,product_features_*,breadcrumbs_*,currencies_*,languages_*,selected_filters_*"}
    {if $addons.cp_product_page.brand_id}
        {$manuf_feat_id=$addons.cp_product_page.brand_id}
    {/if}
    {if $addons.cp_product_page.brand_country_id}
        {$manuf_country_feat_id=$addons.cp_product_page.brand_country_id}
    {/if}
    
    {$cp_similar_cheap_block = false}

    {if $items.most_deliv && $items.most_cheap} 
        {if $items.most_deliv.product_id == $items.most_cheap.product_id 
            && $items.most_deliv.cp_fast_warehouse_id == $items.most_cheap.cp_lowest_warehouse_id}
            {$cp_similar_cheap_block = true}
        {/if}
    {/if}

    <h3 class="cp-np-mosts__1st-line_phone hidden">{__("alternative_options")}</h3>
    <div class="cp-np-mosts__1st-line" id="products_search_np_product_cheap_blocks">
    {if $items.most_deliv || $items.most_cheap} 
        {if $items.most_cheap}

            {if $cp_similar_cheap_block}
                {$cp_similar_cheap_block_alredy_view = true}
            {/if}

            {$items_warehouse_id = $items.most_cheap.cp_wh_id}
            {$items_price = $items.most_cheap.extra_warehouse_data.$items_warehouse_id.price}
            {$items_base_price = $items.most_cheap.extra_warehouse_data.$items_warehouse_id.base_price}
            {$promotions = $items.most_cheap.extra_warehouse_data.$items_warehouse_id.promotions}
            <div class="cp-np-mosts__1st-line_item">

                <div class="cp-np-mosts__1st-line_title {if $cp_similar_cheap_block}hidden{/if}">
                    <h3>{__("cp_np_find_cheaper")}</h3>
                </div>
                <div class="cp-np-mosts__1st-line_card{if $cp_similar_cheap_block} height-auto{/if}">
                    <div class="cp-np-mosts__1st-line_label">
                        {if !$cp_similar_cheap_block}
                            <span>{__("cp_pg_low_price")}</span>
                        {/if}
                        {if $cp_block_bo_prod_id && $cp_block_bo_prod_id == $items.most_cheap.product_id && $cp_block_bo_wh_id == $items.most_cheap.cp_lowest_warehouse_id}
                            <span>{__("cp_pg_best_offer")}</span>
                        {/if}
                    </div>
                    <div class="cp-np-mosts__1st-line_name">
                        <a class="cp-np__switch-from-block" data-ca-target-id="{$block_res_ids}" href="{"products.view?product_id=`$items.most_cheap.product_id`&warehouse_id=`$items.most_cheap.cp_lowest_warehouse_id`"|fn_url}">{$items.most_cheap.product}</a>
                    </div>
                    <div class="cp-np-mosts__1st-line_price">
                        {if $promotions}
                            <span class="ty-price_old hidden">
                                {include file="common/price.tpl" value=$items_base_price class="ty-list-price"}
                            </span>
                        {/if}
                        <span class="ty-price_real">
                            {include file="common/price.tpl" value=$items_price class="ty-nowrap"}
                        </span>
                    </div>
                    {if $manuf_feat_id && $items.most_cheap.product_features && $items.most_cheap.product_features.$manuf_feat_id}
                        <div class="cp-np-mosts__1st-line_data">
                            <span class="cp-np-mosts__1st-line_data-label">{$items.most_cheap.product_features.$manuf_feat_id.description}:</span><span class="cp-np-mosts__1st-line_data-value">{$items.most_cheap.product_features.$manuf_feat_id.variant}</span>
                            {if $manuf_country_feat_id && $items.most_cheap.product_features && $items.most_cheap.product_features.$manuf_country_feat_id} 
                                ({$items.most_cheap.product_features.$manuf_country_feat_id.variant})
                            {/if}
                        </div>
                    {/if}
                    {if $smarty.session.cp_user_has_defined_city}
                        <div class="cp-np-mosts__1st-line_data">
                            <span class="cp-np-mosts__1st-line_data-label">{__("cp_np_delivery_txt")}:</span><span class="cp-np-mosts__1st-line_data-value">{$items.most_cheap.cp_lowest_delivery}</span>
                        </div>
                    {/if}
                    <div class="cp-np-mosts__1st-line_data">
                        <span class="cp-np-mosts__1st-line_data-label">{__("city")}:</span><span class="cp-np-mosts__1st-line_data-value">{$items.most_cheap.cp_lowest_city}</span>
                    </div>
                    <div class="cp-np-mosts__1st-line_data cp-np__top-padding">
                        <span class="cp-np-mosts__1st-line_data-label">{__("vendor")}:</span>
                        <span class="cp-np-mosts__1st-line_data-value">
                            {* {if !$auth.user_id}
                                {__("available_after")} <a href="{"profiles.add"|fn_url}" rel="nofollow">{__("cp_spl.registry")}</a>
                            {else} *}
                                <a href="{"companies.products?company_id=`$items.most_cheap.company_id`"|fn_url}">
                                    {$items.most_cheap.company_name}
                                </a>
                            {* {/if} *}
                        </span>
                    </div>
                    <div class="cp-np-mosts__1st-line_arrow">
                        <a class="cp-np__switch-from-block" data-ca-target-id="{$block_res_ids}" href="{"products.view?product_id=`$items.most_cheap.product_id`&warehouse_id=`$items.most_cheap.cp_lowest_warehouse_id`"|fn_url}"><span><i class="icon-spl-arrow-right"></i></span></a>
                    </div>
                </div>
            </div>
        {/if}
        {if $items.most_deliv && !$cp_similar_cheap_block_alredy_view}
            {$items_warehouse_id = $items.most_deliv.cp_wh_id}
            {$items_price = $items.most_deliv.extra_warehouse_data.$items_warehouse_id.price}
            {$items_base_price = $items.most_deliv.extra_warehouse_data.$items_warehouse_id.base_price}
            {$promotions = $items.most_deliv.extra_warehouse_data.$items_warehouse_id.promotions}
            <div class="cp-np-mosts__1st-line_item">
                <div class="cp-np-mosts__1st-line_title">
                    <h3>{__("cp_np_need_faster")}</h3>
                </div>
                <div class="cp-np-mosts__1st-line_card">
                    <div class="cp-np-mosts__1st-line_label">
                        {if !$cp_similar_cheap_block}
                            <span>{__("cp_pg_fastest_delivery")}</span>
                        {/if}
                        {if $cp_block_bo_prod_id && $cp_block_bo_prod_id == $items.most_deliv.product_id && $cp_block_bo_wh_id == $items.most_deliv.cp_fast_warehouse_id}
                            <span>{__("cp_pg_best_offer")}</span>
                        {/if}
                    </div>
                    <div class="cp-np-mosts__1st-line_name">
                        <a class="cp-np__switch-from-block" data-ca-target-id="{$block_res_ids}" href="{"products.view?product_id=`$items.most_deliv.product_id`&warehouse_id=`$items.most_deliv.cp_fast_warehouse_id`"|fn_url}">{$items.most_deliv.product}</a>
                    </div>
                    <div class="cp-np-mosts__1st-line_price">
                        {if $promotions}
                            <div class="ty-price_old hidden">
                                {include file="common/price.tpl" value=$items_base_price class="ty-list-price"}
                            </div>
                        {/if}
                        <span class="ty-price_real">
                            {include file="common/price.tpl" value=$items_price class="ty-nowrap"}
                        </span>
                    </div>
                    {if $manuf_feat_id && $items.most_deliv.product_features && $items.most_deliv.product_features.$manuf_feat_id}
                        <div class="cp-np-mosts__1st-line_data">
                            <span class="cp-np-mosts__1st-line_data-label">{$items.most_deliv.product_features.$manuf_feat_id.description}:</span><span class="cp-np-mosts__1st-line_data-value">{$items.most_deliv.product_features.$manuf_feat_id.variant}</span>
                            {if $manuf_country_feat_id && $items.most_deliv.product_features && $items.most_deliv.product_features.$manuf_country_feat_id} 
                                ({$items.most_deliv.product_features.$manuf_country_feat_id.variant})
                            {/if}
                        </div>
                    {/if}
                    {if $smarty.session.cp_user_has_defined_city}
                        <div class="cp-np-mosts__1st-line_data">
                            <span class="cp-np-mosts__1st-line_data-label">{__("cp_np_delivery_txt")}:</span><span class="cp-np-mosts__1st-line_data-value">{$items.most_deliv.cp_fastest_delivery}</span>
                        </div>
                    {/if}
                    <div class="cp-np-mosts__1st-line_data">
                        <span class="cp-np-mosts__1st-line_data-label">{__("city")}:</span><span class="cp-np-mosts__1st-line_data-value">{$items.most_deliv.cp_fastest_city}</span>
                    </div>
                    <div class="cp-np-mosts__1st-line_data cp-np__top-padding">
                        <span class="cp-np-mosts__1st-line_data-label">{__("vendor")}:</span>
                        <span class="cp-np-mosts__1st-line_data-value">
                            {* {if !$auth.user_id}
                                {__("available_after")} <a href="{"profiles.add"|fn_url}" rel="nofollow">{__("cp_spl.registry")}</a>
                            {else} *}
                                <a href="{"companies.products?company_id=`$items.most_deliv.company_id`"|fn_url}">
                                    {$items.most_deliv.company_name}
                                </a>
                            {* {/if} *}
                        </span>
                    </div>
                    <div class="cp-np-mosts__1st-line_arrow">
                        <a class="cp-np__switch-from-block" data-ca-target-id="{$block_res_ids}" href="{"products.view?product_id=`$items.most_deliv.product_id`&warehouse_id=`$items.most_deliv.cp_fast_warehouse_id`"|fn_url}"><span><i class="icon-spl-arrow-right"></i></span></a>
                    </div>
                </div>
            </div>
        {/if}
    {/if}
    <!--products_search_np_product_cheap_blocks--></div>
    
    
    
    <div class="cp-np-mosts__2nd-line {if $items.from_others}cp-np__top-big-padding{/if}" id="products_search_np_product_other_block">
        {if $items.from_others}
            {if !$o_search && $items.o_search}
                {$o_search=$items.o_search}
            {/if}
            {$c_url=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
            {$go_next=false}
            {foreach from=$items.from_others item="o_prods"}
                {foreach from=$o_prods.extra_warehouse_data item="wh_data"}
                    {$skip_this_wh = false}
                    {if $o_prods.product_id == $smarty.request.product_id && $wh_data.warehouse_id == $smarty.request.warehouse_id}
                        {$skip_this_wh = true}
                    {/if}
                    {if $wh_data.amount > 0 && !$skip_this_wh}
                        {$go_next=true}
                        {break}
                    {/if}
                {/foreach}
                {if $go_next}
                    {break}
                {/if}
            {/foreach}
            {if $go_next}
                <div class="cp-np-mosts__2nd-line_title">
                    <h3>{__("cp_pg_offers_from_other_vendors")}</h3>
                </div>

                <div class="cp-np-mosts__2nd-line_sort-dropdown visible-phone">
                    <a id="sw_elm_sort_fields" class="ty-sort-dropdown__wrapper cm-combination">{__("sort_by")}<span class="icon-spl-up"></span></a>
                    <ul id="elm_sort_fields" class="ty-sort-dropdown__content cm-popup-box hidden">
    
                        <li class="ty-sort-dropdown__content-item">
                            <a class="cm-ajax" href="{"`$c_url`&cp_np_sorting_run=1&cp_np_type=A&cp_np_sort_by=cp_np_amount&sort_order=`$o_search.sort_order_rev`"|fn_url}" data-ca-target-id="products_search_np_product_other_block">
                                {__("cp_np_nalichie_txt")}{if $o_search.cp_np_sort_by == "cp_np_amount"}<i class="icon-spl-{if $o_search.sort_order_rev == "asc"}reverse-sort{else}sort{/if}"></i>{/if}
                            </a>
                        </li>
                        <li class="ty-sort-dropdown__content-item">
                            <a class="cm-ajax" href="{"`$c_url`&cp_np_sorting_run=1&cp_np_type=D&cp_np_sort_by=cp_np_delivery&sort_order=`$o_search.sort_order_rev`"|fn_url}" data-ca-target-id="products_search_np_product_other_block">
                                {__("cp_np_delivery_days")}{if $o_search.cp_np_sort_by == "cp_np_delivery"}<i class="icon-spl-{if $o_search.sort_order_rev == "asc"}reverse-sort{else}sort{/if}"></i>{/if}
                            </a>
                        </li>
                        <li class="ty-sort-dropdown__content-item">
                            <a class="cm-ajax" href="{"`$c_url`&cp_np_sorting_run=1&cp_np_type=C&cp_np_sort_by=cp_np_price&sort_order=`$o_search.sort_order_rev`"|fn_url}" data-ca-target-id="products_search_np_product_other_block">
                                {__("price")}{if $o_search.cp_np_sort_by == "cp_np_price"}<i class="icon-spl-{if $o_search.sort_order_rev == "asc"}reverse-sort{else}sort{/if}"></i>{/if}
                            </a>
                        </li>
                    </ul>
                </div>


                <div class="cp-np-mosts__2nd-line_card">
                    <table class="cp-np-mosts__2nd-line_items ty-table">
                        <thead>
                            <th class="th-name">{__("name")}</th>
                            <th class="th-cp-np__amount">
                                <a class="cm-ajax" href="{"`$c_url`&cp_np_sorting_run=1&cp_np_type=A&cp_np_sort_by=cp_np_amount&sort_order=`$o_search.sort_order_rev`"|fn_url}" data-ca-target-id="products_search_np_product_other_block">
                                    {__("cp_np_nalichie_txt")}{if $o_search.cp_np_sort_by == "cp_np_amount"}<i class="icon-spl-{if $o_search.sort_order_rev == "asc"}reverse-sort{else}sort{/if}"></i>{/if}
                                </a>
                            </th>
                            {if $smarty.session.cp_user_has_defined_city}
                                <th class="th-cp-np__delivery">
                                    <a class="cm-ajax" href="{"`$c_url`&cp_np_sorting_run=1&cp_np_type=D&cp_np_sort_by=cp_np_delivery&sort_order=`$o_search.sort_order_rev`"|fn_url}" data-ca-target-id="products_search_np_product_other_block">
                                        {__("cp_np_delivery_days")}{if $o_search.cp_np_sort_by == "cp_np_delivery"}<i class="icon-spl-{if $o_search.sort_order_rev == "asc"}reverse-sort{else}sort{/if}"></i>{/if}
                                    </a>
                                </th>
                            {/if}
                            <th class="th-cp-np__city">{__("city")}</th>
                            <th class="th-cp-np__manufacturer">{__("cp_np_manufacturer")}</th>
                            <th class="th-cp-np__price">
                                <a class="cm-ajax" href="{"`$c_url`&cp_np_sorting_run=1&cp_np_type=C&cp_np_sort_by=cp_np_price&sort_order=`$o_search.sort_order_rev`"|fn_url}" data-ca-target-id="products_search_np_product_other_block">
                                    {__("price")}{if $o_search.cp_np_sort_by == "cp_np_price"}<i class="icon-spl-{if $o_search.sort_order_rev == "asc"}reverse-sort{else}sort{/if}"></i>{/if}
                                </a>
                            </th>
                            <th class="th-cp-np__buttons"></th>
                        </thead>
                        <tbody>
                        {if $other_list_order}
                            {foreach from=$other_list_order item="o_prods"}
                                {$o_prod_id = $o_prods.product_id}
                                {$o_wh_id = $o_prods.warehouse_id}
                                {assign var=special_product_id value=$o_prod_id|cat:"-"|cat:$o_wh_id}
                                
                                {if $items.from_others.$special_product_id && $items.from_others.$special_product_id.extra_warehouse_data && $items.from_others.$special_product_id.extra_warehouse_data.$o_wh_id}
                                    {$cur_prod_data = $items.from_others.$special_product_id}
                                    {$skip_this_wh = false}
                                    {if $special_product_id == $smarty.request.product_id && $o_wh_id == $smarty.request.warehouse_id}
                                        {$skip_this_wh = true}
                                    {/if}
                                    {if $cur_prod_data.extra_warehouse_data.$o_wh_id.amount > 0 && !$skip_this_wh}
                                        <tr>
                                            <td>
                                                {if $cp_block_bo_prod_id && $cp_block_bo_prod_id == $special_product_id && $cp_block_bo_wh_id == $o_wh_id}
                                                    <div class="cp-np__best-offer-label"><span>{__("cp_pg_best_offer")}</span></div>
                                                {/if}
                                                <a data-ca-target-id="{$block_res_ids}" class="cp-np__switch-from-block cp-np-mosts__2nd-line_name-link" href="{"products.view?product_id=`$special_product_id`&warehouse_id=`$o_wh_id`"|fn_url}">{$cur_prod_data.product}</a>
                                            </td>
                                            <td>{$cur_prod_data.extra_warehouse_data.$o_wh_id.amount} {__("items")}</td>
                                            {if $smarty.session.cp_user_has_defined_city}
                                                <td>{$cur_prod_data.extra_warehouse_data.$o_wh_id.cp_delivery}</td>
                                            {/if}
                                            <td>{$cur_prod_data.extra_warehouse_data.$o_wh_id.warehouse_city}</td>
                                            <td>
                                                {if $manuf_feat_id && $cur_prod_data.product_features && $cur_prod_data.product_features.$manuf_feat_id}
                                                    <div>{$cur_prod_data.product_features.$manuf_feat_id.variant}</div>
                                                    {if $manuf_country_feat_id && $cur_prod_data.product_features && $cur_prod_data.product_features.$manuf_country_feat_id}
                                                        <div class="cp-np-mosts__2nd-line_country-name">{$cur_prod_data.product_features.$manuf_country_feat_id.variant}</div>
                                                    {/if}
                                                {/if}
                                            </td>
                                            <td class="td-cp-np__price">
                                                {if $cur_prod_data.extra_warehouse_data.$o_wh_id.promotions}
                                                <span class="ty-price_old">
                                                    {include file="common/price.tpl" value=$cur_prod_data.extra_warehouse_data.$o_wh_id.base_price class="ty-list-price"}
                                                </span>
                                                {/if}
                                                <span class="ty-price_real">
                                                    {include file="common/price.tpl" value=$cur_prod_data.extra_warehouse_data.$o_wh_id.price class="ty-nowrap"}
                                                </span>
                                            </td>
                                            <td><a data-ca-target-id="{$block_res_ids}" class="cp-np__switch-from-block cp-np-mosts__2nd-line_arrow-link" href="{"products.view?product_id=`$cur_prod_data.product_id`&warehouse_id=`$o_wh_id`"|fn_url}"><span><i class="icon-spl-arrow-right"></i></span></a></td>
                                        </tr>
                                    {/if}
                                {/if}
                            {/foreach}
                        {else}
                            {foreach from=$items.from_others item="o_prods"}
                                {foreach from=$o_prods.extra_warehouse_data item="wh_data"}
                                
                                    {$skip_this_wh = false}
                                    {if $o_prods.product_id == $smarty.request.product_id && $wh_data.warehouse_id == $smarty.request.warehouse_id}
                                        {$skip_this_wh = true}
                                    {/if}
                                    {if $wh_data.amount > 0 && !$skip_this_wh}
                                        <tr>
                                            <td>
                                                {if $cp_block_bo_prod_id && $cp_block_bo_prod_id == $o_prods.product_id && $cp_block_bo_wh_id == $wh_data.warehouse_id}
                                                    <div class="cp-np__best-offer-label"><span>{__("cp_pg_best_offer")}</span></div>
                                                {/if}
                                                <a data-ca-target-id="{$block_res_ids}" class="cp-np__switch-from-block cp-np-mosts__2nd-line_name-link" href="{"products.view?product_id=`$o_prods.product_id`&warehouse_id=`$wh_data.warehouse_id`"|fn_url}">{$o_prods.product}</a>
                                            </td>
                                            <td>{$wh_data.amount} {__("items")}</td>
                                            {if $smarty.session.cp_user_has_defined_city}
                                                <td>{$wh_data.cp_delivery}</td>
                                            {/if}
                                            <td>{$wh_data.warehouse_city}</td>
                                            <td>
                                                {if $manuf_feat_id && $o_prods.product_features && $o_prods.product_features.$manuf_feat_id}
                                                    <div>{$o_prods.product_features.$manuf_feat_id.variant}</div>
                                                    {if $manuf_country_feat_id && $o_prods.product_features && $o_prods.product_features.$manuf_country_feat_id}
                                                        <div class="cp-np-mosts__2nd-line_country-name">{$o_prods.product_features.$manuf_country_feat_id.variant}</div>
                                                    {/if}
                                                {/if}
                                            </td>
                                            <td class="td-cp-np__price">
                                                {if $wh_data.promotions}
                                                <span class="ty-price_old">
                                                    {include file="common/price.tpl" value=$wh_data.base_price class="ty-list-price"}
                                                </span>
                                                {/if}
                                                <span class="ty-price_real">
                                                    {include file="common/price.tpl" value=$wh_data.price class="ty-nowrap"}
                                                </span>
                                            </td>
                                            <td><a data-ca-target-id="{$block_res_ids}" class="cp-np__switch-from-block cp-np-mosts__2nd-line_arrow-link" href="{"products.view?product_id=`$o_prods.product_id`&warehouse_id=`$wh_data.warehouse_id`"|fn_url}"><span><i class="icon-spl-arrow-right"></i></span></a></td>
                                        </tr>
                                    {/if}
                                {/foreach}
                            {/foreach}
                        {/if}
                        <tr id="cp_np_other_block_pagination">
                        <!--cp_np_other_block_pagination--></tr>
                        </tbody>
                    </table>
                    <div id="cp_np_other_block_pagination_more">
                        {$o_pagination=$o_search|fn_generate_pagination}
                        {$c_url2=$config.current_url|fn_query_remove:"page"}
                        {if $o_search.total_items && $o_pagination.next_page}
                            <div class="cp-np-mosts__2nd-line_more">
                                <a class="" href="{"`$c_url2`&page=`$o_pagination.next_page`"|fn_url}">{__("cp_pg_show_more")}</a>
                            </div>
                        {/if}
                    <!--cp_np_other_block_pagination_more--></div>
                </div>
            {/if}
        {/if}
    <!--products_search_np_product_other_block--></div>
    
{/if}
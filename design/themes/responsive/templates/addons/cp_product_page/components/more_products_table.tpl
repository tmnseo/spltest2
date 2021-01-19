{$block_res_ids = "product_filters_*,products_search_*,category_products_*,product_features_*,breadcrumbs_*,currencies_*,languages_*,selected_filters_*"}
{if $addons.cp_product_page.brand_id}
    {$manuf_feat_id=$addons.cp_product_page.brand_id}
{/if}
{if $addons.cp_product_page.brand_country_id}
    {$manuf_country_feat_id=$addons.cp_product_page.brand_country_id}
{/if}
{foreach from=$other_list_order item="o_prods"}
    {$o_prod_id = $o_prods.product_id}
    {$o_wh_id = $o_prods.warehouse_id}
    {assign var=special_product_id value=$o_prod_id|cat:"-"|cat:$o_wh_id}
    
    
    {if $items.from_others.$special_product_id && $items.from_others.$special_product_id.extra_warehouse_data && $items.from_others.$special_product_id.extra_warehouse_data.$o_wh_id}
        {$cur_prod_data = $items.from_others.$special_product_id}
        
        {if $o_prod_id == $current_prod_id && $o_wh_id == $current_wh_id}
            {$skip_this_wh = true}
        {/if}

        {if $cur_prod_data.extra_warehouse_data.$o_wh_id.amount > 0 && !$skip_this_wh}
            <tr>
                <td>
                    {if $cp_block_bo_prod_id && $cp_block_bo_prod_id == $o_prod_id && $cp_block_bo_wh_id == $o_wh_id}
                        <div class="cp-np__best-offer-label"><span>{__("cp_pg_best_offer")}</span></div>
                    {/if}
                    <a data-ca-target-id="{$block_res_ids}" class="cp-np__switch-from-block cp-np-mosts__2nd-line_name-link" href="{"products.view?product_id=`$o_prod_id`&warehouse_id=`$o_wh_id`"|fn_url}">{$cur_prod_data.product}</a>
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
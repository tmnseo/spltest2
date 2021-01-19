<ul class="live-products" id="live_search_products{$search_prefix}{$search_input_id}">
    {assign var="group_category_name" value=""}
    {foreach from=$live_result item="product" name="live_result_products"}
        {*assign var="category_group_displayed" value=false}

        {if $category_names && $category_names[$product.main_category]
            && $category_names[$product.main_category] != $group_category_names
        }
            {$category_group_displayed = true}
            {$group_category_names = $category_names[$product.main_category]}
            <li class="live-group-category">{$group_category_names}</li>
        {/if*}
        
        <li class="live-item-li clearfix">  
            <a href="{"products.view?product_id=`$product.product_id`&warehouse_id=`$product.cp_wh_id`"|fn_url}">
                <div class="live-item-container">
                    <div class="live-info-container">
                        <div class="live-product-name-wrap">
                            <span class="product-title" title="{$product.product nofilter}">{$product.product nofilter}</span>
                            {if $product.cp_np_manuf_code}
                                <div class="live-product_manuf-code">
                                    {if $product.cp_wh_id && $product.extra_warehouse_data}
                                        {$use_this_wh_id=$product.cp_wh_id}
                                        {if $product.extra_warehouse_data.$use_this_wh_id}
                                            {$show_this_price = $product.extra_warehouse_data.$use_this_wh_id.price}
                                            {$show_base_price = $product.extra_warehouse_data.$use_this_wh_id.base_price}
                                            {$show_promotions = $product.extra_warehouse_data.$use_this_wh_id.promotions}
                                        {/if}
                                    {/if}
                                    {if $show_this_price}
                                        <span class="live-product-price ty-price-num">
                                            {if $show_promotions}
                                                <span class="ty-price_old">
                                                    {include file="common/price.tpl" value=$show_base_price}
                                                </span>
                                            {/if}
                                            <span class="ty-price_real">
                                                {include file="common/price.tpl" value=$show_this_price}
                                            </span>
                                        </span>
                                    {/if}
                                    <span class="live-product-name">{$product.cp_np_manuf_code}</span>
                                </div>
                            {else}
                                <div class="live-product_manuf-code">
                                    {$use_this_wh_id=$product.cp_wh_id}
                                    {if $product.extra_warehouse_data.$use_this_wh_id}
                                        {$show_this_price = $product.extra_warehouse_data.$use_this_wh_id.price}
                                        {$show_base_price = $product.extra_warehouse_data.$use_this_wh_id.base_price}
                                        {$show_promotions = $product.extra_warehouse_data.$use_this_wh_id.promotions}
                                    {/if}
                                    {if $show_this_price}
                                        <span class="live-product-price ty-price-num">
                                            {if $show_promotions}
                                                <span class="ty-price_old">
                                                    {include file="common/price.tpl" value=$show_base_price}
                                                </span>
                                            {/if}
                                            <span class="ty-price_real">
                                                {include file="common/price.tpl" value=$show_this_price}
                                            </span>
                                        </span>
                                    {/if}
                                    <span class="live-product-name">{$product.cp_np_manuf_code}</span>
                                </div>
                            {/if}
                        </div>
                    </div>
                </div>
            </a>
        </li>
    {/foreach}
<!--live_search_products{$search_prefix}{$search_input_id}--></ul>


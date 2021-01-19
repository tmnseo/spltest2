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
            {*if $category_labels && $category_labels[$product.main_category]}
                <div class="cp-category-label-wrap">
                    <a href="{"categories.view?category_id=`$category_labels[$product.main_category].category_id`"|fn_url}">
                        <div class="cp-category-label" title="{$category_labels[$product.main_category].category}" style="background-color: {$category_labels[$product.main_category].color};">
                            {$category_labels[$product.main_category].category}
                        </div>
                    </a>
                </div>
            {/if*}
            
            <a class="cm-ls-product" onclick="fn_cp_catalog_changes_search_by_q(this, '{$product.product}');">
                <div class="live-item-container">
                    {*if $addons.cp_live_search.show_thumbnails == 'Y'}
                        <div class="live-image-container">
                            {include file="common/image.tpl" images=$product.main_pair image_width=$addons.cp_live_search.thumbnails_width|default:50 image_height=$addons.cp_live_search.thumbnails_height|default:50}
                        </div>
                    {/if*}
                    <div class="live-info-container">
                        <div class="live-product-name-wrap">
                            <a href="{"products.view?product_id=`$product.product_id`&warehouse_id=`$product.cp_wh_id`"|fn_url}">
                                <span class="product-title" title="{$product.product nofilter}">{$product.product nofilter}</span>
                            </a>
                            {if $product.cp_np_manuf_code}
                                <div>
                                    {if $product.cp_wh_id && $product.extra_warehouse_data}
                                        {$use_this_wh_id=$product.cp_wh_id}
                                        {if $product.extra_warehouse_data.$use_this_wh_id}
                                            {$show_this_price = $product.extra_warehouse_data.$use_this_wh_id.price}
                                        {/if}
                                    {/if}
                                    {if $show_this_price}
                                        <span class="live-product-price ty-price-num">{include file="common/price.tpl" value=$show_this_price}</span>
                                    {/if}
                                    <span class="live-product-name">{$product.cp_np_manuf_code}</span>
                                </div>
                            {/if}
                        </div>
                        {*if $addons.cp_live_search.show_product_price == 'Y'
                            && ($settings.General.allow_anonymous_shopping != 'hide_price_and_add_to_cart' || $auth.user_id != 0)
                        }
                            <div class="live-product-price-wrap">
                                {if $product.zero_price_action == "R" && (!$product.price || $product.price == 0.0)}
                                    <span class="ty-no-price">{__("contact_us_for_price")}</span>
                                {else}
                                    {if $addons.cp_live_search.show_list_price == 'Y'}
                                        {assign var="old_price" value=0}
                                        {if $product.discount}
                                            {$old_price = $product.original_price|default:$product.base_price}
                                        {elseif $product.list_discount}
                                            {$old_price = $product.list_price}
                                        {/if}
                                        {if $old_price && $old_price != 0.0 && $old_price > $product.price}
                                            <span class="live-product-list-price ty-list-price">{include file="common/price.tpl" value=$old_price}</span>
                                        {/if}
                                    {/if}
                                    <span class="live-product-price ty-price-num">
                                        {include file="common/price.tpl" value=$product.price}
                                    </span>
                                    {if $settings.Appearance.show_prices_taxed_clean == "Y" && $product.taxed_price}
                                        <div class="live-product-price cp-taxed-price">
                                            {if $product.clean_price != $product.taxed_price && $product.included_tax}
                                                ({include file="common/price.tpl" value=$product.taxed_price} {__("inc_tax")})
                                            {/if}
                                        </div>
                                    {/if}
                                {/if}
                            </div>
                        {/if}
                        {if $addons.cp_live_search.show_product_code == 'Y'}
                            <div class="live-product-code-wrap">
                                <span class="live-product-code">{$product.product_code nofilter}</span>
                            </div>
                        {/if*}
                    </div>
                </div>
            </a>
        </li>
    {/foreach}
<!--live_search_products{$search_prefix}{$search_input_id}--></ul>


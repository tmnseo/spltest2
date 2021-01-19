{if $product}
    {$block_res_ids = "product_filters_*,products_search_*,category_products_*,product_features_*,breadcrumbs_*,currencies_*,languages_*,selected_filters_*"}
    {if $addons.cp_product_page.brand_code_id}
        {$manuf_code_feat_id=$addons.cp_product_page.brand_code_id}
    {/if}
    {assign var="obj_id" value=$obj_id|default:$product.product_id}
    {assign var="obj_id_prefix" value="`$obj_prefix``$product.product_id`"}
    {include file="common/product_data.tpl" obj_id=$obj_id product=$product}
    {assign var="features" value=$product|fn_get_product_features_list}
    <div class="ty-simple-list clearfix">
        {assign var="form_open" value="form_open_`$obj_id`"}
        {$smarty.capture.$form_open nofilter}
            {if $item_number == "Y"}<strong>{$smarty.foreach.products.iteration}.&nbsp;</strong>{/if}

            {assign var="product_labels" value="product_labels_`$obj_prefix``$obj_id`"}
            {$smarty.capture.$product_labels nofilter}

            <div class="ty-simple-list__item-product-code">
                {if !$manuf_code_feat_id}
                    {$product.product_code}
                {else}
                    {$features.$manuf_code_feat_id.variant}
                {/if}
            </div>

            <div class="ty-simple-list__item-name">
                {assign var="name" value="name_$obj_id"}
                <bdi>{$smarty.capture.$name nofilter}</bdi>
            </div>

            <div class="ty-simple-list__item-supplier">
                <span class="cp-item-supplier__label">{__("supplier")}: </span>
                <span class="cp-item-supplier__value">
                    {if !$auth.user_id}
                        {__("available_after")} <a href="{"profiles.add"|fn_url}" rel="nofollow">{__("cp_spl.registry")}</a>
                    {else}
                        {* <a href="{"companies.products?company_id=`$product.company_id`"|fn_url}" class="company">{$product.company_name}</a> *}
                        <span class="company">{$product.company_name}</span>
                    {/if}
                </span>
            </div>

            {assign var="rating" value="rating_`$obj_id`"}{$smarty.capture.$rating nofilter}

            <div class="ty-simple-list__item-block_flex">
                <div class="ty-simple-list__item-price-delivery">
                    {if !$hide_price}
                        {if $product.price > 0}
                            <div class="ty-simple-list__price {if $product.price == 0}ty-simple-list__no-price{/if}">
                                <span class="cp-item-price__label">{__("price")}:</span>
                                <span class="cp-item-price__value">
                                    {assign var="old_price" value="old_price_`$obj_id`"}
                                    {if $smarty.capture.$old_price|trim}{$smarty.capture.$old_price nofilter}{/if}

                                    {assign var="price" value="price_`$obj_id`"}
                                    {$smarty.capture.$price nofilter}

                                    {assign var="clean_price" value="clean_price_`$obj_id`"}
                                    {$smarty.capture.$clean_price nofilter}

                                    {assign var="list_discount" value="list_discount_`$obj_id`"}
                                    {$smarty.capture.$list_discount nofilter}
                                </span>
                            </div>
                        {/if}
                    {/if}

                    {if $product.delivery_data}
                        <div class="ty-simple-list__delivery">
                            <span class="cp-item-delivery__label">{__("delivery")}:</span>
                            <span class="cp-item-delivery__value">{$product.delivery_data}
                            </span>
                        </div>
                    {/if}
                </div>

                {if $capture_options_vs_qty}{capture name="product_options"}{/if}
                {assign var="product_amount" value="product_amount_`$obj_id`"}
                {$smarty.capture.$product_amount nofilter}

                {if $show_features || $show_descr}
                    <div class="ty-simple-list__feature">{assign var="product_features" value="product_features_`$obj_id`"}{$smarty.capture.$product_features nofilter}</div>
                    <div class="ty-simple-list__descr">{assign var="prod_descr" value="prod_descr_`$obj_id`"}{$smarty.capture.$prod_descr nofilter}</div>
                {/if}

                {assign var="product_options" value="product_options_`$obj_id`"}
                {$smarty.capture.$product_options nofilter}
                
                {if !$hide_qty}
                    {assign var="qty" value="qty_`$obj_id`"}
                    {$smarty.capture.$qty nofilter}
                {/if}

                {assign var="advanced_options" value="advanced_options_`$obj_id`"}
                {$smarty.capture.$advanced_options nofilter}
                {if $capture_options_vs_qty}{/capture}{/if}
                
                {assign var="min_qty" value="min_qty_`$obj_id`"}
                {$smarty.capture.$min_qty nofilter}

                {assign var="product_edp" value="product_edp_`$obj_id`"}
                {$smarty.capture.$product_edp nofilter}

                {if $capture_buttons}{capture name="buttons"}{/if}
                {if $show_add_to_cart}
                    <div class="ty-simple-list__buttons">
                        <div class="cp-np-mosts__1st-line_arrow">
                            <a class="cp-np__switch-from-block" data-ca-target-id="{$block_res_ids}" href="{"products.view?product_id=`$product.product_id`"|fn_url}"><span><i class="icon-spl-arrow-right"></i></span></a>
                        </div>

                        {assign var="add_to_cart" value="add_to_cart_`$obj_id`"}
                        {$smarty.capture.$add_to_cart nofilter}

                        {assign var="list_buttons" value="list_buttons_`$obj_id`"}
                        {$smarty.capture.$list_buttons nofilter}
                    </div>
                {/if}
                {if $capture_buttons}{/capture}{/if}
            </div>
        {assign var="form_close" value="form_close_`$obj_id`"}
        {$smarty.capture.$form_close nofilter}
    </div>
{/if}
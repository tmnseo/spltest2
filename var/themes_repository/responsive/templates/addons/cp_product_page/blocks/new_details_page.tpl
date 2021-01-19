{script src="js/tygh/exceptions.js"}
{$product_wrap=true}
<div class="ty-product-block ty-product-detail" id="products_search_np_product_main_product">
    <div class="ty-product-block__wrapper clearfix">
    {if $product}
        {if $addons.cp_product_page.brand_id}
            {$manuf_feat_id=$addons.cp_product_page.brand_id}
        {/if}

        {if $addons.cp_product_page.brand_code_id}
            {$manuf_code_feat_id=$addons.cp_product_page.brand_code_id}
        {/if}

        {if $addons.cp_product_page.brand_techniques_id}
            {$brand_techniques_feat_id=$addons.cp_product_page.brand_techniques_id}
        {/if}
        {if $addons.cp_product_page.brand_analogs_id}
            {$analogs=$addons.cp_product_page.brand_analogs_id}
        {/if}

        {assign var="features" value=$product|fn_get_product_features_list}

        
        {assign var="obj_id" value=$product.product_id}
        {include file="common/product_data.tpl" product=$product but_role="big" but_text=__("add_to_cart")}
        {hook name="products:view_main_info"}
        {/hook}
        {hook name="products:product_tabs"}
            {include file="views/tabs/components/product_tabs.tpl"}

            {if $blocks.$tabs_block_id.properties.wrapper}
                {include file=$blocks.$tabs_block_id.properties.wrapper content=$smarty.capture.tabsbox_content title=$blocks.$tabs_block_id.description}
            {else}
                {$smarty.capture.tabsbox_content nofilter}
            {/if}
        {/hook}

        <div class="ty-product-block__botton">
            {assign var="form_open" value="form_open_`$obj_id`"}
            {$smarty.capture.$form_open nofilter}

                {assign var="old_price" value="old_price_`$obj_id`"}
                {assign var="price" value="price_`$obj_id`"}
                {assign var="clean_price" value="clean_price_`$obj_id`"}
                {assign var="list_discount" value="list_discount_`$obj_id`"}
                {assign var="discount_label" value="discount_label_`$obj_id`"}

                {if $cp_warehouse_id}
                    <input type="hidden" name="product_data[{$product.product_id}][extra][warehouse_id]" value="{$cp_warehouse_id}"/>
                {/if}
                {if !$cp_warehouse_id}
                    <input type="hidden" value="" id="warehouse_id_{$product.product_id}" name="product_data[{$obj_id}][extra][warehouse_id]">
                    <input type="hidden" value="" id="cp_qty_count_{$product.product_id}" name="product_data[{$obj_id}][amount]">
                {/if}

                {if $capture_options_vs_qty}{capture name="product_options"}{$smarty.capture.product_options nofilter}{/if}
                    <div class="ty-product-block__qty">
                        {if isset($cp_warehouses_amount)}
                            {$warehouse_data.amount = $cp_warehouses_amount}
                            {include file="addons/cp_warehouse_products_prices/components/warehouses_qty.tpl"}
                        {else}
                            {assign var="qty" value="qty_`$obj_id`"}
                            {$smarty.capture.$qty nofilter}
                        {/if}
                        {assign var="min_qty" value="min_qty_`$obj_id`"}
                        {$smarty.capture.$min_qty nofilter}
                    </div>
                {if $capture_options_vs_qty}{/capture}{/if}

                {if $capture_buttons}{capture name="buttons"}{/if}

                    <div class="ty-product-block__button{if !$warehouse_data.amount && !$qty} ty-product-block__button_no-qty{/if}">
                    {if $show_details_button}
                        {include file="buttons/button.tpl" but_href="products.view?product_id=`$product.product_id`" but_text=__("view_details") but_role="submit"}
                    {/if}
                    
                    {if $warehouse_prices[$cp_warehouse_id]}
                        {$warehouse_data.price = $warehouse_prices[$cp_warehouse_id]}
                        {include file="addons/cp_warehouse_products_prices/components/warehouses_add_to_cart_button.tpl"}
                    {elseif !$cp_warehouse_id}
                        <a class="ty-btn__primary ty-btn__big ty-btn__add-to-cart ty-btn cm-dialog-opener cm-dialog-auto-size" 
                            data-ca-target-id="warehouses_product_popup_{$obj_id}" 
                            data-ca-dialog-class="warehouses-popup-wrap"
                        >
                            {__("add_to_cart")}
                        </a>
                        {if $show_list_buttons}
                            {capture name="product_buy_now_`$obj_id`"}
                                {$compare_product_id = $product.product_id}

                                {if $settings.General.enable_compare_products == "Y"}
                                    {include file="buttons/add_to_compare_list.tpl" product_id=$compare_product_id}
                                {/if}
                            {/capture}
                            {assign var="capture_buy_now" value="product_buy_now_`$obj_id`"}

                            {if $smarty.capture.$capture_buy_now|trim}
                                {$smarty.capture.$capture_buy_now nofilter}
                            {/if}
                        {/if}
                    {else}
                        {assign var="add_to_cart" value="add_to_cart_`$obj_id`"}
                        {$smarty.capture.$add_to_cart nofilter}
                    {/if}

                    {assign var="list_buttons" value="list_buttons_`$obj_id`"}
                    {$smarty.capture.$list_buttons nofilter}
                </div>
                {if $capture_buttons}{/capture}{/if}

            {assign var="form_close" value="form_close_`$obj_id`"}
            {$smarty.capture.$form_close nofilter}
        </div>
        {if $product_wrap}
            </div>
        {/if}
    {/if}
    </div>

    {if $smarty.capture.hide_form_changed == "Y"}
        {assign var="hide_form" value=$smarty.capture.orig_val_hide_form}
    {/if}

    
<!--products_search_np_product_main_product--></div>

{capture name="mainbox_title"}{assign var="details_page" value=true}{/capture}

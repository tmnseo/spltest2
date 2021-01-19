{hook name="products:view_main_info"} 
{*moved to module cp_product_page*}
    {* {if $product} 
        {assign var="obj_id" value=$product.product_id}
        {include file="common/product_data.tpl" product=$product but_role="big" but_text=__("add_to_cart")}
        <div class="ty-product-block__img-wrapper" style="width: {$settings.Thumbnails.product_details_thumbnail_width}px">
            {hook name="products:image_wrap"}
                {if !$no_images}
                    <div class="ty-product-block__img cm-reload-{$product.product_id}" data-ca-previewer="true" id="product_images_{$product.product_id}_update">

                        {assign var="product_labels" value="product_labels_`$obj_prefix``$obj_id`"}
                        {$smarty.capture.$product_labels nofilter}

                        {include file="views/products/components/product_images.tpl" product=$product show_detailed_link="Y" image_width=$settings.Thumbnails.product_details_thumbnail_width image_height=$settings.Thumbnails.product_details_thumbnail_height}
                    <!--product_images_{$product.product_id}_update--></div>
                {/if}
            {/hook}
        </div>
        
        <div class="ty-product-block__left">
            {assign var="form_open" value="form_open_`$obj_id`"}
            {$smarty.capture.$form_open nofilter}
            {if $cp_warehouse_id}
                <input type="hidden" name="product_data[{$product.product_id}][extra][warehouse_id]" value="{$cp_warehouse_id}"/>
            {/if}
            {if !$cp_warehouse_id}
            <input type="hidden" value="" id="warehouse_id_{$product.product_id}" name="product_data[{$obj_id}][extra][warehouse_id]">
            <input type="hidden" value="" id="cp_qty_count_{$product.product_id}" name="product_data[{$obj_id}][amount]">
           {/if}
            {hook name="products:main_info_title"}
                {if !$hide_title}
                    <h1 class="ty-product-block-title" {live_edit name="product:product:{$product.product_id}"}><bdi>{$product.product nofilter}</bdi></h1>
                {/if}

                {hook name="products:brand"}
                    {hook name="products:brand_default"}
                        <div class="brand">
                            {include file="views/products/components/product_features_short_list.tpl" features=$product.header_features}
                        </div>
                    {/hook}
                {/hook}
            {/hook}

            {assign var="old_price" value="old_price_`$obj_id`"}
            {assign var="price" value="price_`$obj_id`"}
            {assign var="clean_price" value="clean_price_`$obj_id`"}
            {assign var="list_discount" value="list_discount_`$obj_id`"}
            {assign var="discount_label" value="discount_label_`$obj_id`"}

            {hook name="products:promo_text"}
            {if $product.promo_text}
            <div class="ty-product-block__note-wrapper">
                <div class="ty-product-block__note ty-product-block__note-inner">
                    {$product.promo_text nofilter}
                </div>
            </div>
            {/if}
            {/hook}

            <div class="{if $smarty.capture.$old_price|trim || $smarty.capture.$clean_price|trim || $smarty.capture.$list_discount|trim}prices-container {/if}price-wrap">
                {if $smarty.capture.$old_price|trim || $smarty.capture.$clean_price|trim || $smarty.capture.$list_discount|trim}
                    <div class="ty-product-prices">
                        {if $warehouse_prices[$cp_warehouse_id]['base_price']}
                            {$warehouse_data.base_price = $warehouse_prices[$cp_warehouse_id]['base_price']}
                            {include file="addons/cp_warehouse_products_prices/components/warehouses_product_old_price.tpl"}
                        {else}
                            {if $smarty.capture.$old_price|trim}{$smarty.capture.$old_price nofilter}{/if}
                        {/if} 
                        
                {/if}

                {if $smarty.capture.$price|trim}
                    <div class="ty-product-block__price-actual">
                            {if $warehouse_prices[$cp_warehouse_id]['price']}
                                {$warehouse_data.price = $warehouse_prices[$cp_warehouse_id]['price']}
                                {include file="addons/cp_warehouse_products_prices/components/warehouses_product_price.tpl"}
                            {else}
                                {$smarty.capture.$price nofilter}
                            {/if}    
                    </div>
                {/if}

                {if $smarty.capture.$old_price|trim || $smarty.capture.$clean_price|trim || $smarty.capture.$list_discount|trim}
                        {$smarty.capture.$clean_price nofilter}
                        {$smarty.capture.$list_discount nofilter}
                    </div>
                {/if}
            </div>

            {if $capture_options_vs_qty}{capture name="product_options"}{$smarty.capture.product_options nofilter}{/if}
            <div class="ty-product-block__option">
                {assign var="product_options" value="product_options_`$obj_id`"}
                {$smarty.capture.$product_options nofilter}
            </div>
            {if $capture_options_vs_qty}{/capture}{/if}

            <div class="ty-product-block__advanced-option clearfix">
                {if $capture_options_vs_qty}{capture name="product_options"}{$smarty.capture.product_options nofilter}{/if}
                {assign var="advanced_options" value="advanced_options_`$obj_id`"}
                {$smarty.capture.$advanced_options nofilter}
                {if $capture_options_vs_qty}{/capture}{/if}
            </div>

            <div class="ty-product-block__sku">
                {assign var="sku" value="sku_`$obj_id`"}
                {$smarty.capture.$sku nofilter}
            </div>
            {if $cp_store_data}
                <div class="ty-product-block__field-group">
                    {__("store")} : {$cp_store_data.name},{$cp_store_data.city}
                </div>
            {/if}
            {if $capture_options_vs_qty}{capture name="product_options"}{$smarty.capture.product_options nofilter}{/if}
            <div class="ty-product-block__field-group">

                {if isset($cp_warehouses_amount)}
                    {$warehouse_data.amount = $cp_warehouses_amount}
                    {include file="addons/cp_warehouse_products_prices/components/warehouses_amount.tpl"}
                    {include file="addons/cp_warehouse_products_prices/components/warehouses_qty.tpl"}
                {else}
                    {assign var="product_amount" value="product_amount_`$obj_id`"}
                    {$smarty.capture.$product_amount nofilter}
                {/if}

                {assign var="min_qty" value="min_qty_`$obj_id`"}
                {$smarty.capture.$min_qty nofilter}
            </div>
            {if $capture_options_vs_qty}{/capture}{/if}

            {assign var="product_edp" value="product_edp_`$obj_id`"}
            {$smarty.capture.$product_edp nofilter}

            {if $show_descr}
            {assign var="prod_descr" value="prod_descr_`$obj_id`"}
                <h3 class="ty-product-block__description-title">{__("description")}</h3>
                <div class="ty-product-block__description">{$smarty.capture.$prod_descr nofilter}</div>
            {/if}

            {if $capture_buttons}{capture name="buttons"}{/if}
            <div class="ty-product-block__button">
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

            {if !$cp_warehouse_id}
                {hook name="products:product_detail_bottom"}
                {/hook}
            {/if}

            {if $show_product_tabs}
            {include file="views/tabs/components/product_popup_tabs.tpl"}
            {$smarty.capture.popupsbox_content nofilter}
            {/if}
        </div>
    {/if} *}
{/hook}
{include file="addons/cp_warehouse_products_prices/components/warehouses_product_popup.tpl"}
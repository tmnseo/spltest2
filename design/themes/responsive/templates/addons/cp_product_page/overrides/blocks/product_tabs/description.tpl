{** block-description:description **}

<div class="ty-product-detail-desc ty-product-detail-tabs">
    <div class="ty-product-detail-desc__left ty-product-detail__column">
        {if $cp_block_bo_prod_id && $cp_block_bo_prod_id == $product.product_id && (($smarty.request.warehouse_id && $cp_block_bo_wh_id && $smarty.request.warehouse_id == $cp_block_bo_wh_id) || !$smarty.request.warehouse_id)}
        <div class="cp-np__best-offer-label ty-product-detail__item">
            <span>{__("cp_pg_best_offer")}</span>
        </div>
        {/if}
        {if $manuf_code_feat_id}
        <div class="ty-product-detail-desc__product-code">
            <span class="ty-product-detail__label">{__("cp_ls_product_code")}:</span>
            <span class="ty-product-detail__value">{$features.$manuf_code_feat_id.variant}</span>
        </div>
        {/if}

        <h2 class="ty-product-detail-desc__title ty-product-detail__item{if $smarty.capture.$old_price|trim || $smarty.capture.$clean_price|trim || $smarty.capture.$list_discount|trim || $product.promotions} ty-product-detail-desc__title_discount{/if}" {live_edit name="product:product:{$product.product_id}"}><bdi>{$product.product nofilter}</bdi></h2>

        <div class="ty-product-detail-desc__price ty-product-detail__item">
            {assign var="old_price" value="old_price_`$obj_id`"}
            {assign var="price" value="price_`$obj_id`"}
            {assign var="clean_price" value="clean_price_`$obj_id`"}
            {assign var="list_discount" value="list_discount_`$obj_id`"}
            {assign var="discount_label" value="discount_label_`$obj_id`"}

            {if $smarty.capture.$old_price|trim || $smarty.capture.$clean_price|trim || $smarty.capture.$list_discount|trim || $product.promotions}

                {if $warehouse_prices[$cp_warehouse_id]['base_price'] && $warehouse_prices[$cp_warehouse_id]['price'] && $warehouse_prices[$cp_warehouse_id]['base_price'] != $warehouse_prices[$cp_warehouse_id]['price']}
                    {$warehouse_data.base_price = $warehouse_prices[$cp_warehouse_id]['base_price']}
                    {$warehouse_data.price = $warehouse_prices[$cp_warehouse_id]['price']}
                    {include file="addons/cp_warehouse_products_prices/components/warehouses_product_old_price.tpl"}
                {elseif !$warehouse_prices[$cp_warehouse_id]['base_price']}
                    {if $smarty.capture.$old_price|trim}{$smarty.capture.$old_price nofilter}{/if}
                {/if}     
            {/if}

            {if $smarty.capture.$price|trim}
                {if $warehouse_prices[$cp_warehouse_id]['price']}
                    {$warehouse_data.price = $warehouse_prices[$cp_warehouse_id]['price']}
                    {include file="addons/cp_warehouse_products_prices/components/warehouses_product_price.tpl"}
                {else}
                    {$smarty.capture.$price nofilter}
                {/if}    
            {/if}
            {if $smarty.capture.$clean_price|trim}
                {$smarty.capture.$clean_price nofilter}
                {* {$smarty.capture.$list_discount nofilter} *}
            {/if}
        </div>
        
        <div class="ty-product-detail-desc__vendor ty-product-detail__item">
            <span class="ty-product-detail__label">{__("vendor")}:</span>
            {* {if !$auth.user_id}
                <span class="ty-product-detail__value">{__("available_after")} <a href="{"profiles.add"|fn_url}" rel="nofollow">{__("cp_spl.registry")}</a></span>
            {else} *}
                <a class="ty-product-detail__value" href="{"companies.products?company_id=`$product.company_id`"|fn_url}">{$product.company_name}</a>
            {* {/if} *}
        </div>
    </div>

    <div class="ty-product-detail-desc__right ty-product-detail__column">
        <div class="ty-product-detail-desc__stock ty-product-detail__item_full-width ty-product-detail__item">
            <span class="ty-product-detail__label">{__("stock")}:</span>
            <span class="ty-product-detail__value">{$cp_store_data.city}</span>
        </div>
        {if $manuf_feat_id}
        <div class="ty-product-detail-desc__manufacturer ty-product-detail__item">
            <span class="ty-product-detail__label">{__("cp_np_parts_manufacturer")}:</span>
            <span class="ty-product-detail__value">
                {$features.$manuf_feat_id.variant} 
                {if $manuf_country_feat_id && $features.$manuf_country_feat_id.variant}
                    ({$features.$manuf_country_feat_id.variant})
                {/if}
            </span>
        </div>
        {/if}

        <div class="ty-product-detail-desc__guarantee ty-product-detail__item">
            <span class="ty-product-detail__label">{__("cp_np_guarantee")}:</span>
            {if $product.cp_warranty_period}
                <span class="ty-product-detail__value">{__("cp_warranty_period", [$product.cp_warranty_period])}</span>
            {else}
                <a class="ty-product-detail__value" href="{$addons.cp_spl_theme.cp_warranty_href}?vendor_id={$product.company_id}" target="_blank">{__("cp_warranty_href")}</a>
            {/if}
        </div>

        <div class="ty-product-detail-desc__delivery ty-product-detail__item">
            <span class="ty-product-detail__label">{__("cp_np_delivery")} {$cp_matrix_city_name}:</span>
            <span class="ty-product-detail__value">{$cp_fastest_delivery_con}</span>
        </div>

        {* <div class="ty-product-detail-desc__part-type ty-product-detail__item">
            <span class="ty-product-detail__label">{__("cp_np_part_type")}:</span>
            <span class="ty-product-detail__value"></span>
        </div> *}

        {if $capture_options_vs_qty}{capture name="product_options"}{$smarty.capture.product_options nofilter}{/if}
            <div class="ty-product-detail-desc__amount ty-product-detail__item">
                {if isset($cp_warehouses_amount)}
                    {$warehouse_data.amount = $cp_warehouses_amount}
                    {include file="addons/cp_warehouse_products_prices/components/warehouses_amount.tpl"}
                    {* {include file="addons/cp_warehouse_products_prices/components/warehouses_qty.tpl"} *}
                {else}
                    {assign var="product_amount" value="product_amount_`$obj_id`"}
                    {$smarty.capture.$product_amount nofilter}
                {/if}
            </div>
        {if $capture_options_vs_qty}{/capture}{/if}
        {hook name="products:vendor_communication"}
        {/hook}

    </div>
</div>

<div class="cp-compact-list__item">
    <form {if !$config.tweaks.disable_dhtml}class="cm-ajax cm-ajax-full-render"{/if} action="{""|fn_url}" method="post" name="short_list_form{$obj_prefix}">
        <input type="hidden" name="result_ids" value="cart_status*,wish_list*,account_info*" />
        <input type="hidden" name="redirect_url" value="{$config.current_url}" />
        <input type="hidden" name="product_data[{$product.product_id}][extra][warehouse_id]" value="{$warehouse_data.warehouse_id}"/>

        <div class="cp-compact-list__name">
            <div class="ty-table__responsive-header visible-phone">{__("product")}</div>
            <div class="ty-table__responsive-content">
                <a href="{"products.view?product_id=`$product.product_id`&warehouse_id=`$warehouse_data.warehouse_id`"|fn_url}" class="product-title" title="{$product.product|strip_tags}" {live_edit name="product:product:{$product.product_id}" phrase=$product.product}>{$product.product nofilter}</a>
            </div>
        </div>

        <div class="cp-compact-list__manufacturer">
            <div class="ty-table__responsive-header visible-phone">{__("cp_np_manufacturer")}</div>
            <div class="ty-table__responsive-content">
                {$features.$manuf_feat_id.variant}
            </div>
        </div>

        <div class="cp-compact-list__manufacturer-code">
            <div class="ty-table__responsive-header visible-phone">{__("cp_ls_product_code")}</div>
            <div class="ty-table__responsive-content">
                {$features.$manuf_code_feat_id.variant}
            </div>
        </div>

        <div class="cp-compact-list__quantity">
            <div class="ty-table__responsive-header visible-phone">{__("quantity")}</div>
            <div class="ty-table__responsive-content">
                <span class="balance-amount">{$warehouse_data.amount}&nbsp;{__("items")}</span>
            </div>
        </div>

        <div class="cp-compact-list__price">
            <div class="ty-table__responsive-header visible-phone">{__("price")}&nbsp;{$currencies.$secondary_currency.symbol nofilter} </div>
            <div class="ty-table__responsive-content">
                {include file="addons/cp_warehouse_products_prices/components/warehouses_product_old_price.tpl"}
                {include file="addons/cp_warehouse_products_prices/components/warehouses_product_price.tpl"}

                {assign var="clean_price" value="clean_price_`$obj_id`"}
                {$smarty.capture.$clean_price nofilter}
            </div>
        </div>
        <div class="cp-compact-list__button">
            <a class="cp-np-mosts__2nd-line_arrow-link"  href="{"products.view?product_id=`$product.product_id`&warehouse_id=`$warehouse_data.warehouse_id`"|fn_url}"><span><i class="icon-spl-arrow-right"></i></span></a>
        </div>
    </form>
</div>
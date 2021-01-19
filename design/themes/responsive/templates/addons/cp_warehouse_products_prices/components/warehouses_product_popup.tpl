<div class="hidden warehouses-popup" title="{__("warehouses_product_popup_title")}" id="warehouses_product_popup_{$obj_id}">
    
    <div class="ty-warehouses-stores-list" 
        id="warehouses_list_{$block.block_id}" 
        data-ca-warehouses-view-selector="#warehouses_view_selector_{$block.block_id}_list"
    >
    <div class="ty-warehouses-stores-list__header">
        <div class="ty-warehouses-store__header-item ty-warehouses-store__header-item--name-wrapper">
            {__("warehouses.store_and_address")}
        </div>
        <div class="ty-warehouses-store__header-item ty-warehouses-store__header-item--availability">
            {__("availability")}
        </div>
        <div class="ty-warehouses-store__header-item ty-warehouses-store__header-item--price">
            {__("price")}
        </div>
    </div>
    <div class="ty-warehouses-stores-list__items" id="warehouses_list_items_{$block.block_id}">
            {foreach $items as $group_id => $stores_group}
                <div class="ty-warehouses-store__group" id="warehouses_list_items_{$block.block_id}_group_{$group_id}">
                    {if $show_store_groups}
                        <div class="ty-warehouses-store__group-name">
                            {$stores_group.name}
                        </div>
                    {/if}
                    {foreach $stores_group.items as $store}
                        {if $store.amount > 0}
                        <div class="ty-warehouses-stores-list__item"
                            data-ca-warehouses-store-group-selector="#warehouses_list_items_{$block.block_id}_group_{$group_id}"
                        >
                            <div class="ty-warehouses-store__name-wrapper">
                                <a class="ty-warehouses-store__name"
                                data-ca-warehouses-marker-selector="#warehouses_marker_{$block.block_id}_{$store.store_location_id}"
                                data-ca-warehouses-view-selector-off="#warehouses_list_{$block.block_id}"
                                data-ca-warehouses-view-selector-on="#warehouses_map_{$block.block_id}"
                                data-ca-warehouses-map-selector="#warehouses_map_{$block.block_id}_map"
                                >
                                    {$store.name}
                                </a>
                                <div class="ty-warehouses-store__address">
                                    {$store.pickup_address}
                                </div>
                            </div>

                            <div class="ty-warehouses-store__availability">
                                {*{if $capture_options_vs_qty}{capture name="product_options"}{$smarty.capture.product_options nofilter}{/if}
                                    {assign var="qty" value="qty_`$obj_id`"}
                                    {$smarty.capture.$qty nofilter}

                                    {assign var="min_qty" value="min_qty_`$obj_id`"}
                                    {$smarty.capture.$min_qty nofilter}
                                {if $capture_options_vs_qty}{/capture}{/if}*}
                                {$warehouse_data.amount = $store.amount}
                                {include file="addons/cp_warehouse_products_prices/components/warehouses_qty.tpl"}
                                <span class="available-qty">
                                    {if $store.amount}
                                        {if $settings.Appearance.in_stock_field === "YesNo::YES"|enum}
                                            {__("availability")}: {$store.amount}
                                        {else}
                                            <span class="ty-qty-in-stock">{__("in_stock")}</span>
                                        {/if}
                                    {elseif $store.is_available}
                                        {__("warehouses.shipping_delay.description.short", [
                                        "[shipping_delay]" => $store.shipping_delay
                                        ])}
                                    {else}
                                        {__("text_out_of_stock")}
                                    {/if}
                                </span>
                            </div>

                            <div class="ty-warehouses-store__price">
                                {if $warehouse_prices[$store.store_location_id]['price']}
                                    {include file="common/price.tpl" value=$warehouse_prices[$store.store_location_id]['price']  class="ty-list-price ty-nowrap"}
                                {else}
                                    {include file="common/price.tpl" value=$product.original_price|default:$product.base_price  class="ty-list-price ty-nowrap"}
                                {/if}
                            </div>

                            <div class="ty-warehouses-store__button">
                                {if $settings.Checkout.allow_anonymous_shopping == "allow_shopping" || $auth.user_id}
                                    {include file="buttons/button.tpl" but_onclick="fn_cp_set_warehouse_id_and_amount('{$product.product_id}','{$store.store_location_id}');" but_text=__("add_to_cart") but_role="text"  but_meta="cm-submit cm-post ty-btn__primary ty-btn__big ty-btn cm-dialog-closer" but_target_form="product_form_{$obj_id}" but_name='dispatch[separate_checkout.add]'} 
                                {else}
                                    {if $runtime.controller == "auth" && $runtime.mode == "login_form"}
                                        {assign var="login_url" value=$config.current_url}
                                    {else}
                                        {assign var="login_url" value="auth.login_form?return_url=`$c_url`"}
                                    {/if}
                                    {include file="buttons/button.tpl" but_id=$but_id but_text=__("sign_in_to_buy") but_href=$login_url but_role=$but_role|default:"text" but_name="" but_meta="ty_btn_sign_in_to_buy"}
                                {/if}
                            </div>
                        </div>
                        {/if}
                    {/foreach}
                </div>
            {/foreach}
        </div>
        <div class="ty-warehouses-stores-list__not-found hidden" id="warehouses_list_items_{$block.block_id}_not_found">
            <p class="ty-no-items">
                {__("warehouses.no_matching_stores_found")}
            </p>
        </div>
    </div>
</div>
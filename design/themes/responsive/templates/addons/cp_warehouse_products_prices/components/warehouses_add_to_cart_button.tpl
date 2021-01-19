
{if $product.tracking == "ProductTracking::TRACK_WITH_OPTIONS"|enum}
    {assign var="out_of_stock_text" value=__("text_combination_out_of_stock")}
{else}
    {assign var="out_of_stock_text" value=__("text_out_of_stock")}
{/if}

{if ($product.price|floatval || $product.zero_price_action == "P" || $product.zero_price_action == "A" || (!$product.price|floatval && $product.zero_price_action == "R")) && !($settings.Checkout.allow_anonymous_shopping == "hide_price_and_add_to_cart" && !$auth.user_id)}
    {assign var="show_price_values" value=true}
{else}
    {assign var="show_price_values" value=false}
{/if}
{capture name="show_price_values"}{$show_price_values}{/capture}

{assign var="cart_button_exists" value=false}
{assign var="show_qty" value=$show_qty|default:true}
{assign var="obj_id" value=$obj_id|default:$product.product_id}
{assign var="product_amount" value=$product.inventory_amount|default:$product.amount}
{assign var="show_sku_label" value=$show_sku_label|default:true}
{assign var="show_amount_label" value=$show_amount_label|default:true}
{if !$config.tweaks.disable_dhtml && !$no_ajax}
    {assign var="is_ajax" value=true}
{/if}
{if $show_add_to_cart}
<div class="cm-reload-{$obj_prefix}{$obj_id} {$add_to_cart_class}" id="add_to_cart_update_{$obj_prefix}{$obj_id}">
<input type="hidden" name="appearance[show_add_to_cart]" value="{$show_add_to_cart}" />
<input type="hidden" name="appearance[show_list_buttons]" value="{$show_list_buttons}" />
<input type="hidden" name="appearance[but_role]" value="{$but_role}" />
<input type="hidden" name="appearance[quick_view]" value="{$quick_view}" />

{strip}
{capture name="buttons_product"}
    {hook name="products:add_to_cart"}
        {if $product.has_options && !$show_product_options && !$details_page}
            {if $but_role == "text"}
                {$opt_but_role="text"}
            {else}
                {$opt_but_role="action"}
            {/if}

            {include file="buttons/button.tpl" but_id="button_cart_`$obj_prefix``$obj_id`" but_text=__("select_options") but_href="products.view?product_id=`$product.product_id`" but_role=$opt_but_role but_name="" but_meta="ty-btn__primary ty-btn__big"}
        {else}
                {$_but_id="button_cart_`$obj_prefix``$obj_id`"}

            {if $extra_button}{$extra_button nofilter}&nbsp;{/if}
            {include file="buttons/add_to_cart.tpl" but_id=$_but_id but_name="dispatch[checkout.add..`$obj_id`]" but_role=$but_role block_width=$block_width obj_id=$obj_id product=$product but_meta=$add_to_cart_meta}

            {assign var="cart_button_exists" value=true}
        {/if}
    {/hook}
{/capture}
    {if (
            $product.zero_price_action != "R"
            || $warehouse_data.price != 0
        )
        && (
            $settings.General.inventory_tracking != "Y"
            || $settings.General.allow_negative_amount == "Y"
            || (
                $warehouse_data.amount > 0
                && $warehouse_data.amount >= $product.min_qty
            )
            || $product.tracking == "ProductTracking::DO_NOT_TRACK"|enum
            || $product.is_edp == "Y"
            || $product.out_of_stock_actions == "OutOfStockActions::BUY_IN_ADVANCE"|enum
        )
        || (
            $product.has_options
            && !$show_product_options
        )}

        {if $smarty.capture.buttons_product|trim != '&nbsp;'}
            {if $product.avail_since <= $smarty.const.TIME || (
                $product.avail_since > $smarty.const.TIME && $product.out_of_stock_actions == "OutOfStockActions::BUY_IN_ADVANCE"|enum
            )}
                {$smarty.capture.buttons_product nofilter}
            {/if}
        {/if}

    {elseif ($settings.General.inventory_tracking == "Y" && $settings.General.allow_negative_amount != "Y" && (($warehouse_data.amount <= 0 || $warehouse_data.amount < $product.min_qty) && $product.tracking != "ProductTracking::DO_NOT_TRACK"|enum) && $product.is_edp != "Y")}
            {assign var="show_qty" value=false}
            {if !$details_page}
                {if (!$product.hide_stock_info && !(($warehouse_data.amount <= 0 || $warehouse_data.amount < $product.min_qty) && ($product.avail_since > $smarty.const.TIME)))}
                    <span class="ty-qty-out-of-stock ty-control-group__item" id="out_of_stock_info_{$obj_prefix}{$obj_id}">{$out_of_stock_text}</span>
                {/if}
            {elseif (($product.out_of_stock_actions == "OutOfStockActions::SUBSCRIBE"|enum) && ($product.tracking != "ProductTracking::TRACK_WITH_OPTIONS"|enum))}
                <div class="ty-control-group">
                    <label for="sw_product_notify_{$obj_prefix}{$obj_id}" class="ty-strong" id="label_sw_product_notify_{$obj_prefix}{$obj_id}">
                        <input id="sw_product_notify_{$obj_prefix}{$obj_id}" type="checkbox" class="checkbox cm-switch-availability cm-switch-visibility" name="product_notify" {if $product_notification_enabled == "Y"}checked="checked"{/if} onclick="
                            {if !$auth.user_id}
                                if (!this.checked) {
                                    Tygh.$.ceAjax('request', '{"products.product_notifications?enable="|fn_url}' + 'N&amp;product_id={$product.product_id}&amp;email=' + $('#product_notify_email_{$obj_prefix}{$obj_id}').get(0).value, {$ldelim}cache: false{$rdelim});
                                }
                            {else}
                                Tygh.$.ceAjax('request', '{"products.product_notifications?enable="|fn_url}' + (this.checked ? 'Y' : 'N') + '&amp;product_id=' + '{$product.product_id}', {$ldelim}cache: false{$rdelim});
                            {/if}
                        "/>{__("notify_when_back_in_stock")}
                    </label>
                </div>
                {if !$auth.user_id }
                <div class="ty-control-group ty-input-append ty-product-notify-email {if $product_notification_enabled != "Y"}hidden{/if}" id="product_notify_{$obj_prefix}{$obj_id}">

                    <input type="hidden" name="enable" value="Y" disabled />
                    <input type="hidden" name="product_id" value="{$product.product_id}" disabled />
                    
                    <label id="product_notify_email_label" for="product_notify_email_{$obj_prefix}{$obj_id}" class="cm-required cm-email hidden">{__("email")}</label>
                    <input type="text" name="email" id="product_notify_email_{$obj_prefix}{$obj_id}" size="20" value="{$product_notification_email|default:__("enter_email")}" class="ty-product-notify-email__input cm-hint" title="{__("enter_email")}" disabled />

                    <button class="ty-btn-go cm-ajax" type="submit" name="dispatch[products.product_notifications]" title="{__("go")}"><i class="ty-btn-go__icon ty-icon-right-dir"></i></button>

                </div>
                {/if}
            {/if}
    {/if}

    {if $show_list_buttons}
        {capture name="product_buy_now_`$obj_id`"}
            {$compare_product_id = $product.product_id}
            {hook name="products:buy_now"}
                {if $settings.General.enable_compare_products == "Y"}
                    {include file="buttons/add_to_compare_list.tpl" product_id=$compare_product_id}
                {/if}
            {/hook}
        {/capture}
        {assign var="capture_buy_now" value="product_buy_now_`$obj_id`"}

        {if $smarty.capture.$capture_buy_now|trim}
            {$smarty.capture.$capture_buy_now nofilter}
        {/if}
    {/if}

    {if ($product.avail_since > $smarty.const.TIME)}
        {include file="common/coming_soon_notice.tpl" avail_date=$product.avail_since add_to_cart=$product.out_of_stock_actions}
    {/if}

    {* Uncomment these lines in the overrides hooks for back-passing $cart_button_exists variable to the product_data template *}
    {if $cart_button_exists}
        {capture name="cart_button_exists"}Y{/capture}
    {/if}
{/strip}
<!--add_to_cart_update_{$obj_prefix}{$obj_id}--></div>
{/if}
{$obj_prefix = $block.snapping_id}
<div class="cp-info-order" id="checkout_info_{$block.snapping_id}">
    <div class="cp-info-order__vendor">
        {$first_product = $cart_products|current}
        {$company_name = $first_product.company_name}
        <div class="cp-info-order__vendor-name">
            <span class="cp-info-order__label">{__("vendor")}:</span> 
            <span class="cp-info-order__value">{$company_name}</span>
        </div>
        <div class="cp-info-order__vendor-stock">
            <span class="cp-info-order__label">{__("stock")}:</span> 
            <span class="cp-info-order__value">{$warehouse_address}</span>
        </div>
    </div>
    <div class="cp-info-order__user">
        <div class="cp-info-order__user-company">
            {$id_inn = $addons.cp_spl_theme.id_inn}
            <span class="cp-info-order__label">{__("customer")}:</span> 
            <span class="cp-info-order__value">{$user_data.company} ({__("inn")}: {$user_data.fields.$id_inn})</span>
        </div>
        <div class="cp-info-order__user-info">
            <span class="cp-info-order__label">{__("buyer_contact_person")}:</span> 
            <span class="cp-info-order__value">
            {if $user_data.lastname}{$user_data.lastname}{/if}{if $user_data.firstname} {$user_data.firstname}{/if}{if $user_data.phone}, {$user_data.phone|replace:'-':''}{/if}{if $user_data.email}, {$user_data.email}{/if}</span>
        </div>
        <div class="cp-info-order__recipient" id="cp_recipient_info">
        {if $recipient_data}
            <div class="cp-info-order__user-info cp-info-order__user-info_recipient">
                <span class="cp-info-order__label">{__("reciever")}:</span> 
                <span class="cp-info-order__value">
                {if $recipient_data.lastname}{$recipient_data.lastname}{/if}{if $recipient_data.firstname} {$recipient_data.firstname}{/if}{if $recipient_data.middlename} {$recipient_data.middlename}{/if}{if $recipient_data.phone}, {$recipient_data.phone|replace:'-':''}{/if}
                <span><a class="cm-ajax cm-ajax-full-render" data-ca-target-id="cp_recipient_info" href="{"checkout.checkout?delete_recipient=1"|fn_url}"><i class="ty-icon-cancel-circle"></i></a></span>
            </div>
        {else}
            {include file="buttons/button.tpl" 
                but_text=__("cp_another_recipient") 
                but_meta="cm-ajax cm-dialog-opener cm-dialog-auto-size ty-btn ty-btn__tertiary" 
                but_href="{"checkout.add_recipient"|fn_url}"
                but_dialog_class="cp-recipient-popup"
                but_title=__("cp_add_recipient")
            }
        {/if}
        <!--cp_recipient_info--></div>
    </div>

    <div class="cp-info-order__products">
        <div class="cp-info-order__products-header cm-combination open" id="sw_products_list_{$block.block_id}">
            <h2 class="litecheckout__step-title">{__("items_in_order")}</h2>
            <span class="cp-info-order__title-toggle">
                <i class="ty-icon-down-open"></i>
                <i class="ty-icon-up-open"></i>
            </span>
        </div>
        <div class="cp-info-order__products-list" id="products_list_{$block.block_id}">
            {foreach from=$cart_products key="key" item="product" name="cart_products"}
                <div class="cp-info-order__product">
                    <div class="cp-info-order__product-name">
                        <a class="cp-info-order__product-link" href="{"products.view?product_id=`$product.product_id`"|fn_url}">{$product.product nofilter}</a>
                    </div>
                    <div class="cp-info-order__product-quantity">
                        {$product.amount} {__("items")}
                         {* <div class="ty-center ty-value-changer cm-value-changer">
                            {if $settings.Appearance.quantity_changer == "Y"}
                            <a class="cm-increase ty-value-changer__increase">&#43;</a>
                            {/if}
                            <input {if $product.qty_step > 1}readonly="readonly"{/if} type="text" size="5" class="ty-value-changer__input cm-amount" id="qty_count_{$obj_prefix}_{$key}" name="product_data[{$key}][amount]" value="{$product.amount}"{if $product.qty_step > 1} data-ca-step="{$product.qty_step}"{/if} data-ca-min-qty="{if $product.min_qty > 1}{$product.min_qty}{else}1{/if}" />
                            {if $settings.Appearance.quantity_changer == "Y"}
                            <a class="cm-decrease ty-value-changer__decrease">&minus;</a>
                            {/if}
                        </div> *}
                    </div>
                    <div class="cp-info-order__product-sum">
                        {$total_price = $product.amount * $product.display_price}
                        {$total_base_price = $product.amount * $product.base_price}
                        {if $product.base_price > $product.price}
                            <span class="ty-price_old">
                                {include file="common/price.tpl" value=$total_base_price class="ty-list-price"}
                            </span>
                        {/if}
                        <span class="ty-price_real">
                        {include file="common/price.tpl" value=$total_price span_id="price_total_`$key`_`$dropdown_id`" class="ty-nowrap"}
                        </span>
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
<!--checkout_info_{$block.snapping_id}--></div>
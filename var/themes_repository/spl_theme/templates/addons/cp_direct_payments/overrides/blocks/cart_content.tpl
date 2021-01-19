{assign var="dropdown_id" value=$block.snapping_id}
{assign var="r_url" value=$config.current_url|escape:url}
{hook name="checkout:cart_content"}

    <div class="ty-dropdown-box" id="cart_status_{$dropdown_id}">
         <div id="sw_dropdown_{$dropdown_id}" class="ty-dropdown-box__title cm-combination">
            <a href="{"checkout.cart"|fn_url}" class="ty-minicart_header">
                {hook name="checkout:dropdown_title"}
                    {$cart_amount = $cart.amount|default:0}
                    <span class="icon-spl-shopping"></span>
                    <span class="ty-minicart-amount hidden-phone">{__("n_products", [$cart_amount])}</span>
                    <span class="ty-minicart-amount_phone hidden-tablet hidden-desktop">{$cart_amount}</span>
                {/hook}
            </a>
        </div>
        <div id="dropdown_{$dropdown_id}" class="cm-popup-box ty-dropdown-box__content hidden">
            {hook name="checkout:minicart"}
                <div class="cm-cart-content {if $block.properties.products_links_type == "thumb"}cm-cart-content-thumb{/if} {if $block.properties.display_delete_icons == "Y"}cm-cart-content-delete{/if}">
                        <div class="ty-cart-items">
                            {if $cart.amount}
                                <ul class="ty-minicart__list">
                                    {hook name="index:cart_status"}
                                        {assign var="_cart_products" value=$cart.products|array_reverse:true}

                                        {foreach from=$_cart_products key="key" item="product" name="cart_products"}
                                            {hook name="checkout:minicart_product"}
                                            {if !$product.extra.parent}
                                                <li class="ty-minicart__item">
                                                    {hook name="checkout:minicart_product_info"}
                                                    {if $block.properties.products_links_type == "thumb"}
                                                    <div class="ty-minicart__item-image">
                                                        {include file="common/image.tpl" image_width="90" image_height="90" images=$product.main_pair no_ids=true}
                                                    </div>
                                                    {/if}
                                                    <div class="ty-minicart__item-desc">
                                                        <a class="ty-minicart__item-name" href="{"products.view?product_id=`$product.product_id`"|fn_url}">
                                                            {$product.product|default:fn_get_product_name($product.product_id) nofilter}
                                                        </a>
                                                        <span class="ty-minicart__item-product-code">{$product.product_code}</span>
                                                    </div>

                                                    <div class="ty-minicart__item-info-price">
                                                        <div class="ty-minicart__item-qty">
                                                            <div class="ty-center ty-value-changer cm-value-changer">
                                                                {if $settings.Appearance.quantity_changer == "Y"}
                                                                <a class="cm-increase ty-value-changer__increase">&#43;</a>
                                                                {/if}
                                                                <input {if $product.qty_step > 1}readonly="readonly"{/if} type="text" size="5" class="ty-value-changer__input cm-amount" id="qty_count_{$obj_prefix}{$obj_id}" name="product_data[{$obj_id}][amount]" value="{$product.amount}"{if $product.qty_step > 1} data-ca-step="{$product.qty_step}"{/if} data-ca-min-qty="{if $product.min_qty > 1}{$product.min_qty}{else}1{/if}" />
                                                                {if $settings.Appearance.quantity_changer == "Y"}
                                                                <a class="cm-decrease ty-value-changer__decrease">&minus;</a>
                                                                {/if}
                                                            </div>
                                                        </div>
                                                        <div class="ty-minicart__item-price">
                                                            {$total_price = $product.amount * $product.display_price}
                                                            <span class="ty-minicart__item-total-price">
                                                                {include file="common/price.tpl" value=$total_price span_id="price_total_`$key`_`$dropdown_id`" class="none"}
                                                            </span>
                                                            <span class="ty-minicart__item-display-price">
                                                                {include file="common/price.tpl" value=$product.display_price span_id="price_`$key`_`$dropdown_id`" class="none"}/{__("items")}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="ty-minicart__item-buttons">
                                                        {if $product.extra.warehouse_id > 0}
                                                            {assign var="cp_warehouse_id" value="`$product.company_id`_`$product.extra.warehouse_id`"}
                                                        {else}
                                                            {assign var="cp_warehouse_id" value=$product.company_id}
                                                        {/if}
                                                        {if $block.properties.display_delete_icons == "Y"}
                                                            {if (!$runtime.checkout || $force_items_deletion) && !$product.extra.exclude_from_calculate}
                                                                <div class="ty-minicart__item-delete cm-cart-item-delete">
                                                                    {include file="buttons/button.tpl" 
                                                                        but_href="checkout.delete.from_status?vendor_id=`$cp_warehouse_id`&cart_id=`$key`&redirect_url=`$r_url`" 
                                                                        but_meta="cm-ajax cm-ajax-full-render" 
                                                                        but_target_id="cart_status*" 
                                                                        but_role="delete" 
                                                                        but_name="delete_cart_item"
                                                                        but_icon="icon-spl-delete"
                                                                    }
                                                                </div>
                                                            {/if}
                                                        {/if}
                                                    </div>
                                                    {/hook}
                                                </li>
                                            {/if}
                                            {/hook}
                                        {/foreach}
                                    {/hook}
                                </ul>
                            {else}
                                <div class="ty-cart-items__empty ty-center">{__("cart_is_empty")}</div>
                            {/if}
                        </div>

                        {if $block.properties.display_bottom_buttons == "Y"}
                        <div class="cm-cart-buttons ty-cart-content__buttons{if $cart.amount} full-cart{else} hidden{/if}">
                            <div class="ty-minicart__promo">
                                <span class="icon-spl-delivery"></span>
                                <span class="ty-minicart__promo-text">{__("minicart_promo_text")}</span>
                            </div>
                            <div class="ty-minicart__total">
                                <div class="ty-minicart__total-sum">
                                    <span class="ty-minicart__total-sum-label">
                                        {__("total")}:
                                    </span>
                                    <span class="ty-minicart__total-sum-price">
                                        {include file="common/price.tpl" value=$cart.display_subtotal class="none"}
                                    </span>
                                </div>
                                <div class="ty-minicart__total-buttons">
                                    <a href="{"checkout.cart"|fn_url}" rel="nofollow" class="ty-btn ty-btn__secondary ty-btn__checkout-cart"><span class="icon-spl-cart"></span></a>
                                    {if $settings.Checkout.checkout_redirect != "Y"}
                                        {$vendor_id = $cart.vendor_ids|reset}
                                        <div class="ty-float-right">
                                            {include
                                                file="buttons/proceed_to_checkout.tpl"
                                                but_text=__("checkout")
                                                but_href="checkout.checkout?vendor_id=`$vendor_id`"|fn_url
                                            }
                                        </div>
                                    {/if}
                                </div>
                            </div>
                        </div>
                        {/if}

                </div>
            {/hook}
        </div>
    <!--cart_status_{$dropdown_id}--></div>
{/hook}
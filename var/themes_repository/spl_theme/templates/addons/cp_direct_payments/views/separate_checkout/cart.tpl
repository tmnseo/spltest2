{script src="js/tygh/exceptions.js"}
{script src="js/tygh/checkout.js"}
{script src="js/tygh/cart_content.js"}

{if $carts}
    <div class="ty-cart__header">
        <h1 class="ty-mainbox-title ty-mainbox-title__cart">{__("cart_contents")}</h1>
        <span class="ty-cart__header-text">{__("the_amount_is_indicated_excluding_delivery")}</span>
    </div>

    {foreach $carts as $vendor_id => $cart}
        {assign var="vendor_id_clear" value=$vendor_id|fn_cp_direct_helper_separate_ids}
        {include file="addons/cp_direct_payments/views/separate_checkout/components/cart_content.tpl"
                 vendor_id=$vendor_id
                 vendor=$vendors.$vendor_id_clear.company
                 location_warehouse = $cart.store_location_data
                 cart=$cart
                 cart_products=$group_cart_products.$vendor_id
                 product_groups=$group_product_groups.$vendor_id
                 checkout_add_buttons=$group_checkout_add_buttons.$vendor_id
                 take_surcharge_from_vendor=$group_take_surcharge_from_vendor.$vendor_id
                 payment_methods=$group_payment_methods.$vendor_id
        }
    {/foreach}

    <div class="buttons-container ty-cart-content__bottom-buttons clearfix">
        {include file="addons/cp_direct_payments/views/separate_checkout/components/checkout_totals.tpl"
             location="cart"}
        <div class="ty-cart-content__right-buttons">
            {if count($carts) > 1}
                {if !$auth.user_id
                    && $settings.Checkout.disable_anonymous_checkout == "YesNo::YES"|enum
                }
                    {$but_meta = "ty-btn__primary ty-btn__all-orders"}
                    {$return_url = "checkout.place_all_orders"|fn_url}

                    <a
                        class="cm-dialog-opener cm-dialog-auto-size ty-btn {$but_meta}"
                        href="{"checkout.cp_checkout_login_form?return_url=`$return_url|urlencode`"|fn_url}"
                        data-ca-dialog-title="{__("sign_in")}"
                        data-ca-target-id="checkout_login_form"
                        rel="nofollow">
                        {__("place_all_orders")}
                    </a>
                {else}
                    <a class="ty-btn ty-btn__primary ty-btn__all-orders" href="{"checkout.place_all_orders"|fn_url}" >{__("place_all_orders")}</a>
                {/if}
            {else}
                {if !$auth.user_id
                    && $settings.Checkout.disable_anonymous_checkout == "YesNo::YES"|enum
                }
                    {$but_meta = "ty-btn__primary ty-btn__all-orders"}
                    {$return_url = "checkout.place_all_orders"|fn_url}

                    <a
                        class="cm-dialog-opener cm-dialog-auto-size ty-btn {$but_meta}"
                        href="{"checkout.cp_checkout_login_form?return_url=`$return_url|urlencode`"|fn_url}"
                        data-ca-dialog-title="{__("sign_in")}"
                        data-ca-target-id="checkout_login_form"
                        rel="nofollow">
                        {__("checkout")}
                    </a>
                {else}
                    <a  href="{"checkout.place_all_orders"|fn_url}" class="ty-btn ty-btn__primary ty-btn__all-orders" >{__("checkout")}</a>
                {/if}
            {/if}
        </div>
    </div>
{else}
    <p class="ty-no-items">{__("text_cart_empty")}</p>

    <div class="buttons-container wrap">
        {include file="buttons/continue_shopping.tpl" but_href=$continue_url|fn_url but_role="submit"}
    </div>
{/if}
{include file="addons/cp_direct_payments/views/separate_checkout/components/popup_place_all_orders.tpl"}
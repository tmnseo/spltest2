{script src="js/tygh/exceptions.js"}
{script src="js/tygh/checkout.js"}
{script src="js/tygh/cart_content.js"}

{if $carts}
    <h1 class="ty-mainbox-title {if $carts|count > 1}ty-mve-title{/if}">{__("cart_contents")}</h1>

    <div class="buttons-container ty-cart-content__top-buttons clearfix">
        <div class="ty-float-left ty-cart-content__left-buttons">
            {include file="buttons/continue_shopping.tpl"
                        but_href=$continue_url|fn_url
            }
            {include file="buttons/clear_cart.tpl"
                        but_href="checkout.clear"
                        but_role="text"
                        but_meta="cm-confirm ty-cart-content__clear-button"
            }
        </div>

        <div class="ty-float-right ty-cart-content__right-buttons">
            {if $carts|count > 0}
            <div class="ty-mve-total"
                 id="checkout_totals_header_general">
                {__("max_amount")}:&nbsp;{include file="common/price.tpl" value=$carts_total class="ty-price"}
            <!--checkout_totals_header_general--></div>
            {/if}
            <a class="ty-btn ty-btn__secondary ty-btn__all-orders cm-dialog-opener cm-dialog-auto-size" data-ca-dialog-class="popup-place-all-orders" data-ca-target-id="place_all_orders">{__("place_all_orders")}</a>
        </div>
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

    {include file="addons/cp_direct_payments/views/separate_checkout/components/checkout_totals.tpl"
             location="cart"}

    <div class="buttons-container ty-cart-content__bottom-buttons clearfix">
        <div class="ty-float-left ty-cart-content__left-buttons">
            {include file="buttons/continue_shopping.tpl"
                        but_href=$continue_url|fn_url
            }
            {include file="buttons/clear_cart.tpl"
                        but_href="checkout.clear"
                        but_role="text"
                        but_meta="cm-confirm ty-cart-content__clear-button"
            }
        </div>
        
        <div class="ty-float-right ty-cart-content__right-buttons">
            <a class="ty-btn ty-btn__secondary ty-btn__all-orders cm-dialog-opener cm-dialog-auto-size" data-ca-dialog-class="popup-place-all-orders" data-ca-target-id="place_all_orders">{__("place_all_orders")}</a>
        </div>
    </div>
{else}
    <p class="ty-no-items">{__("text_cart_empty")}</p>

    <div class="buttons-container wrap">
        {include file="buttons/continue_shopping.tpl" but_href=$continue_url|fn_url but_role="submit"}
    </div>
{/if}
{include file="addons/cp_direct_payments/views/separate_checkout/components/popup_place_all_orders.tpl"}

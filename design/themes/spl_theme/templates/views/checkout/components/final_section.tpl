{$show_place_order = false}

{if $cart|fn_allow_place_order:$auth}
    {$show_place_order = true}
{/if}

{if $recalculate && !$cart.amount_failed}
    {$show_place_order = true}
{/if}

{if $show_place_order}

    <div class="clearfix {if !$is_payment_step} checkout__block ty-checkout-block-terms{/if}">
        {hook name="checkout:final_section_customer_notes"}
        {/hook}
    </div>

    <input type="hidden" name="update_steps" value="1" />
    
    {if !$iframe_mode}
        <div class="litecheckout__item litecheckout__item--full litecheckout__submit-order">

            <span class="litecheckout-total-cost">
                <span class="litecheckout__order-number">
                    {__("total_cost_order_number")}
                    {if count($carts) > 1 || $cp_completed_orders}
                        &nbsp;{__("number_symbol")} {$cp_current_checkout_order_number}
                        <input type="hidden" name="cp_current_checkout_order_number" value="{$cp_current_checkout_order_number}">
                    {/if}    
                </span>
                <span class="litecheckout__total-cost">
                {include file="common/price.tpl" value=$_total|default:$cart.total}
                {if $cart.taxes && $settings.Appearance.cart_prices_w_taxes == "Y"}<span class="ty-list__clean">{__("cp_spl_theme.including_tax")}</span>{/if}
                </span>
            </span>
            {include
                file="buttons/place_order.tpl"
                but_name="dispatch[checkout.place_order]"
                but_role="big"
                but_id="litecheckout_place_order"
            }
        </div>
    {/if}

{else}

    {if $cart.amount_failed}
        <div class="checkout__block">
            <p class="ty-error-text">{__("text_min_order_amount_required")}&nbsp;<strong>{include file="common/price.tpl" value=$settings.Checkout.min_order_amount}</strong></p>
        </div>
    {/if}

    <div class="litecheckout__item litecheckout__submit-order">
        {include file="buttons/continue_shopping.tpl" but_href=$continue_url|fn_url but_role="action"}
    </div>
    
{/if}

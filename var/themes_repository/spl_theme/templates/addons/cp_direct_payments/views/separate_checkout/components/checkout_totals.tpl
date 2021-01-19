<div class="ty-cart-total clearfix cm-reload" id="checkout_totals{$suffix_key}">
    <span class="ty-cart-total__label">{__("total_cost_excluding_shipping")}:</span>
    <span class="ty-cart-total__value">{include file="common/price.tpl" value=$carts_total|default:$smarty.capture._total|default:$cart.total span_id="cart_total" class="ty-price"}</span>
<!--checkout_totals{$suffix_key}--></div>


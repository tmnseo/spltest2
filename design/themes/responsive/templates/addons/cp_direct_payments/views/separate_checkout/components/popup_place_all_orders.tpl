<div id="place_all_orders" class="cp-all-orders-popup hidden">
    <p class="cp-all-orders-popup__text"><strong>{__("cp_all_orders_popup_text_strong")}</strong></p>
    <p class="cp-all-orders-popup__text"><span>{__("cp_all_orders_popup_text")}</span></p>
    <div class="cp-all-orders-popup__buttons">
        {include file="buttons/button.tpl"
            but_id=$but_id
            but_text=__("ok")
            but_meta="ty-btn__secondary ty-btn__all-orders_ok"
            but_role="text"
            but_href="checkout.place_all_orders"
        }
        {include file="buttons/button.tpl"
                 but_id="cp_all_orders_cancel"
                 but_text=__("cp_direct_payments.cancel")
                 but_meta="ty-btn__all-orders_cancel"
                 but_role="text"
        }
    </div>
</div>
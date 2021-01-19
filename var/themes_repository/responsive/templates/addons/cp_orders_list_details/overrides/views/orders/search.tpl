{*
{capture name="section"}
    {include file="views/orders/components/orders_search_form.tpl"}
{/capture}
{include file="common/section.tpl" section_title=__("search_options") section_content=$smarty.capture.section class="ty-search-form" collapse=true}
*}
{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
{if $search.sort_order == "asc"}
{assign var="sort_sign" value="<i class=\"ty-icon-down-dir\"></i>"}
{else}
{assign var="sort_sign" value="<i class=\"ty-icon-up-dir\"></i>"}
{/if}
{if !$config.tweaks.disable_dhtml}
    {assign var="ajax_class" value="cm-ajax"}

{/if}

{include file="common/pagination.tpl"}

<div class="cp-oc__orders">
    {foreach from=$orders item="o"}
        <div class="cp-oc__orders-item">
            <div class="cp-oc__orders-item_top">
                <div class="cp-oc__orders-item_top-column">
                    {hook name="cp_orders_list:top_left_column"}
                    <div class="cp-oc__orders-item_top_order">
                        {__("order")} №{$o.order_id}
                    </div>
                    <div class="cp-oc__orders-item_top_status">
                        {include file="addons/cp_orders_list_details/components/status.tpl" status=$o.status}
                    </div>
                    {/hook}
                </div>
                <div class="cp-oc__orders-item_top-column cp-oc__orders-item_mob-hide">
                    {hook name="cp_orders_list:top_middle_column"}
                    {if $cp_allowed_cancel && in_array($o.status, $cp_allowed_cancel) && $is_cancel_status && $is_cancel_status != $o.status}
                        <div class="cp-oc__orders-item_top-link">
                            <a class="cm-post cm-confirm" href="{"orders.cp_oc_cancel_order?order_id=`$o.order_id`"|fn_url}">{__("cp_oc_cancel_order")}</a>
                        </div>
                    {/if}
                    <div class="cp-oc__orders-item_top-link">
                        <a class="cm-confirm" href="{"orders.reorder?order_id=`$o.order_id`"|fn_url}">{__("re_order")}</a>
                    </div>
                    {*
                    <div class="cp-oc__orders-item_top-link">
                        <a class="cm-confirm" href="">{__("cp_oc_support_txt")}</a>
                    </div>
                    *}
                    {/hook}
                </div>
                <div class="cp-oc__orders-item_top-column cp-oc__orders-item_mob-hide">
                    {hook name="cp_orders_list:top_rigth_column"}
                    {/hook}
                </div>
            </div>
            <div class="cp-oc__orders-item_middle">
                <div class="cp-oc__orders-item_middle_title">
                    {__("cp_oc_order_info")}:
                </div>
                <div class="cp-oc__orders-item_middle_flex">
                    <div class="cp-oc__orders-item_middle-column cp-oc__name">
                        <div class="cp-oc__orders-item_middle-column-title">{__("vendor")}</div>
                        <div class="cp-oc__orders-item_middle-column-value"><a href="{"companies.products?company_id=`$o.company_id`"|fn_url}">{$o.company_name}</a></div>
                    </div>
                    <div class="cp-oc__orders-item_middle-column cp-oc__recipient">
                        <div class="cp-oc__orders-item_middle-column-title">{if $o.cp_recipient_data}{__("buyer_contact_person")}{else}{__("recipient")}{/if}</div>
                        <div class="cp-oc__orders-item_middle-column-value">
                            {$o.lastname} {$o.firstname}, {$o.phone|replace:'-':''}
                        </div>
                        {if $o.cp_recipient_data}
                        <div class="cp-oc__orders-item_middle-column-title">{__("recipient")}</div>
                        <div class="cp-oc__orders-item_middle-column-value">
                            {if $o.cp_recipient_data}
                                {if $o.cp_recipient_data.lastname}{$o.cp_recipient_data.lastname}{/if}{if $o.cp_recipient_data.firstname} {$o.cp_recipient_data.firstname}{/if}{if $o.cp_recipient_data.middlename} {$o.cp_recipient_data.middlename}{/if}{if $o.cp_recipient_data.phone}, {$o.cp_recipient_data.phone|replace:'-':''}{/if}
                            {else}
                                {if $o.lastname}{$o.lastname}{/if}{if $o.firstname} {$o.firstname}{/if}{if $o.phone}, {$o.phone|replace:'-':''}{/if}
                            {/if}
                        </div>
                        {/if}
                    </div>
                    <div class="cp-oc__orders-item_middle-column cp-oc__data">
                        <div class="cp-oc__orders-item_middle-column-title">{__("date")}</div>
                        <div class="cp-oc__orders-item_middle-column-value">{$o.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</div>
                    </div>
                    {hook name="cp_orders_list:middle_data"}{/hook}
                    <div class="cp-oc__orders-item_middle-column cp-oc__total">
                        <div class="cp-oc__orders-item_middle-column-title">{__("subtotal")}</div>
                        <div class="cp-oc__orders-item_middle-column-value">{include file="common/price.tpl" value=$o.total}</div>
                    </div>
                    <div class="cp-oc__orders-item_middle-column cp-oc__tracking-number">
                        <div class="cp-oc__orders-item_middle-column-title">{if $o.cp_is_pick_up_order}{__("pickup")}{else}{__("tracking_number")}{/if}</div>
                        <div class="cp-oc__orders-item_middle-column-value">
                            {if $o.cp_is_pick_up_order && $o.cp_pickup_store_data}
                                {foreach from=$o.cp_pickup_store_data item="cp_shipping_method"}
                                    {if $cp_shipping_method.store_data}
                                        {$cp_shipping_method.store_data.city}{if $cp_shipping_method.store_data.pickup_address}, {$cp_shipping_method.store_data.pickup_address}{/if}</br>
                                        {if $cp_shipping_method.store_data.pickup_phone}
                                            {__("phone")}: {$cp_shipping_method.store_data.pickup_phone}</br>
                                        {/if}
                                        {if $cp_shipping_method.store_data.pickup_time}
                                            {__("store_locator.work_time")}: {$cp_shipping_method.store_data.pickup_time}</br>
                                        {/if}
                                        {$cp_shipping_method.store_data.description nofilter}
                                    {/if}
                                {/foreach}
                            {else}
                                {if $o.cp_tracking_url}<a target="_blank" href="{$o.cp_tracking_url}" rel="nofollow">{$o.tracking_number}</a>{else}{$o.tracking_number}{/if}
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
            <div class="cp-oc__orders-item_bot">
                <div class="cp-oc__orders-item_bot_title">
                    <a class="cp-oc__get_details-click" data-cp-orderid="{$o.order_id}" data-cp-result="cp_orders_list_details_{$o.order_id}">{__("cp_oc_you_ordered")}<i class="ty-icon-down-open"></i></a>
                </div>
                {include file="addons/cp_orders_list_details/components/order_products.tpl"}
            </div>
        </div>
        <div class="cp-oc__orders-btns">
            {if $cp_allowed_cancel && in_array($o.status, $cp_allowed_cancel) && $is_cancel_status && $is_cancel_status != $o.status}
                <a class="cp-oc__orders-btns-item cm-post cm-confirm" href="{"orders.cp_oc_cancel_order?order_id=`$o.order_id`"|fn_url}">{__("cp_oc_cancel_order")}</a>
            {/if}
            <a class="cp-oc__orders-btns-item cm-confirm" href="{"orders.reorder?order_id=`$o.order_id`"|fn_url}">{__("re_order")}</a>
            <a class="cp-oc__orders-btns-item cp-oc__expand_btns" data-cp-orderid="{$o.order_id}">{__("cp_oc_more_txt")}</a>
            <div class="hidden" id="more_mobile_btns_{$o.order_id}">
            {hook name="cp_orders_list:mobile_bts"}{/hook}
            </div>
        </div>
    {foreachelse}
        <p class="ty-no-items">{__("text_no_orders")}</p>
    {/foreach}
</div>
{include file="common/pagination.tpl"}

{capture name="mainbox_title"}{__("orders")}{/capture}
{hook name="orders:order_row"}
<tr>
    <td class="left mobile-hide">
        <input type="checkbox" name="order_ids[]" value="{$o.order_id}" class="cm-item cm-item-status-{$o.status|lower}" /></td>
    <td data-th="{__("id")}">
        <a href="{"orders.details?order_id=`$o.order_id`"|fn_url}" class="underlined">{__("order")} <bdi>#{$o.order_id}</bdi></a>
        {if $order_statuses[$o.status].params.appearance_type == "I" && $o.invoice_id}
            <p class="muted">{__("invoice")} #{$o.invoice_id}</p>
        {elseif $order_statuses[$o.status].params.appearance_type == "C" && $o.credit_memo_id}
            <p class="muted">{__("credit_memo")} #{$o.credit_memo_id}</p>
        {/if}
        {include file="views/companies/components/company_name.tpl" object=$o}
    </td>
    <td data-th="{__("status")}">
        {if "MULTIVENDOR"|fn_allowed_for}
            {assign var="notify_vendor" value=true}
        {else}
            {assign var="notify_vendor" value=false}
        {/if}
        
        {if $auth.user_type == "V"}
            {$allowed_order_statuses = fn_cp_get_allowed_order_statuses($order_status_descr, $o.status, $cp_unallowed_statuses)}
        {else}
            {$allowed_order_statuses = $order_status_descr}
        {/if}

        {include file="common/select_popup.tpl"
                 suffix="o"
                 order_info=$o
                 id=$o.order_id
                 status=$o.status
                 items_status=$allowed_order_statuses
                 update_controller="orders"
                 notify=true
                 notify_department=true
                 notify_vendor=$notify_vendor
                 status_target_id="orders_total,`$rev`"
                 extra="&return_url=`$extra_status`"
                 statuses=$order_statuses
                 btn_meta="btn btn-info o-status-`$o.status` btn-small"|lower
                 text_wrap=true
        }
        {if $auth.user_type == 'V' && $o.status && $cp_before_completed_statuses && in_array($o.status, $cp_before_completed_statuses)}
            {include file="addons/cp_edost_improvement/components/size_before_completed.tpl" order_id=$o.order_id}
        {/if}
        {if $o.issuer_name}
        <p class="muted shift-left">{$o.issuer_name}</p>
        {/if}
    </td>
    <td class="nowrap" data-th="{__("date")}">{$o.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
    <td data-th="{__("customer")}">
        {if $o.email}<a href="mailto:{$o.email|escape:url}">@</a> {/if}
        {if $o.user_id}<a href="{"profiles.update?user_id=`$o.user_id`"|fn_url}">{/if}{$o.lastname} {$o.firstname}{if $o.user_id}</a>{/if}
        {if $o.company}<p class="muted">{$o.company}</p>{/if}
    </td>
    <td {if $o.phone}data-th="{__("phone")}"{/if}><bdi><a href="tel:{$o.phone}">{$o.phone}</a></bdi></td>

    {hook name="orders:manage_data"}{/hook}

    <td width="5%" class="center" data-th="{__("tools")}">
        {capture name="tools_items"}
            <li>{btn type="list" href="orders.details?order_id=`$o.order_id`" text={__("view")}}</li>
            {hook name="orders:list_extra_links"}
                <li>{btn type="list" href="order_management.edit?order_id=`$o.order_id`" text={__("edit")}}</li>
                <li>{btn type="list" href="order_management.edit?order_id=`$o.order_id`&copy=1" text={__("copy")}}</li>
                {assign var="current_redirect_url" value=$config.current_url|escape:url}
                <li>{btn type="list" href="orders.delete?order_id=`$o.order_id`&redirect_url=`$current_redirect_url`" class="cm-confirm" text={__("delete")} method="POST"}</li>
            {/hook}
        {/capture}
        <div class="hidden-tools">
            {dropdown content=$smarty.capture.tools_items}
        </div>
    </td>
    <td class="right" data-th="{__("total")}">
        {include file="common/price.tpl" value=$o.total}
    </td>
</tr>
{/hook}
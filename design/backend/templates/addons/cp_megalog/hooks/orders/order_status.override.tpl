{*
{hook name="orders:order_status"}
    {if $order_info.status == $smarty.const.STATUS_INCOMPLETED_ORDER}
        {assign var="get_additional_statuses" value=true}
    {else}
        {assign var="get_additional_statuses" value=false}
    {/if}
    {assign var="order_status_descr" value=$smarty.const.STATUSES_ORDER|fn_get_simple_statuses:$get_additional_statuses:true}
    {assign var="extra_status" value=$config.current_url|escape:"url"}
    {if "MULTIVENDOR"|fn_allowed_for}
        {assign var="notify_vendor" value=true}
    {else}
        {assign var="notify_vendor" value=false}
    {/if}

    {$statuses = []}
    {assign var="order_statuses" value=$smarty.const.STATUSES_ORDER|fn_get_statuses:$statuses:$get_additional_statuses:true}
    {include file="addons/cp_megalog/components/select_popup.tpl" suffix="o" id=$order_info.order_id status=$order_info.status items_status=$order_status_descr update_controller="orders" notify=true notify_department=true notify_vendor=$notify_vendor status_target_id="content_downloads,cp_planned_time_issuing_order_div,cp_tracking_number" extra="&return_url=`$extra_status`" statuses=$order_statuses popup_additional_class="dropleft" text_wrap=true }
{/hook}
*}
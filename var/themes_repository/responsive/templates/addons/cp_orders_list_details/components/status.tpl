{if !$cp_order_statuses || ($cp_order_statuses && !$cp_order_statuses.$status)}
    {$order_status_descr=$smarty.const.STATUSES_ORDER|fn_get_simple_statuses}
{else}
    {$order_status_descr=$cp_order_statuses.$status.description}
{/if}
{if $cp_order_statuses && $cp_order_statuses.$status && $cp_order_statuses.$status.params && $cp_order_statuses.$status.params.color}
    {$status_color=$cp_order_statuses.$status.params.color}
{/if}
<span class="cp-oc-order__status-color" style="background-color: {$status_color};"> </span><span>{$order_status_descr}</span>

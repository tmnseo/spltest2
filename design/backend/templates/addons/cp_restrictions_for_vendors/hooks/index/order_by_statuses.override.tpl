{hook name="index:order_by_statuses"}
{if $user_can_view_orders && $order_by_statuses && $auth.user_type != 'V'}
    <div class="dashboard-table dashboard-table-order-by-statuses">
        <h4>{__("order_by_status")}</h4>
        <div class="table-wrap" id="dashboard_order_by_status">
            <table class="table">
                <thead>
                <tr>
                    <th width="25%">{__("status")}</th>
                    <th width="25%">{__("qty")}</th>
                    <th width="25%">{__(total)}</th>
                    <th width="25%">{__("shipping")}</th>
                </tr>
                </thead>
            </table>
            <div class="scrollable-table">
                <table class="table table-striped table--relative">
                    <tbody>
                    {foreach from=$order_by_statuses item="order_status"}
                        {$url = "orders.manage?is_search=Y&period=C&time_from=`$time_from`&time_to=`$time_to`&status[]=`$order_status.status`"|fn_url}
                        <tr>
                            <td width="25%"><a class="a--text-wrap" href="{$url}" title="{$order_status.status_name}">{$order_status.status_name}</a></td>
                            <td width="25%">{$order_status.count}</td>
                            <td width="25%">{include file="common/price.tpl" value=$order_status.total}</td>
                            <td width="25%">{include file="common/price.tpl" value=$order_status.shipping}</td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
            <!--dashboard_order_by_status--></div>
    </div>
{else}
<div class="hidden"></div>
{/if}
{/hook}
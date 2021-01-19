{hook name="index:finance_statistic"}
{if "MULTIVENDOR"|fn_allowed_for}
    {if $runtime.company_id && $auth.user_type != 'V'}
        <div class="dashboard-card dashboard-card--balance">
            <div class="dashboard-card-title">{__("vendor_payouts.current_balance_text")}</div>
            <div class="dashboard-card-content">
                <h3>
                    {hook name="index:finance_statistic_balance"}
                        <a href="{"companies.balance"|fn_url}"
                        >{include file="common/price.tpl" value=$current_balance}</a>
                    {/hook}
                </h3>
                &nbsp;
            </div>
        </div>
    {/if}
    {if isset($period_income)}
        <div class="dashboard-card">
            <div class="dashboard-card-title">{__("vendor_payouts.income")}</div>
            <div class="dashboard-card-content">
                <h3>
                    {include file="common/price.tpl" value=$period_income}
                </h3>
                &nbsp;
            </div>
        </div>
    {/if}
{/if}
{if !empty($orders_stat.orders)}
    <div class="dashboard-card">
        <div class="dashboard-card-title">{__("orders")}</div>
        <div class="dashboard-card-content">
            <h3>
                {if $user_can_view_orders}
                    <a href="{"orders.manage?is_search=Y&period=C&time_from=`$time_from`&time_to=`$time_to`"|fn_url}">{$orders_stat.orders|count}</a>
                {else}
                    {$orders_stat.orders|count}
                {/if}
            </h3>
            {$orders_stat.prev_orders|count}, {if $orders_stat.diff.orders_count > 0}+{/if}{$orders_stat.diff.orders_count}
        </div>
    </div>
{/if}
{if !empty($orders_stat.orders_total)}
    <div class="dashboard-card">
        <div class="dashboard-card-title">{__("sales")}</div>
        <div class="dashboard-card-content">
            <h3>{include file="common/price.tpl" value=$orders_stat.orders_total.totally_paid}</h3>{include file="common/price.tpl" value=$orders_stat.prev_orders_total.totally_paid}, {if $orders_stat.orders_total.totally_paid > $orders_stat.prev_orders_total.totally_paid}+{/if}{$orders_stat.diff.sales nofilter}%
        </div>
    </div>
{/if}
{if !empty($orders_stat.taxes) && $auth.user_type != 'V'}
    <div class="dashboard-card">
        <div class="dashboard-card-title">{__("taxes")}</div>
        <div class="dashboard-card-content">
            <h3>{include file="common/price.tpl" value=$orders_stat.taxes.subtotal}</h3>{include file="common/price.tpl" value=$orders_stat.taxes.prev_subtotal}, {if $orders_stat.taxes.subtotal > $orders_stat.taxes.prev_subtotal}+{/if}{$orders_stat.taxes.diff nofilter}%
        </div>
    </div>
{/if}
{if !empty($orders_stat.abandoned_cart_total)}
    <div class="dashboard-card">
        <div class="dashboard-card-title">{__("users_carts")}</div>
        <div class="dashboard-card-content">
            <h3>{$orders_stat.abandoned_cart_total|default:0}</h3>{$orders_stat.prev_abandoned_cart_total|default:0}, {if $orders_stat.abandoned_cart_total > $orders_stat.prev_abandoned_cart_total}+{/if}{$orders_stat.diff.abandoned_carts nofilter}%
        </div>
    </div>
{/if}
{/hook}
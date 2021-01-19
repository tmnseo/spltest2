{if $plan_usage && $auth.user_type != 'V'}
    <div class="dashboard-table dashboard-table-plan-usage">
        <h4>{__("vendor_plans.current_plan_usage")}</h4>
        <div class="table-wrapper">
            <table class="table" width="100%">
                <tr>
                    <td>
                        {__("vendor_plans.plan_name")}:
                    </td>
                    <td>
                        <a href="{"companies.update?company_id={$runtime.company_id}&selected_section=plan"|fn_url}">
                            <strong>{$plan_data.plan}</strong>
                        </a>
                    </td>
                </tr>
                {foreach from=$plan_usage item=item}
                <tr>
                    <td width="30%">
                        <strong>{$item.title}</strong><br />
                        {strip}
                            {if $item.is_price}
                                {include file="common/price.tpl" value=$item.current}/
                            {else}
                                {$item.current}&nbsp;/&nbsp;
                            {/if}
                            
                            {if !$item.limit} 
                                {__("vendor_plans.unlimited")}
                            {elseif $item.is_price}
                                {include file="common/price.tpl" value=$item.limit}
                            {else}
                                {$item.limit}
                            {/if}
                        {/strip}
                    </td>
                    <td width="70%" valign="middle">
                        <div class="progress {if $item.current == $item.limit}progress-info{elseif $item.current > $item.limit}progress-danger{/if}">
                            <div class="bar" style="width: {$item.percentage}%;"></div>
                        </div>
                    </td>
                </tr>
                {/foreach}
            </table>
        </div>
    </div>
{/if}
{capture name="mainbox"}
    {include file="common/pagination.tpl"}
    {if $log_data}
    <form >
    <div id="statuses_table">
        <input type="hidden" name="redirect_url" value="{$config.current_url}" />
        <table width="100%" class="table table-middle">
            <thead>
            <tr>
                <th width="10%">{__("cp_statuses_rules.order_id")}</th>
                <th width="15%">{__("cp_statuses_rules.update_time")}</th>
                <th width="5%">{__("cp_statuses_rules.order_status")}</th>
            </tr>
            </thead>

            {foreach from=$log_data item=log}
                <tbody>
                <tr>
                    <td>{$log.order_id}</td>
                    <td title="{$log.update_timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}"><input type="text" value="{$log.update_timestamp}" onchange="fn_cp_change_statuses_update_timestamp({$log.order_id}, this)"></td>
                    <td>{$log.description}</td>
                </tr>
                </tbody>
            {/foreach}
        </table>
    <!--statuses_table--></div>
    {else}
        <p class="no-items">{__("no_data")}</p>
    {/if}

    {include file="common/pagination.tpl"}
    {capture name="buttons"}
        <a class="cm-post btn" href="{"cp_statuses_rules.go"|fn_url}" >{__("cp_statuses_rules.go")}</a>
    {/capture}
{/capture}
{include file="common/mainbox.tpl" title=__("cp_statuses_rules.test_title") content=$smarty.capture.mainbox buttons=$smarty.capture.buttons}
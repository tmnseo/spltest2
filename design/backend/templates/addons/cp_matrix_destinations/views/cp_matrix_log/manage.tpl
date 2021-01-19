{capture name="mainbox"}
    {include file="common/pagination.tpl"}
    {if $logs}
        <table width="100%" class="table table-middle">
            <thead>
            <tr>
                <th width="10%">id</th>
                <th width="15%">{__("cp_matrix_logs_date")}</th>
                <th width="30%">{__("cp_matrix_logs_data")}</th>
                <th width="20%">{__("cp_matrix_logs_type")}</th>
                <th width="20%">{__("cp_matrix_logs_status")}</th>
            </tr>
            </thead>

            {foreach from=$logs item=log}
                <tbody>
                <tr>
                    <td>{$log.log_id}</td>
                    <td>{$log.time|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
                    <td>{$log.data}</td>
                    <td>{$log.type}</td>
                    <td>{$log.status}</td>
                </tr>
                </tbody>
            {/foreach}
        </table>
    {else}
        <p class="no-items">{__("no_data")}</p>
    {/if}

    {include file="common/pagination.tpl"}

{/capture}
{include file="common/mainbox.tpl" title=__("cp_matrix_log_system") content=$smarty.capture.mainbox}
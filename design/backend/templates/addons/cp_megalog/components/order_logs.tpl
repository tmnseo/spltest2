<div id="cp_ml_order_logs">
    {if $cp_ml_logs}
        <table width="100%" class="table table-middle">
        <thead>
        <tr>
            <th width="5%" class="center">{__("id")}</th>
            <th width="15%">{__("user")}</th>
            <th width="15%" class="left">{__("action")}</th>
            <th width="50%" class="left">{__("description")}</th>
            <th width="15%" class="center">{__("date")}</th>
        </tr>
        </thead>
        {foreach from=$cp_ml_logs item="log"}
            <tr>
                <td class="center">#{$log.log_id}</td>
                <td>
                    {if $log.user_id}
                        <a href="{"profiles.update&user_id=`$log.user_id`"|fn_url}" class="strong">{$log.firstname}&nbsp;{$log.lastname}</a>
                    {else}
                        {if $log.parce_request && $log.parce_request.label == "cp_ml_order_created"}
                            {__("cp_ml_guest")}
                        {else}
                            {__("cp_ml_system")}
                        {/if}
                    {/if}
                </td>
                <td class="left">{__($log.parce_request.label)}</td>
                <td class="left">{$log.parce_request.description}{if $log.parce_request.notice}<br /><strong>{__("comment")}: </strong>{$log.parce_request.notice nofilter}{/if}</td>
                <td class="center">
                    {$log.timestamp|date_format:"`$settings.Appearance.date_format`"},&nbsp;{$log.timestamp|date_format:"`$settings.Appearance.time_format`"}
                </td>
            </tr>
        {/foreach}
        </table>
    {else}
        <p class="no-items">{__("no_data")}</p>
    {/if}
    </table>
<!--cp_ml_order_logs--></div>
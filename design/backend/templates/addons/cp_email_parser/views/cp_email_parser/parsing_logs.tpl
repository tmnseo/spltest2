{capture name="mainbox"}
    {include file="common/pagination.tpl"}
    {if $logs}
        <table width="100%" class="table table-middle">
            <thead>
            <tr>
                <th width="10%">{__("cp_email_parser.log_id")}</th>
                <th width="15%">{__("cp_email_parser.log_date")}</th>
                <th width="5%">{__("cp_email_parser.log_time")}</th>
                <th width="30%">{__("cp_email_parser.log_message")}</th>
                <th width="15%">{__("cp_email_parser.log_status")}</th>
                <th width="20%">{__("cp_email_parser.log_operation")}</th>
                <th></th>
            </tr>
            </thead>

            {foreach from=$logs item=log}
                <tbody>
                <tr class="{if $log.status == "E"} cp-parser-log-error {/if}">
                    <td>{$log.log_id}</td>
                    <td>{$log.start_time|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
                    <td>{$log.time}</td>
                    <td>{$log.data}</td>
                    <td>{if $log.status == "E"}
                            {__("cp_email_parser.log_status_error")}
                        {elseif $log.status == "S"}
                            {__("cp_email_parser.log_status_success")}
                        {/if}
                    </td>
                    <td>{$log.type}</td>
                    <td>
                        {if $log.parent_log_id && $log.is_final}
                            <a class="cp-am-link cm-dialog-opener cm-dialog-auto-size cm-ajax" href="{"cp_email_parser.log_events&log_id=`$log.log_id`"|fn_url}" data-ca-dialog-title="{__("cp_email_parser.log_events")}" data-ca-target-id="cp_event_{$log.log_id}">{__("cp_email_parser.log_events")}</a>
                        {/if}
                    </td>
                </tr>
                </tbody>
            {/foreach}
        </table>
    {else}
        <p class="no-items">{__("no_data")}</p>
    {/if}

    {include file="common/pagination.tpl"}

{/capture}
{capture name="buttons"}
    {capture name="tools_list"}
        <li>{btn type="list" text=__("cp_email_parser.clean_logs") href="cp_email_parser.clean_logs" class="cm-confirm" method="POST"}</li>
        {if $settings.Logging.log_lifetime|intval}
            <li>{btn type="list" text=__("cp_email_parser.clean_old_logs", [$settings.Logging.log_lifetime|intval]) href="cp_email_parser.clean_old_logs" class="cm-confirm" method="POST"}</li>
        {/if}
    {/capture}
    {dropdown content=$smarty.capture.tools_list}
{/capture}
{include file="common/mainbox.tpl" title=__("cp_email_parser.logs_title") content=$smarty.capture.mainbox buttons=$smarty.capture.buttons}
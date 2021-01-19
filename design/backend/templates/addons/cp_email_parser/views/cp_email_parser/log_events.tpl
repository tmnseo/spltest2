<div id="cp_event_{$id}">
    {if $events}
        <table width="100%" class="table table-middle">
        {foreach from=$events item=event}
            <tr class="{if $event.status == "E"} cp-parser-log-error {else} cp-parser-success{/if}">
                <td>{$event.process}</td>
                <td>{$event.message}</td>
                <td>{$event.time|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
            </tr>
        {/foreach}
        </table>
    {else}
        <p class="no-items">{__("no_data")}</p>
    {/if}
<!--cp_event_{$id}--></div>
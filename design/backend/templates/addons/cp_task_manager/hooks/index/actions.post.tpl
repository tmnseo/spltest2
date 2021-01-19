    

{if $runtime.controller == 'tasks'}
<div id="server_time">
    <div class="pull-left">
        <label for="server_time_value" class="cp-actions-label f-middle">{__("cp_server_time")}:</label><span id="server_time_value" class="f-middle">{$server_time|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</span>
    </div>
<!--server_time--></div>
{/if}
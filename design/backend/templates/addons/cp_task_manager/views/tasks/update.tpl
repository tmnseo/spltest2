{if $task}
    {$id=$task.task_id}
    {$task_type=$task.type}
{else}
    {assign var="id" value=0}
    {if !$cp_aa_is_vendor}
        {$task_type=$smarty.const.TM_DB_BACKUP}
    {else}
        {$task_type=$smarty.const.TM_EXPORT}
    {/if}
{/if}

{** tasks section **}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" class="form-horizontal form-edit" enctype="multipart/form-data" name="tasks_form">
    <input type="hidden" class="cm-no-hide-input" name="task_id" value="{$id}" />

    <div class="control-group">
        <label for="elm_task_name" class="control-label {if $id}cm-required{/if}">{__("name")}:</label>
        <div class="controls">
            {if !$cp_aa_is_vendor}
                <input type="text" name="task_data[task]" id="elm_task_name" value="{if $task.task}{$task.task}{else}{$smarty.const.TM_DB_BACKUP|fn_cp_task_manager_type_to_string}{/if}" size="25" class="input-large" />
            {else}
                <input type="text" name="task_data[task]" id="elm_task_name" value="{if $task.task}{$task.task}{else}{$smarty.const.TM_EXPORT|fn_cp_task_manager_type_to_string}{/if}" size="25" class="input-large" />
            {/if}
        </div>
    </div>
    
    <div class="control-group">
        <label for="elm_task_type" class="control-label cm-required ">{__("type")}:</label>
        <div class="controls">
            <select name="task_data[type]" id="elm_task_type" onchange="fn_cp_task_manager_display_setting(this.value);">
                {if !$cp_aa_is_vendor}
                    <option {if $task.type == $smarty.const.TM_DB_BACKUP}selected="selected"{/if} value="{$smarty.const.TM_DB_BACKUP}">{$smarty.const.TM_DB_BACKUP|fn_cp_task_manager_type_to_string}</option>
                    <option {if $task.type == $smarty.const.TM_FILES_BACKUP}selected="selected"{/if} value="{$smarty.const.TM_FILES_BACKUP}">{$smarty.const.TM_FILES_BACKUP|fn_cp_task_manager_type_to_string}</option>
                {/if}
                <option {if $task.type == $smarty.const.TM_EXPORT}selected="selected"{/if} value="{$smarty.const.TM_EXPORT}">{$smarty.const.TM_EXPORT|fn_cp_task_manager_type_to_string}</option>
                <option {if $task.type == $smarty.const.TM_IMPORT}selected="selected"{/if} value="{$smarty.const.TM_IMPORT}">{$smarty.const.TM_IMPORT|fn_cp_task_manager_type_to_string}</option>
                {if !$cp_aa_is_vendor}
                    <option {if $task.type == $smarty.const.TM_CLEAR_CACHE}selected="selected"{/if} value="{$smarty.const.TM_CLEAR_CACHE}">{$smarty.const.TM_CLEAR_CACHE|fn_cp_task_manager_type_to_string}</option>
                    <option {if $task.type == $smarty.const.TM_CLEAR_TEMPLATES}selected="selected"{/if} value="{$smarty.const.TM_CLEAR_TEMPLATES}">{$smarty.const.TM_CLEAR_TEMPLATES|fn_cp_task_manager_type_to_string}</option>
                    <option {if $task.type == $smarty.const.TM_THUMBNAILS_REGENERATION}selected="selected"{/if} value="{$smarty.const.TM_THUMBNAILS_REGENERATION}">{$smarty.const.TM_THUMBNAILS_REGENERATION|fn_cp_task_manager_type_to_string}</option>
                    {if $addons.google_sitemap.status == 'A'}
                        <option {if $task.type == $smarty.const.TM_REGENERATE_SITEMAP}selected="selected"{/if} value="{$smarty.const.TM_REGENERATE_SITEMAP}">{$smarty.const.TM_REGENERATE_SITEMAP|fn_cp_task_manager_type_to_string}</option>
                    {/if}
                    <option {if $task.type == $smarty.const.TM_CLEAR_LOGS}selected="selected"{/if} value="{$smarty.const.TM_CLEAR_LOGS}">{$smarty.const.TM_CLEAR_LOGS|fn_cp_task_manager_type_to_string}</option>
                    <option {if $task.type == $smarty.const.TM_CUSTOM_SCRIPT}selected="selected"{/if} value="{$smarty.const.TM_CUSTOM_SCRIPT}">{$smarty.const.TM_CUSTOM_SCRIPT|fn_cp_task_manager_type_to_string}</option>
                    <option {if $task.type == $smarty.const.TM_DROPBOX}selected="selected"{/if} value="{$smarty.const.TM_DROPBOX}">{$smarty.const.TM_DROPBOX|fn_cp_task_manager_type_to_string}</option>
                    <option {if $task.type == $smarty.const.TM_FTP}selected="selected"{/if} value="{$smarty.const.TM_FTP}">{$smarty.const.TM_FTP|fn_cp_task_manager_type_to_string}</option>
                    <option {if $task.type == $smarty.const.TM_OPTIMIZE_DATABASE}selected="selected"{/if} value="{$smarty.const.TM_OPTIMIZE_DATABASE}">{$smarty.const.TM_OPTIMIZE_DATABASE|fn_cp_task_manager_type_to_string}</option>
                    <option {if $task.type == $smarty.const.TM_DATA_FEED}selected="selected"{/if} value="{$smarty.const.TM_DATA_FEED}">{$smarty.const.TM_DATA_FEED|fn_cp_task_manager_type_to_string}</option>
                {/if}
            </select>
        </div>
    </div>
    
    <div class="control-group">
        <label class="control-label ">{__("cp_cron_settings")}{include file="common/tooltip.tpl" tooltip=__("cp_crontab_tooltip")}:</label>
        <div class="controls">
            <div class="cp-control-group">
                <label class="cp-control-label cm-value-factory-minutes-lbl" for="elm_task_factory_minutes">{__("minutes")}:</label>
                <div class="cp-controls">
                    <input type="text" name="task_data[factory][minutes]" id="elm_task_factory_minutes" value="{$task.factory.minutes|default:'*'}" size="25" class="input-mini cm-value-factory-minutes" />
                </div>
            </div>
            <div class="cp-control-group">
                <label class="cp-control-label cm-value-factory-hours-lbl" for="elm_task_factory_hours">{__("cp_hours")}:</label>
                <div class="cp-controls">
                    <input type="text" name="task_data[factory][hours]" id="elm_task_factory_hours" value="{$task.factory.hours|default:'*'}" size="25" class="input-mini cm-value-factory-hours" />
                </div>
            </div>
            <div class="cp-control-group">
                <label class="cp-control-label cm-value-factory-days-lbl" for="elm_task_factory_days">{__("days")}:</label>
                <div class="cp-controls">
                    <input type="text" name="task_data[factory][days]" id="elm_task_factory_days" value="{$task.factory.days|default:'*'}" size="25" class="input-mini cm-value-factory-days" />
                </div>
            </div>
            <div class="cp-control-group">
                <label class="cp-control-label cm-value-factory-months-lbl" for="elm_task_factory_months">{__("months")}:</label>
                <div class="cp-controls">
                    <input type="text" name="task_data[factory][months]" id="elm_task_factory_months" value="{$task.factory.months|default:'*'}" size="25" class="input-mini cm-value-factory-months" />
                </div>
            </div>
            <div class="cp-control-group">
                <label class="cp-control-label cm-value-factory-dws-lbl" for="elm_task_factory_dws">{__("cp_dws")}:</label>
                <div class="cp-controls">
                    <input type="text" name="task_data[factory][dws]" id="elm_task_factory_dws" value="{$task.factory.dws|default:'*'}" size="25" class="input-mini cm-value-factory-dws" />
                </div>
            </div>
        </div>
    </div>
    
    <div class="control-group">
        <label class="control-label" for="elm_use_avail_period">{__("use_avail_period")}:</label>
        <div class="controls">
            <input type="checkbox" name="avail_period" id="elm_use_avail_period" {if $task.from_date || $task.to_date}checked="checked"{/if} value="Y" onclick="fn_activate_calendar(this);"/>
        </div>
    </div>

    {capture name="calendar_disable"}{if !$task.from_date && !$task.to_date}disabled="disabled"{/if}{/capture}

    <div class="control-group">
        <label class="control-label" for="elm_date_holder_from">{__("avail_from")}:</label>
        <div class="controls">
            <input type="hidden" name="task_data[from_date]" value="0" />
            {include file="common/calendar.tpl" date_id="elm_date_holder_from" date_name="task_data[from_date]" date_val=$task.from_date|default:$smarty.const.TIME start_year=$settings.Company.company_start_year extra=$smarty.capture.calendar_disable}
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="elm_date_holder_to">{__("avail_till")}:</label>
        <div class="controls">
            <input type="hidden" name="task_data[to_date]" value="0" />
            {include file="common/calendar.tpl" date_id="elm_date_holder_to" date_name="task_data[to_date]" date_val=$task.to_date|default:$smarty.const.TIME start_year=$settings.Company.company_start_year extra=$smarty.capture.calendar_disable}
        </div>
    </div>

    <script language="javascript">
    function fn_activate_calendar(el)
    {
        var $ = Tygh.$;
        var jelm = $(el);
        var checked = jelm.prop('checked');

        $('#elm_date_holder_from,#elm_date_holder_to').prop('disabled', !checked);
    }

    fn_activate_calendar(Tygh.$('#elm_use_avail_period'));
    </script>
    
    <div class="control-group">
        <label class="control-label" for="elm_task_timestamp_{$id}">{__("creation_date")}:</label>
        <div class="controls">
            {include file="common/calendar.tpl" date_id="elm_task_timestamp_`$id`" date_name="task_data[timestamp]" date_val=$task.timestamp|default:$smarty.const.TIME start_year=$settings.Company.company_start_year}
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="elm_task_notify_by_email">{__("cp_notify_by_email")}:</label>
        <div class="controls">
            <input type="hidden" name="task_data[notify_by_email]" value="N" />
            <input type="checkbox" id="elm_task_notify_by_email" name="task_data[notify_by_email]" value="Y" {if $task.notify_by_email == 'Y'}checked="checked"{/if} onclick="fn_cp_task_manager_activate_email(this);"/>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="elm_task_notify_email">{__("cp_notify_email")}:</label>
        <div class="controls">
            <input type="text" name="task_data[notify_email]" id="elm_task_notify_email" value="{$task.notify_email}" size="25" class="input-medium" />
        </div>
    </div>
    
    <script language="javascript">
    function fn_cp_task_manager_activate_email(el)
    {
        var $ = Tygh.$;
        var jelm = $(el);
        var checked = jelm.prop('checked');

        $('#elm_task_notify_email').prop('disabled', !checked);
    }

    fn_cp_task_manager_activate_email(Tygh.$('#elm_task_notify_by_email'));
    </script>
    
    {if $id}
    <div class="control-group">
        <label class="control-label" for="elm_next_run">{__("cp_next_run")}:</label>
        <div class="controls">
            <span>{$task.next_run|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</span>
        </div>
    </div>
    {/if}

    {include file="common/select_status.tpl" input_name="task_data[status]" id="elm_task_status" obj_id=$id obj=$task hidden=false}
    
    <div class="{if $task_type != $smarty.const.TM_DB_BACKUP}hidden{/if}" id="task_{$smarty.const.TM_DB_BACKUP}_settings">
        {include file="addons/cp_task_manager/views/tasks/components/db_backup_settings.tpl"}
    </div>
    
    <div class="{if $task_type != $smarty.const.TM_FILES_BACKUP}hidden{/if}" id="task_{$smarty.const.TM_FILES_BACKUP}_settings">
        {include file="addons/cp_task_manager/views/tasks/components/files_backup_settings.tpl"}
    </div>

    <div class="{if $task_type != $smarty.const.TM_CUSTOM_SCRIPT}hidden{/if}" id="task_{$smarty.const.TM_CUSTOM_SCRIPT}_settings">
        {include file="addons/cp_task_manager/views/tasks/components/custom_script_settings.tpl"}
    </div>
    
    <div class="{if $task_type != $smarty.const.TM_EXPORT}hidden{/if}" id="task_{$smarty.const.TM_EXPORT}_settings">
        {include file="addons/cp_task_manager/views/tasks/components/export_settings.tpl"}
    </div>
    
    <div class="{if $task_type != $smarty.const.TM_IMPORT}hidden{/if}" id="task_{$smarty.const.TM_IMPORT}_settings">
        {include file="addons/cp_task_manager/views/tasks/components/import_settings.tpl"}
    </div>
    
    <div class="{if $task_type != $smarty.const.TM_DROPBOX}hidden{/if}" id="task_{$smarty.const.TM_DROPBOX}_settings">
        {include file="addons/cp_task_manager/views/tasks/components/dropbox_settings.tpl"}
    </div>
   
    <div class="{if $task_type != $smarty.const.TM_FTP}hidden{/if}" id="task_{$smarty.const.TM_FTP}_settings">
        {include file="addons/cp_task_manager/views/tasks/components/ftp_settings.tpl"}
    </div>
{capture name="buttons"}
    {if !$id}
        {include file="buttons/save_cancel.tpl" but_role="submit-link" but_target_form="tasks_form" but_name="dispatch[tasks.update]"}
    {else}
        {include file="buttons/save_cancel.tpl" but_name="dispatch[tasks.update]" but_role="submit-link" but_target_form="tasks_form" save=$id}
    {/if}
{/capture}
    
</form>

<script type='text/javascript'>

function fn_cp_task_manager_display_setting(value) 
{
    $("div[id*='_settings']").hide();
    $('#task_' + value + '_settings').show();
    if (!{$id}) {
        $('#elm_task_name').val($('#elm_task_type option:selected').text());
    }
}

</script>

{/capture}

{if !$id}
    {assign var="title" value=__("cp_new_task")}
{else}
    {assign var="title" value="{__("cp_editing_task")}: `$task.task`"}
{/if}
{include file="common/mainbox.tpl" title=$title content=$smarty.capture.mainbox buttons=$smarty.capture.buttons select_languages=true}

{** task section **}

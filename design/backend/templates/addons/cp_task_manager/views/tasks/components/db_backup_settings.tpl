{include file="common/subheader.tpl" title=__("cp_task_settings") target="#acc_task_{$smarty.const.TM_DB_BACKUP}"}
<div id="acc_task_{$smarty.const.TM_DB_BACKUP}" class="collapse in">
    <div class="control-group">
        <label for="dbdump_tables" class="control-label">{__("select_tables")}:</label>

        <div class="controls">
            <select name="task_data[task_settings][{$smarty.const.TM_DB_BACKUP}][dbdump_tables][]" id="dbdump_tables" multiple="multiple" size="10">
                {foreach from=$all_tables item=tbl}
                    <option value="{$tbl}"{if $tbl|in_array:$task.task_settings.dbdump_tables && ($config.table_prefix == '' || $tbl|strpos:$config.table_prefix === 0)} selected="selected"{/if}>{$tbl}</option>
                {/foreach}
            </select>

            <p><a onclick="Tygh.$('#dbdump_tables').selectOptions(true); return false;"
                    class="underlined">{__("select_all")}</a> / <a
                        onclick="Tygh.$('#dbdump_tables').selectOptions(false); return false;"
                        class="underlined">{__("unselect_all")}</a></p>

            <div class="muted">{__("multiple_selectbox_notice")}</div>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label">{__("backup_options")}:</label>

        <div class="controls">
            <label for="dbdump_schema" class="checkbox">
                <input type="hidden" name="task_data[task_settings][{$smarty.const.TM_DB_BACKUP}][dbdump_schema]" value="N" />
                <input type="checkbox" name="task_data[task_settings][{$smarty.const.TM_DB_BACKUP}][dbdump_schema]" id="dbdump_schema" value="Y" {if $task.task_settings.dbdump_schema == 'Y' || !$task.task_settings.dbdump_schema} checked="checked" {/if}>{__("backup_schema")}</label>
            <label for="dbdump_data" class="checkbox">
                <input type="hidden" name="task_data[task_settings][{$smarty.const.TM_DB_BACKUP}][dbdump_data]" value="N" />
                <input type="checkbox" name="task_data[task_settings][{$smarty.const.TM_DB_BACKUP}][dbdump_data]" id="dbdump_data" value="Y" {if $task.task_settings.dbdump_data == 'Y' || !$task.task_settings.dbdump_data} checked="checked" {/if}>{__("backup_data")}</label>
        </div>
    </div>
    
    <div class="control-group">
        <label for="dbdump_filename_prefix_{$smarty.const.TM_DB_BACKUP}" class="control-label">{__("cp_backup_filename_prefix")}:</label>

        <div class="controls">
            <div class="input-append">
                {assign var="default_name" value="dMY_His"|date:$smarty.now}
                 <input type="text" name="task_data[task_settings][{$smarty.const.TM_DB_BACKUP}][dbdump_filename_prefix]" id="dbdump_filename_prefix_{$smarty.const.TM_DB_BACKUP}" size="30" value="{$task.task_settings.dbdump_filename_prefix|default:'backup_'}" class="input-text">
                <span class="add-on">[dMY_His].sql.zip</span>
            </div>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="elm_task_{$smarty.const.TM_DB_BACKUP}_setting">{__("cp_number_of_backups")}:</label>
        <div class="controls">
            <input type="text" name="task_data[task_settings][{$smarty.const.TM_DB_BACKUP}][number_of_db_backups]" id="elm_task_{$smarty.const.TM_DB_BACKUP}_setting" value="{$task.task_settings.number_of_db_backups|default:5}" size="25" class="input-mini cm-value-integer" />
        </div>
    </div>
</div>

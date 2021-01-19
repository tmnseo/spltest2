{include file="common/subheader.tpl" title=__("cp_task_settings") target="#acc_task_{$smarty.const.TM_FILES_BACKUP}"}
<div id="acc_task_{$smarty.const.TM_FILES_BACKUP}" class="collapse in">
    <div class="control-group">
        <label for="extra_folders" class="control-label">{__("cp_extra_folders")}:</label>

        <div class="controls">
            <select name="task_data[task_settings][{$smarty.const.TM_FILES_BACKUP}][extra_folders][]" id="extra_folders" multiple="multiple" size="5">
                <option value="images"{if "images"|in_array:$task.task_settings.extra_folders} selected="selected"{/if}>images</option>
                <option value="var/files"{if "var/files"|in_array:$task.task_settings.extra_folders} selected="selected"{/if}>var/files</option>
                <option value="var/attachments"{if "var/attachments"|in_array:$task.task_settings.extra_folders} selected="selected"{/if}>var/attachments</option>
                <option value="var/langs"{if "var/langs"|in_array:$task.task_settings.extra_folders} selected="selected"{/if}>var/langs</option>
            </select>

            <p><a onclick="Tygh.$('#extra_folders').selectOptions(true); return false;"
                    class="underlined">{__("select_all")}</a> / <a
                        onclick="Tygh.$('#extra_folders').selectOptions(false); return false;"
                        class="underlined">{__("unselect_all")}</a></p>
        </div>
    </div>
  
    <div class="control-group">
        <label for="dbdump_filename_prefix_{$smarty.const.TM_FILES_BACKUP}" class="control-label">{__("cp_backup_filename_prefix")}:</label>

        <div class="controls">
            <div class="input-append">
                <input type="text" name="task_data[task_settings][{$smarty.const.TM_FILES_BACKUP}][dbdump_filename_prefix]" id="dbdump_filename_prefix_{$smarty.const.TM_FILES_BACKUP}" size="30" value="{$task.task_settings.dbdump_filename_prefix|default:'backup_'}" class="input-text">
                <span class="add-on">[dMY_His].zip</span>
            </div>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="elm_task_{$smarty.const.TM_FILES_BACKUP}_setting">{__("cp_number_of_backups")}:</label>
        <div class="controls">
            <input type="text" name="task_data[task_settings][{$smarty.const.TM_FILES_BACKUP}][number_of_file_backups]" id="elm_task_{$smarty.const.TM_FILES_BACKUP}_setting" value="{$task.task_settings.number_of_file_backups|default:5}" size="25" class="input-mini cm-value-integer" />
        </div>
    </div>
</div>

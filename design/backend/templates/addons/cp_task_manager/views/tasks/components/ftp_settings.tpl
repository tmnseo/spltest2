{include file="common/subheader.tpl" title=__("cp_task_settings") target="#acc_task_{$smarty.const.TM_FTP}"}
<div id="acc_task_{$smarty.const.TM_FTP}" class="collapse in">
    <div class="control-group">
        <label class="control-label" for="elm_task_{$smarty.const.TM_FTP}_host">{__("host")}:</label>
        <div class="controls">
            <input type="text" name="task_data[task_settings][{$smarty.const.TM_FTP}][host]" id="elm_task_{$smarty.const.TM_FTP}_host" value="{$task.task_settings.host}" size="25" class="input-medium" />
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="elm_task_{$smarty.const.TM_FTP}_username">{__("username")}:</label>
        <div class="controls">
            <input type="text" name="task_data[task_settings][{$smarty.const.TM_FTP}][username]" id="elm_task_{$smarty.const.TM_FTP}_username" value="{$task.task_settings.username}" size="25" class="input-medium" />
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="elm_task_{$smarty.const.TM_FTP}_password">{__("password")}:</label>
        <div class="controls">
            <input type="password" name="task_data[task_settings][{$smarty.const.TM_FTP}][password]" id="elm_task_{$smarty.const.TM_FTP}_password" value="{$task.task_settings.password}" size="25" class="input-medium" />
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="elm_task_{$smarty.const.TM_FTP}_path">{__("folder")}:</label>
        <div class="controls">
            <input type="text" name="task_data[task_settings][{$smarty.const.TM_FTP}][path]" id="elm_task_{$smarty.const.TM_FTP}_path" value="{$task.task_settings.path}" size="25" class="input-medium" />
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="elm_task_{$smarty.const.TM_FTP}_port">{__("cp_port")}:</label>
        <div class="controls">
            <input type="text" name="task_data[task_settings][{$smarty.const.TM_FTP}][port]" id="elm_task_{$smarty.const.TM_FTP}_port" value="{$task.task_settings.port|default:21}" size="25" class="input-medium" />
        </div>
    </div>
    {if $id}
    <div class="control-group">
        <label class="control-label">{__("select_file")}:</label>
        <input type="hidden" name="task_data[task_settings][{$smarty.const.TM_FTP}][ftp_file]" value="{$task.task_settings.ftp_file}" />
        <div class="controls">{include file="common/fileuploader.tpl" var_name="csv_file[0]" images=$task.task_settings.ftp_uploaded_file}</div>
    </div>
    <div class="control-group">
        <label class="control-label" for="elm_task_{$smarty.const.TM_FTP}_remove_file">{__("cp_remove_after_upload")}:</label>
        <div class="controls">
            <input type="hidden" name="task_data[task_settings][{$smarty.const.TM_FTP}][remove_file]" value="N"/>
            <input type="checkbox" name="task_data[task_settings][{$smarty.const.TM_FTP}][remove_file]" id="elm_task_{$smarty.const.TM_FTP}_remove_file" value="Y" {if $task.task_settings.remove_file == "Y"}checked="checked" {/if}/>
        </div>
    </div>
    {/if}
</div>
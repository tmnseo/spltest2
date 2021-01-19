{include file="common/subheader.tpl" title=__("cp_task_settings") target="#acc_task_{$smarty.const.TM_CUSTOM_SCRIPT}"}
<div id="acc_task_{$smarty.const.TM_CUSTOM_SCRIPT}" class="collapse in">
    <div class="control-group">
        <label class="control-label" for="elm_task_{$smarty.const.TM_CUSTOM_SCRIPT}_setting">{__("cp_custom_script")}:</label>
        <div class="controls">
            <input type="text" name="task_data[task_settings][{$smarty.const.TM_CUSTOM_SCRIPT}][custom_script]" id="elm_task_{$smarty.const.TM_CUSTOM_SCRIPT}_setting" value="{$task.task_settings.custom_script}" size="25" class="input-large" />
        </div>
    </div>
</div>
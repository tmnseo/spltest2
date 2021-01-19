{include file="common/subheader.tpl" title=__("cp_task_settings") target="#acc_task_{$smarty.const.TM_DROPBOX}"}
<div id="acc_task_{$smarty.const.TM_DROPBOX}" class="collapse in">
    <p class="muted">{__("cp_dropbox_instruction") nofilter}</p>
    <div class="control-group">
        <label class="control-label" for="elm_task_{$smarty.const.TM_DROPBOX}_key">{__("key")}{include file="common/tooltip.tpl" tooltip=__("cp_dropbox_key_tooltip")}:</label>
        <div class="controls">
            <input type="text" name="task_data[task_settings][{$smarty.const.TM_DROPBOX}][key]" id="elm_task_{$smarty.const.TM_DROPBOX}_key" value="{$task.task_settings.key}" size="25" class="input-medium" />
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="elm_task_{$smarty.const.TM_DROPBOX}_secret">{__("cp_secret")}{include file="common/tooltip.tpl" tooltip=__("cp_dropbox_secret_tooltip")}:</label>
        <div class="controls">
            <input type="text" name="task_data[task_settings][{$smarty.const.TM_DROPBOX}][secret]" id="elm_task_{$smarty.const.TM_DROPBOX}_secret" value="{$task.task_settings.secret}" size="25" class="input-medium" />
        </div>
    </div>
    <div class="control-group">
        <label class="control-label " for="elm_task_{$smarty.const.TM_DROPBOX}_use_generated_token">{__("cp_use_generated_token")}{include file="common/tooltip.tpl" tooltip=__("cp_dropbox_use_generated_token_tooltip")}:</label>
        <div class="controls">
            <input type="hidden" name="task_data[task_settings][{$smarty.const.TM_DROPBOX}][use_generated_token]" value="N" />
            <input type="checkbox" name="task_data[task_settings][{$smarty.const.TM_DROPBOX}][use_generated_token]" id="elm_task_{$smarty.const.TM_DROPBOX}_use_generated_token" value="Y" {if $task.task_settings.use_generated_token == 'Y'}checked="checked"{/if}/>
        </div>
    </div>
    <div class="control-group" id="token_section">
        <label class="control-label " for="elm_task_{$smarty.const.TM_DROPBOX}_token">{__("cp_token")}{include file="common/tooltip.tpl" tooltip=__("cp_dropbox_token_tooltip")}:</label>
        <div class="controls">
            <input type="text" name="task_data[task_settings][{$smarty.const.TM_DROPBOX}][token]" id="elm_task_{$smarty.const.TM_DROPBOX}_token" value="{$task.task_settings.token}" size="25" class="input-medium" />
            {if $id && $task.task_settings.use_generated_token == 'N'}
                {include file="buttons/button.tpl" but_text=__("cp_generate_token") but_role="action" but_onclick="fn_cp_task_manager_get_auth_url()"}
            {/if}
            
            {if $auth_url}
                <p class="muted">{__("cp_get_the_token", ["[auth_url]" => "`$auth_url`"])}</p>
            {/if}
        </div>
    <!--token_section--></div>

    {if $id}
        
    
    <input type="hidden" name="task_data[task_settings][{$smarty.const.TM_DROPBOX}][access_token]" id="elm_task_access_token" value="{$task.task_settings.access_token}" />
    <input type="hidden" name="task_data[task_settings][{$smarty.const.TM_DROPBOX}][dropbox_user_id]" id="elm_task_{$smarty.const.TM_DROPBOX}_dropbox_user_id" value="{$task.task_settings.dropbox_user_id}" />
    <div class="control-group">
        <label class="control-label" for="elm_task_{$smarty.const.TM_DROPBOX}_remove_file">{__("cp_remove_after_upload")}:</label>
        <div class="controls">
            <input type="hidden" name="task_data[task_settings][{$smarty.const.TM_DROPBOX}][remove_file]" value="N"/>
            <input type="checkbox" name="task_data[task_settings][{$smarty.const.TM_DROPBOX}][remove_file]" id="elm_task_{$smarty.const.TM_DROPBOX}_remove_file" value="Y" {if $task.task_settings.remove_file == "Y"}checked="checked" {/if}/>
        </div>
    </div>
    
    {/if}
    <div class="control-group">
        <label class="control-label" for="elm_task_{$smarty.const.TM_DROPBOX}_folder">{__("folder")}{include file="common/tooltip.tpl" tooltip=__("cp_dropbox_folder_tooltip")}:</label>
        <div class="controls">
            <input type="text" name="task_data[task_settings][{$smarty.const.TM_DROPBOX}][folder]" id="elm_task_{$smarty.const.TM_DROPBOX}_folder" value="{$task.task_settings.folder|default:"`$config.dir.backups`"}" size="25" class="input-large" />
        </div>
    </div>

<script type="text/javascript">
function fn_cp_task_manager_get_auth_url()
{
    if ({$id}) {
        var key = $('#elm_task_{$smarty.const.TM_DROPBOX}_key').val();
        var secret = $('#elm_task_{$smarty.const.TM_DROPBOX}_secret').val();
        $('#elm_task_{$smarty.const.TM_DROPBOX}_token').val('');
        $('#elm_task_{$smarty.const.TM_DROPBOX}_access_token').val('');
        var url = fn_url('tasks.get_auth_token?key=' + key + '&secret=' + secret + '&task_id=' + {$id});
        $.ceAjax('request', url, {
            result_ids: 'token_section',
            force_exec: true,
            method: 'get'
        });
    }
}
</script>
</div>
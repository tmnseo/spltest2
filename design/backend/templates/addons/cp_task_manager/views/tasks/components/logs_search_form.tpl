<div class="sidebar-row">
    <h6>{__("search")}</h6>
    <form action="{""|fn_url}" name="logs_form" method="get">
    <input type="hidden" name="object" value="{$smarty.request.object}">

    {capture name="simple_search"}
    {include file="common/period_selector.tpl" period=$search.period extra="" display="form" button="false"}
    
    <div class="sidebar-field">
        <label for="elm_task_type">{__("type")}:</label>
        <select name="type" id="elm_task_type" onchange="">
            <option {if empty($search.type)}selected="selected"{/if} value="0">{__("all")}</option>
            <option {if $search.type == $smarty.const.TM_DB_BACKUP}selected="selected"{/if} value="{$smarty.const.TM_DB_BACKUP}">{$smarty.const.TM_DB_BACKUP|fn_cp_task_manager_type_to_string}</option>
            <option {if $search.type == $smarty.const.TM_FILES_BACKUP}selected="selected"{/if} value="{$smarty.const.TM_FILES_BACKUP}">{$smarty.const.TM_FILES_BACKUP|fn_cp_task_manager_type_to_string}</option>
            <option {if $search.type == $smarty.const.TM_EXPORT}selected="selected"{/if} value="{$smarty.const.TM_EXPORT}">{$smarty.const.TM_EXPORT|fn_cp_task_manager_type_to_string}</option>
            <option {if $search.type == $smarty.const.TM_IMPORT}selected="selected"{/if} value="{$smarty.const.TM_IMPORT}">{$smarty.const.TM_IMPORT|fn_cp_task_manager_type_to_string}</option>
            <option {if $search.type == $smarty.const.TM_CLEAR_CACHE}selected="selected"{/if} value="{$smarty.const.TM_CLEAR_CACHE}">{$smarty.const.TM_CLEAR_CACHE|fn_cp_task_manager_type_to_string}</option>
            <option {if $search.type == $smarty.const.TM_CLEAR_TEMPLATES}selected="selected"{/if} value="{$smarty.const.TM_CLEAR_TEMPLATES}">{$smarty.const.TM_CLEAR_TEMPLATES|fn_cp_task_manager_type_to_string}</option>
            <option {if $search.type == $smarty.const.TM_THUMBNAILS_REGENERATION}selected="selected"{/if} value="{$smarty.const.TM_THUMBNAILS_REGENERATION}">{$smarty.const.TM_THUMBNAILS_REGENERATION|fn_cp_task_manager_type_to_string}</option>
            {if $addons.google_sitemap.status == 'A'}
                <option {if $search.type == $smarty.const.TM_REGENERATE_SITEMAP}selected="selected"{/if} value="{$smarty.const.TM_REGENERATE_SITEMAP}">{$smarty.const.TM_REGENERATE_SITEMAP|fn_cp_task_manager_type_to_string}</option>
            {/if}
            <option {if $search.type == $smarty.const.TM_CLEAR_LOGS}selected="selected"{/if} value="{$smarty.const.TM_CLEAR_LOGS}">{$smarty.const.TM_CLEAR_LOGS|fn_cp_task_manager_type_to_string}</option>
            <option {if $search.type == $smarty.const.TM_CUSTOM_SCRIPT}selected="selected"{/if} value="{$smarty.const.TM_CUSTOM_SCRIPT}">{$smarty.const.TM_CUSTOM_SCRIPT|fn_cp_task_manager_type_to_string}</option>
            <option {if $search.type == $smarty.const.TM_DROPBOX}selected="selected"{/if} value="{$smarty.const.TM_DROPBOX}">{$smarty.const.TM_DROPBOX|fn_cp_task_manager_type_to_string}</option>
            <option {if $search.type == $smarty.const.TM_FTP}selected="selected"{/if} value="{$smarty.const.TM_FTP}">{$smarty.const.TM_FTP|fn_cp_task_manager_type_to_string}</option>
        </select>
    </div>
    
    {/capture}
    
    {capture name="advanced_search"}
    {/capture}
    
    {include file="common/advanced_search.tpl" advanced_search=$smarty.capture.advanced_search simple_search=$smarty.capture.simple_search dispatch="tasks.view_logs" view_type="logs"}
    
</form>
</div>
{include file="common/subheader.tpl" title=__("cp_task_settings") target="#acc_task_{$smarty.const.TM_IMPORT}"}
<div id="acc_task_{$smarty.const.TM_IMPORT}" class="collapse in">
    <div class="control-group">
        <label class="control-label" for="elm_task_{$smarty.const.TM_IMPORT}_pattern">{__("import")}:</label>
        <div class="controls">
            <select name="task_data[task_settings][{$smarty.const.TM_IMPORT}][pattern_id]" id="elm_task_{$smarty.const.TM_IMPORT}_pattern" onchange="fn_cp_task_manager_display_import_setting(this.value, 0);">
            {foreach from=$import_patterns item="pattern"}
                <option {if $task.task_settings.pattern_id == $pattern.pattern_id}selected="selected"{/if} value="{$pattern.pattern_id}">{$pattern.name}</option>
            {/foreach}
            </select>
            {if !$id}
                <p class="muted">{__("cp_save_to_see_details")}</p>
            {/if}
        </div>
    </div>
    
    
    <div id="import_section">
        {if $selected_pat_id == "adv_products"}
            {if $adv_import_presets}
                <div class="control-group">
                    <label class="control-label">{__("cp_aa_preset_name")}:</label>
                    <div class="controls">
                        <select name="task_data[task_settings][{$smarty.const.TM_IMPORT}][adv_preset_id]">
                            {foreach from=$adv_import_presets item="preset"}
                                <option value="{$preset.preset_id}" {if $selected_preset_id == $preset.preset_id}selected="selected"{/if}>{$preset.preset}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            {else}
                <p class="no-items">{__("no_data")}</p>
            {/if}
        {else}
            <div class="{if !$task}hidden{/if}">
            
            {include file="common/subheader.tpl" title=$import_pattern.name notes=$smarty.capture.local_notes notes_id=$p_id target="#import_fields_`$id`"}
            <div id="import_fields_{$id}" class="in collapse">
                <p class="p-notice">{__("text_exim_import_notice")}</p>
                {split data=$import_pattern.export_fields size=5 assign="splitted_fields" simple=true size_is_horizontal=true}
                <table class="table table-striped table-exim">
                    <tr>
                    {foreach from=$splitted_fields item="fields"}
                        <td>
                            <ul class="unstyled">
                            {foreach from=$fields key="field" item="f"}
                                <li>{if $f.required}<strong>{/if}{$field}{if $f.required}</strong>{/if}</li>
                            {/foreach}
                            </ul>
                        </td>
                    {/foreach}
                    </tr>
                </table>
            </div>
            
            {include file="common/subheader.tpl" title=__("import_options")}
            
            {if $import_pattern.options}
            
                {foreach from=$import_pattern.options key="k" item="o"}
                    {if !$o.export_only && $o.type != "languages"}
                        <div class="control-group">
                            <label for="{$p_id}_{$k}_{$id}" class="control-label">
                                {__($o.title)}{if $o.description}{include file="common/tooltip.tpl" tooltip=__($o.description)}{/if}:
                            </label>
                            <div class="controls">
                                {if $o.type == "checkbox"}
                                    <input type="hidden" name="task_data[task_settings][{$smarty.const.TM_IMPORT}][import_options][{$k}]" value="N" />
                                    <input id="{$p_id}_{$k}_{$id}" class="checkbox" type="checkbox" name="task_data[task_settings][{$smarty.const.TM_IMPORT}][import_options][{$k}]" value="Y" {if $task.task_settings.import_options.$k == "Y"}checked="checked"{elseif $o.default_value == "Y"}checked="checked"{/if} />
                                {elseif $o.type == "input"}
                                    <input id="{$p_id}_{$k}_{$id}" class="input-large" type="text" name="task_data[task_settings][{$smarty.const.TM_IMPORT}][import_options][{$k}]" value="{$task.task_settings.import_options.$k|default:$o.default_value}" />
                                {elseif $o.type == "select"}
                                    <select id="{$p_id}_{$k}_{$id}" name="task_data[task_settings][{$smarty.const.TM_IMPORT}][import_options][{$k}]">
                                    {if $o.variants_function}
                                        {foreach from=$o.variants_function|call_user_func key=vk item=vi}
                                        <option value="{$vk}" {if $task.task_settings.import_options.$k == $vk}checked="checked"{elseif $vk == $o.default_value}checked="checked"{/if}>{$vi}</option>
                                        {/foreach}
                                    {else}
                                        {foreach from=$o.variants key=vk item=vi}
                                        <option value="{$vk}" {if $task.task_settings.import_options.$k == $vk}checked="checked"{elseif $vk == $o.default_value}checked="checked"{/if}>{__($vi)}</option>
                                        {/foreach}
                                    {/if}
                                    </select>
                                {/if}

                                {if $o.notes}
                                    <p class="muted">{$o.notes nofilter}</p>
                                {/if}
                            </div>
                        </div>
                    {/if}
                {/foreach}
            {/if}
            
            {assign var="override_options" value=$import_pattern.override_options}
            {if $override_options.delimiter}
                <input type="hidden" name="task_data[task_settings][{$smarty.const.TM_IMPORT}][import_options][delimiter]" value="{$task.task_settings.import_options.delimiter|default:$override_options.delimiter}" />
            {else}
            <div class="control-group">
                <label class="control-label">{__("csv_delimiter")}:</label>
                <div class="controls">
                    {include file="views/exim/components/csv_delimiters.tpl" id="delimiter_{$smarty.const.TM_IMPORT}" name="task_data[task_settings][{$smarty.const.TM_IMPORT}][import_options][delimiter]" value=$task.task_settings.import_options.delimiter|default:$active_layout.options.delimiter}
                </div>
            </div>
            {/if}
            <div class="control-group">
                <label class="control-label">{__("select_file")}:</label>
                <div class="controls">{include file="common/fileuploader.tpl" var_name="import_csv_file[0]" prefix="import_" images=$task.task_settings.uploaded_file delete_link="tasks.remove_file&task_id=`$id`&result_ids=import_section"|fn_url}</div>
                <input type="hidden" name="task_data[task_settings][{$smarty.const.TM_IMPORT}][import_file]" value="{$task.task_settings.import_file}" />
                <input type="hidden" name="task_data[task_settings][{$smarty.const.TM_IMPORT}][url_file]" value="{$task.task_settings.url_file}" />
                <input type="hidden" name="task_data[task_settings][{$smarty.const.TM_IMPORT}][uploaded_file][0][file]" value="{$task.task_settings.uploaded_file.0.file}"/>
                <input type="hidden" name="task_data[task_settings][{$smarty.const.TM_IMPORT}][uploaded_file][0][name]" value="{$task.task_settings.uploaded_file.0.name}"/>
            </div>
            </div>
        {/if}
    <!--import_section--></div>
</div>

<script type='text/javascript'>
    function fn_cp_task_manager_display_import_setting(pattern_id, layout_id) 
    {
        if ({$id}) {
            var url = fn_url('tasks.select_import_pattern?pattern_id=' + pattern_id + '&task_id=' + {$id});
            $.ceAjax('request', url, {
                result_ids: 'import_section',
                force_exec: true,
                method: 'get'
            });
        }
    }
</script>
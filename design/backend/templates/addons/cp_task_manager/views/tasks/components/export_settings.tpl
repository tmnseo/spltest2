{include file="common/subheader.tpl" title=__("cp_task_settings") target="#acc_task_{$smarty.const.TM_EXPORT}"}
<div id="acc_task_{$smarty.const.TM_EXPORT}" class="collapse in">
    <div class="control-group">
        <label class="control-label" for="elm_task_{$smarty.const.TM_EXPORT}_pattern">{__("export")}:</label>
        <div class="controls">
            <select name="task_data[task_settings][{$smarty.const.TM_EXPORT}][pattern_id]" id="elm_task_{$smarty.const.TM_EXPORT}_pattern" onchange="fn_cp_task_manager_display_pattern_setting(this.value, 0);">
            {foreach from=$export_patterns item="pattern"}
                <option {if $task.task_settings.pattern_id == $pattern.pattern_id}selected="selected"{/if} value="{$pattern.pattern_id}">{$pattern.name}</option>
            {/foreach}
            </select>
            {if !$id}
                <p class="muted">{__("cp_save_to_see_details")}</p>
            {/if}   
        </div>
    </div>
    <div id="export_section">
        {if $selected_pat_id == "data_feed"}
            {if $data_feeds}
                <div class="control-group">
                    <label class="control-label">{__("data_feed")}:</label>
                    <div class="controls">
                        <select name="task_data[task_settings][{$smarty.const.TM_EXPORT}][data_feed_id]">
                            {foreach from=$data_feeds item="d_feed"}
                                <option value="{$d_feed.datafeed_id}" {if $selected_feed_id == $d_feed.datafeed_id}selected="selected"{/if}>{$d_feed.datafeed_name}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            {else}
                <p class="no-items">{__("no_data")}</p>
            {/if}
        {else}
            <div class="{if !$task}hidden{/if}">
                <div class="control-group">
                    <label class="control-label">{__("layouts")}:</label>
                    <div class="controls">
                        {if $layouts}
                            <select name="task_data[task_settings][{$smarty.const.TM_EXPORT}][layout_id]" id="s_layout_id" onchange="fn_cp_task_manager_display_pattern_setting($('#elm_task_{$smarty.const.TM_EXPORT}_pattern').val(), this.value);">
                                {foreach from=$layouts item=l}
                                    <option value="{$l.layout_id}" {if $selected_layout.layout_id == $l.layout_id}selected="selected"{/if}>{$l.name}</option>
                                {/foreach}
                            </select>
                        {else}
                            <p class="lowercase">{__("no_items")}</p>
                        {/if}
                    </div>
                </div>
                {include file="common/subheader.tpl" title=$export_pattern.name notes=$smarty.capture.local_notes notes_id=$p_id target="#export_fields_`$id`"}
                <div id="export_fields_{$id}" class="in collapse">
                    {split data=$selected_layout.cols size=5 assign="splitted_fields" simple=true size_is_horizontal=true}
                    <table class="table table-striped table-exim">
                        <tr>
                        {foreach from=$splitted_fields item="fields"}
                            <td>
                                <ul class="unstyled">
                                {foreach from=$fields key="field" item="f"}
                                    <li>{$f}</li>
                                {/foreach}
                                </ul>
                            </td>
                        {/foreach}
                        </tr>
                    </table>
                </div>
                {include file="common/subheader.tpl" title=__("export_options")}
                {if $export_pattern.options}
                    {foreach from=$export_pattern.options key=k item=o}
                    {if !$o.import_only}
                    <div class="control-group">
                        <label for="{$p_id}_{$k}" class="control-label">
                            {__($o.title)}{if $o.description}{include file="common/tooltip.tpl" tooltip=__($o.description)}{/if}:
                        </label>
                        <div class="controls">
                            {if $o.type == "checkbox"}
                                <input type="hidden" name="task_data[task_settings][{$smarty.const.TM_EXPORT}][export_options][{$k}]" value="N" />
                                <input id="{$p_id}_{$k}" class="checkbox" type="checkbox" name="task_data[task_settings][{$smarty.const.TM_EXPORT}][export_options][{$k}]" value="Y" {if $task.task_settings.export_options.$k == "Y"}checked="checked"{elseif $o.default_value == "Y"}checked="checked"{/if} />
                            {elseif $o.type == "input"}
                                <input id="{$p_id}_{$k}" class="input-large" type="text" name="task_data[task_settings][{$smarty.const.TM_EXPORT}][export_options][{$k}]" value="{$task.task_settings.export_options.$k|default:$o.default_value}" />
                            {elseif $o.type == "languages"}
                                <div class="checkbox-list shift-input">
                                    {html_checkboxes name="task_data[task_settings][{$smarty.const.TM_EXPORT}][export_options][lang_code]" options=$export_langs selected=$task.task_settings.export_options.$k|default:$o.default_value columns=8}
                                </div>
                            {elseif $o.type == "select"}
                                <select id="{$p_id}_{$k}" name="task_data[task_settings][{$smarty.const.TM_EXPORT}][export_options][{$k}]">
                                {if $o.variants_function}
                                    {foreach from=$o.variants_function|call_user_func key=vk item=vi}
                                    <option value="{$vk}" {if $task.task_settings.export_options.$k == $vk}checked="checked"{elseif $vk == $o.default_value}checked="checked"{/if}>{$vi}</option>
                                    {/foreach}
                                {else}
                                    {foreach from=$o.variants key=vk item=vi}
                                    <option value="{$vk}" {if $task.task_settings.export_options.$k == $vk}checked="checked"{elseif $vk == $o.default_value}checked="checked"{/if}>{__($vi)}</option>
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
                {assign var="override_options" value=$export_pattern.override_options}
                {if $override_options.delimiter}
                    <input type="hidden" name="task_data[task_settings][{$smarty.const.TM_EXPORT}][export_options][delimiter]" value="{$task.task_settings.export_options.delimiter|default:$override_options.delimiter}" />
                {else}
                    <div class="control-group">
                        <label class="control-label">{__("csv_delimiter")}:</label>
                        <div class="controls">
                            {include file="views/exim/components/csv_delimiters.tpl" id="delimiter_{$smarty.const.TM_EXPORT}" name="task_data[task_settings][{$smarty.const.TM_EXPORT}][export_options][delimiter]" value=$task.task_settings.export_options.delimiter|default:$active_layout.options.delimiter}
                        </div>
                    </div>
                {/if}
                <input type="hidden" name="task_data[task_settings][{$smarty.const.TM_EXPORT}][export_options][output]" value="S" />
                <div class="control-group">
                    <label for="filename" class="control-label">{__("filename")}:</label>
                    <div class="controls">
                        <input type="text" name="task_data[task_settings][{$smarty.const.TM_EXPORT}][export_options][filename]" id="filename" size="50" class="input-large" value="{if $task.task_settings.export_options.filename}{$task.task_settings.export_options.filename}{elseif $export_pattern.filename}{$export_pattern.filename}{else}{$export_pattern.pattern_id}_{$smarty.const.TIME|date_format:"%m%d%Y"}.csv{/if}" />
                        <p class="muted">
                            {__('text_file_editor_notice', ["[href]" => "file_editor.manage?path=/"|fn_url])}
                        </p>
                    </div>
                </div>
            </div>
        {/if}
    <!--export_section--></div>
</div>


<script type='text/javascript'>

function fn_cp_task_manager_display_pattern_setting(pattern_id, layout_id) 
{
    if ({$id}) {
        var url = fn_url('tasks.select_export_pattern?pattern_id=' + pattern_id + '&layout_id=' + layout_id + '&task_id=' + {$id});
        $.ceAjax('request', url, {
            result_ids: 'export_section',
            force_exec: true,
            method: 'get'
        });
    }
}


</script>
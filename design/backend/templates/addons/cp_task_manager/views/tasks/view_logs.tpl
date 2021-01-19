{** logs section **}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="logs_form" class="">
<input type="hidden" name="fake" value="1" />

{include file="common/pagination.tpl" save_current_page=true save_current_url=true div_id=$smarty.request.content_id}

{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}

{assign var="rev" value=$smarty.request.content_id|default:"pagination_contents"}
{assign var="c_icon" value="<i class=\"exicon-`$search.sort_order_rev`\"></i>"}
{assign var="c_dummy" value="<i class=\"exicon-dummy\"></i>"}

{if $logs}
<table class="table table-middle">
<thead>
<tr>
    <th><a class="cm-ajax" href="{"`$c_url`&sort_by=task&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("cp_task")}{if $search.sort_by == "task"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
    <th><a class="cm-ajax" href="{"`$c_url`&sort_by=type&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("type")}{if $search.sort_by == "type"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
    <th><a class="cm-ajax" href="{"`$c_url`&sort_by=start_timestamp&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("cp_start_timestamp")}{if $search.sort_by == "start_timestamp"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
    <th><a class="cm-ajax" href="{"`$c_url`&sort_by=stop_timestamp&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("cp_stop_timestamp")}{if $search.sort_by == "stop_timestamp"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
    <th><a class="cm-ajax" href="{"`$c_url`&sort_by=result&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("cp_result")}{if $search.sort_by == "result"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
    <th width="30%">{__("cp_comment")}</th>
    <th width="6%">&nbsp;</th>
</tr>
</thead>
{foreach from=$logs item=log}
<tr class="cm-row-status-{$log.status|lower}">

    <td class="left"><a href="{"tasks.update&task_id=`$log.task_id`"|fn_url}">{$log.task.task}</a></td>
    <td class="left"><span>{$log.type|fn_cp_task_manager_type_to_string}</span></td>
    <td class="left"><span>{$log.start_timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</span></td>
    <td class="left"><span>{$log.stop_timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</span></td>
    <td class="left"><span>{if $log.result}{$log.result}{/if}</span></td>
    <td class="left"><span>{if $log.download_link}<a href="{$log.download_link}">{$log.comment}</a>{elseif $log.comment}{$log.comment nofilter}{/if}</span>
    {if $log.sub_comment}
        <span id="off_subcomment_{$log.log_id}" alt="{__("expand_collapse_list")}" title="{__("expand_collapse_list")}" class="hidden hand cm-combination"/><span class="exicon-collapse"></span></span>
        <span id="on_subcomment_{$log.log_id}" alt="{__("expand_collapse_list")}" title="{__("expand_collapse_list")}" class="cm-combination"><span class="exicon-expand"></span></span>
        {assign var="subcomment_id" value="subcomment_`$log.log_id`"}
        <div id="{$subcomment_id}" class="hidden row-more">
            {$log.sub_comment nofilter}
        <!--{$subcomment_id}--></div>
    {/if}
    </td>
    <td>
        {capture name="tools_list"}
            {if $log.download_link}
                <li>{btn type="list" class="cm-confirm cm-post" text=__("cp_email_the_link") href="tasks.send_email?log_id=`$log.log_id`&filename=`$log.comment`"}</li>
            {/if}
        {/capture}
        <div class="hidden-tools">
            {dropdown content=$smarty.capture.tools_list}
        </div>
    </td>
</tr>
{/foreach}
</table>
{else}
    <p class="no-items">{__("no_data")}</p>
{/if}

{capture name="sidebar"}
    {include file="common/saved_search.tpl" dispatch="tasks.view_logs" view_type="logs"}
    {include file="addons/cp_task_manager/views/tasks/components/logs_search_form.tpl"}
{/capture}


{capture name="buttons"}
    {capture name="tools_list"}
        <li>{btn type="list" href="tasks.manage" text=__("cp_tasks")}</li>
        <li>{btn type="list" href="tasks.clear_logs" class="cm-confirm cm-post" text=__("cp_clear_logs")}</li>
    {/capture}
    {dropdown content=$smarty.capture.tools_list}
{/capture}
{include file="common/pagination.tpl" div_id=$smarty.request.content_id}
</form>

{/capture}
{include file="common/mainbox.tpl" title=__("cp_logs") content=$smarty.capture.mainbox buttons=$smarty.capture.buttons adv_buttons=$smarty.capture.adv_buttons select_languages=true sidebar=$smarty.capture.sidebar}

{** ad section **}
{** tasks section **}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="tasks_form" class="">
<input type="hidden" name="fake" value="1" />

{include file="common/pagination.tpl" save_current_page=true save_current_url=true div_id=$smarty.request.content_id}

{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}

{assign var="rev" value=$smarty.request.content_id|default:"pagination_contents"}
{assign var="c_icon" value="<i class=\"exicon-`$search.sort_order_rev`\"></i>"}
{assign var="c_dummy" value="<i class=\"exicon-dummy\"></i>"}

{if $tasks}
<table class="table table-middle">
<thead>
<tr>
    <th width="1%" class="left">
        {include file="common/check_items.tpl" class=""}</th>
    <th><a class="cm-ajax" href="{"`$c_url`&sort_by=task&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("cp_task")}{if $search.sort_by == "task"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
    <th><a class="cm-ajax" href="{"`$c_url`&sort_by=next_run&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("cp_next_run")}{if $search.sort_by == "next_run"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
    <th><a class="cm-ajax" href="{"`$c_url`&sort_by=type&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("type")}{if $search.sort_by == "type"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
    {if "MULTIVENDOR"|fn_allowed_for}
        <th><a class="cm-ajax" href="{"`$c_url`&sort_by=approved&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("approval")}{if $search.sort_by == "approved"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
    {/if}
    <th width="6%">&nbsp;</th>
    <th width="10%" class="right"><a class="cm-ajax" href="{"`$c_url`&sort_by=status&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("status")}{if $search.sort_by == "status"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
</tr>
</thead>
{foreach from=$tasks item=task}
<tr class="cm-row-status-{$task.status|lower}">

    <td class="left">
        <input type="checkbox" name="task_ids[]" value="{$task.task_id}" class="cm-item " />
    </td>
    <td class="left">
        <a class="row-status" href="{"tasks.update?task_id=`$task.task_id`"|fn_url}">{$task.task}</a>
        {include file="views/companies/components/company_name.tpl" object=$task}
    </td>
    <td class="left">
        {$task.next_run|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}
    </td>
    <td class="left">
        <span>{$task.type|fn_cp_task_manager_type_to_string}</span>
    </td>
    {if "MULTIVENDOR"|fn_allowed_for}
        <td class="left">
            {if $task.company_id}
                {if $task.approved == "A"}
                    {__("cp_aa_approved")}
                {else}
                    {__("cp_aa_disapproved")}
                {/if}
            {else}
                -
            {/if}
        </td>
    {/if}
    <td>
        {capture name="tools_list"}
            <li>{btn type="list" text=__("edit") href="tasks.update?task_id=`$task.task_id`"}</li>
            <li>{btn type="list" class="cm-confirm cm-post" text=__("delete") href="tasks.delete?task_id=`$task.task_id`"}</li>
            {if !$cp_aa_is_vendor && "MULTIVENDOR"|fn_allowed_for && $task.company_id}
                {if $task.approved == "A"}
                    <li>{btn type="list" class="cm-confirm cm-post" text=__("cp_aa_set_disapprove") href="tasks.set_approve?task_id=`$task.task_id`&action=D"}</li>
                {else}
                    <li>{btn type="list" class="cm-confirm cm-post" text=__("cp_aa_set_approve") href="tasks.set_approve?task_id=`$task.task_id`&action=A"}</li>
                {/if}
            {/if}
        {/capture}
        <div class="hidden-tools">
            {dropdown content=$smarty.capture.tools_list}
        </div>
    </td>
    <td class="right">
        {include file="common/select_popup.tpl" id=$task.task_id status=$task.status hidden=false object_id_name="task_id" table="cp_tasks" popup_additional_class=" dropleft"}
    </td>
</tr>
{/foreach}
</table>
{else}
    <p class="no-items">{__("no_data")}</p>
{/if}

{capture name="buttons"}
    {capture name="tools_list"}
        {if $tasks}
            <li>{btn type="delete_selected" class="cm-confirm" dispatch="dispatch[tasks.m_delete]" form="tasks_form"}</li>
            <li>{btn type="list" text=__("cp_aa_approve_selected") dispatch="dispatch[tasks.m_approve]" form="tasks_form"}</li>
            <li>{btn type="list" text=__("cp_aa_disapprove_selected") dispatch="dispatch[tasks.m_dapprove]" form="tasks_form"}</li>
        {/if}
        <li>{btn type="list" href="tasks.view_logs" text=__("cp_view_logs")}</li>
    {/capture}
    {dropdown content=$smarty.capture.tools_list}
{/capture}
{capture name="adv_buttons"}
    {include file="common/tools.tpl" tool_href="tasks.add" prefix="top" hide_tools="true" title=__("cp_add_task") icon="icon-plus"}
{/capture}
{include file="common/pagination.tpl" div_id=$smarty.request.content_id}
</form>

{/capture}
{include file="common/mainbox.tpl" title=__("cp_tasks") content=$smarty.capture.mainbox buttons=$smarty.capture.buttons adv_buttons=$smarty.capture.adv_buttons select_languages=true}

{** ad section **}
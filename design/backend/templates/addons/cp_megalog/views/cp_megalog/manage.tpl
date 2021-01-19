{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="megalogs_manage_form" class="{if ""|fn_check_form_permissions} cm-hide-inputs{/if}">

{include file="common/pagination.tpl" save_current_page=true save_current_url=true}

{$c_url=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
{$c_icon="<i class=\"exicon-`$search.sort_order_rev`\"></i>"}
{$c_dummy="<i class=\"exicon-dummy\"></i>"}

{if $logs && $ml_types}
    <div class="table-responsive-wrapper">
        <table class="table table-middle">
            <thead>
                <tr>
                    <th width="20%">
                        <a class="cm-ajax" href="{"`$c_url`&sort_by=controller&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">
                            {__("cp_ml_controller")}{if $search.sort_by == "controller"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}
                        </a>
                    </th>
                    <th width="10%">
                        <a class="cm-ajax" href="{"`$c_url`&sort_by=mode&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">
                            {__("cp_ml_mode")}{if $search.sort_by == "mode"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}
                        </a>
                    </th>
                    <th width="20%">
                        <a class="cm-ajax" href="{"`$c_url`&sort_by=name&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">
                            {__("user")}{if $search.sort_by == "name"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}
                        </a>
                    </th>
                    <th width="30%">{__("content")}</th>
                    <th width="20%">
                        <a class="cm-ajax" href="{"`$c_url`&sort_by=timestamp&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">
                            {__("date")}{if $search.sort_by == "timestamp"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}
                        </a>
                    </th>
                </tr>
            </thead>
            {foreach from=$logs item="log"}
                {if $ml_types}
                    {$l_controller=$log.controller}
                    {$l_mode=$log.mode}
                    {if $ml_types.$l_controller && ($ml_types.$l_controller.$l_mode || $l_controller == "cp_megalog")}
                        {$cur_l_type=$ml_types.$l_controller.$l_mode}
                        {$allow_save=$log|fn_allow_save_object:"log"}
                        {if $allow_save}
                            {$link_text=__("edit")}
                            {$additional_class="cm-no-hide-input"}
                            {$status_display=""}
                        {else}
                            {$link_text=__("view")}
                            {$additional_class="cm-hide-inputs"}
                            {$status_display="text"}
                        {/if}
                        <tr class="cm-row-status-{$log.status|lower} {$additional_class}">
                            
                            <td data-th="{__("cp_ml_controller")}">{$log.controller}</td>
                            <td data-th="{__("cp_ml_mode")}">{$log.mode}</td>
                            <td class="row-status" data-th="{__("user")}">{if $log.firstname || $log.lastname || $log.user_id}<a href="{"profiles.update?user_id=`$log.user_id`&user_type=`$log.user_type`"|fn_url}">{if $log.firstname || $log.lastname}{$log.lastname} {$log.firstname}{else}{$log.user_id}{/if}</a>{else}-{/if}</td>
                            <td>
                                {if $cur_l_type.prefix}{$cur_l_type.prefix} - {/if}
                                {if $cur_l_type.object_link}
                                    <a href="{"`$cur_l_type.object_link``$log.object_id`"|fn_url}">#{$log.object_id}</a>
                                {else}
                                    {$log.object_id}
                                {/if}
                                {if $cur_l_type.label && $log.parce_request && $log.parce_request.label}{if $cur_l_type.label_langvar}{__($log.parce_request.label)}{else}{$log.parce_request.label}{/if}{/if}
                                
                                {hook name="cp_megalog:body_row_post"}{/hook}
                                <span id="on_extra_log_{$log.log_id}" alt="{__("expand_collapse_list")}" title="{__("expand_collapse_list")}" class="hand cm-combination-options-{$log.log_id}"><span class="icon-caret-right"></span></span>
                                <span id="off_extra_log_{$log.log_id}" alt="{__("expand_collapse_list")}" title="{__("expand_collapse_list")}" class="hand hidden cm-combination-options-{$log.log_id}"><span class="icon-caret-down"></span> </span>
                                
                            </td>
                            <td class="nowrap" data-th="{__("date")}">{$log.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
                        </tr>
                        <tr id="extra_log_{$log.log_id}" class="hidden cm-ex-op">
                            <td colspan="5" class="cp-megalog__content">
                                {if $log.parce_req}
                                    {$log.parce_req}
                                {else}
                                    {__("no_data")}
                                {/if}
                            </td>
                        </tr>
                    {/if}
                {/if}
            {/foreach}
        </table>
    </div>
{else}
    <p class="no-items">{__("no_data")}</p>
{/if}

{include file="common/pagination.tpl"}
{capture name="buttons"}
    {capture name="tools_list"}
        {if $logs}
            <li>{btn type="list" text=__("clean_logs") dispatch="dispatch[cp_megalog.clear_logs]" form="megalogs_manage_form"}</li>
        {/if}
    {/capture}
    {dropdown content=$smarty.capture.tools_list}
{/capture}

{capture name="sidebar"}
    {include file="addons/cp_megalog/components/megalog_search_form.tpl" dispatch="cp_megalog.manage"}
{/capture}

</form>
{/capture}
{include file="common/mainbox.tpl" title=__("cp_ml_mega_logs") content=$smarty.capture.mainbox tools=$smarty.capture.tools buttons=$smarty.capture.buttons sidebar=$smarty.capture.sidebar}
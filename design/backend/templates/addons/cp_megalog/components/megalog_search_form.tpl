{if $in_popup}
    <div class="adv-search">
    <div class="group">
{else}
    <div class="sidebar-row">
    <h6>{__("search")}</h6>
{/if}
<form name="user_search_form" action="{""|fn_url}" method="get" class="{$form_meta}">

{if $smarty.request.redirect_url}
<input type="hidden" name="redirect_url" value="{$smarty.request.redirect_url}" />
{/if}

{if $selected_section != ""}
<input type="hidden" id="selected_section" name="selected_section" value="{$selected_section}" />
{/if}

{if $put_request_vars}
    {array_to_fields data=$smarty.request skip=["callback"] escape=["data_id"]}
{/if}

{capture name="simple_search"}
{$extra nofilter}
<div class="sidebar-field">
    <label for="elm_name">{__("user")}</label>
    <div class="break">
        <input type="text" name="name" id="elm_name" value="{$search.name}" />
    </div>
</div>
<div class="sidebar-field">
    <label for="elm_controller">{__("cp_ml_controller")}</label>
    <div class="break">
        <select name="controller" id="elm_controller">
            <option value="">-{__("none")}-</option>
            {if $all_controllers}
                {foreach from=$all_controllers item="l_controller"}
                    <option value="{$l_controller}" {if $l_controller == $search.controller}selected="selected"{/if}>{$l_controller}</option>
                {/foreach}
            {/if}
        </select>
    </div>
</div>
<div class="sidebar-field">
    <label for="elm_mode">{__("cp_ml_mode")}</label>
    <div class="break">
        <select name="mode" id="elm_mode">
            <option value="">-{__("none")}-</option>
            {if $all_modes}
                {foreach from=$all_modes item="l_mode"}
                    <option value="{$l_mode}" {if $l_mode == $search.mode}selected="selected"{/if}>{$l_mode}</option>
                {/foreach}
            {/if}
        </select>
    </div>
</div>
<div class="sidebar-field">
    <label for="elm_object_id">{__("cp_ml_object_id")}</label>
    <div class="break">
        <input type="text" name="object_id" id="elm_object_id" value="{$search.object_id}" />
    </div>
</div>
{/capture}

{include file="common/advanced_search.tpl" simple_search=$smarty.capture.simple_search advanced_search=$smarty.capture.advanced_search dispatch=$dispatch view_type="cp_megalog" in_popup=$in_popup}

</form>

{if $in_popup}
</div></div>
{else}
</div><hr>
{/if}

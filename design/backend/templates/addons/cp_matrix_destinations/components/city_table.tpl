{capture name="mainbox"}

    <form action="{""|fn_url}" method="post" name="states_form" class="{if $runtime.company_id} cm-hide-inputs{/if}">
        <input type="hidden" name="country_code" value="{$search.country}" />

        {include file="common/pagination.tpl" save_current_page=true save_current_url=true}

        {if $cities}
            <div class="table-responsive-wrapper">
                <table width="100%" class="table table-middle table--relative table-responsive">
                    <thead>
                    <tr>
                        <th width="1%" class="mobile-hide">{include file="common/check_items.tpl"}</th>
                        <th width="60%">{__("city")}</th>
                        <th width="60%">{__("state")}</th>
                        <th width="5%">&nbsp;</th>
                        <th class="right" width="10%">{__("status")}</th>
                    </tr>
                    </thead>
                    {foreach from=$cities item=city}
                        <tr class="cm-row-status-{$city.status|lower}">
                            <td class="mobile-hide">
                                <input type="checkbox" name="city_ids[]" value="{$city.city_id}" class="cm-item" /></td>

                            <td data-th="{__("city")}">
                                <input type="text" name="city_data[{$city.city_id}][city_name]" size="55" value="{$city.city_name}" class="input-hidden span8"/>
                            </td>

                            <td data-th="{__("state")}">
                                <input type="text" name="city_data[{$city.city_id}][state_code]" size="55" value="{$city.state_code}" class="input-hidden span2"/>
                            </td>
                            <td class="nowrap" data-th="{__("tools")}">
                                {capture name="tools_list"}
                                    <li>{btn type="list" class="cm-confirm" text=__("delete") href="cp_{$model}city.delete?city_id=`$city.city_id`" method="POST"}</li>
                                {/capture}
                                <div class="hidden-tools">
                                    {dropdown content=$smarty.capture.tools_list}
                                </div>
                            </td>
                            <td class="right" data-th="{__("status")}">
                                {*$has_permission = fn_check_permissions("tools", "update_status", "admin", "GET", ["table" => "states"])*}
                                {include file="common/select_popup.tpl" id=$city.city_id status=$city.status hidden="" object_id_name="city_id" table="cp_`$model`matrix_cities" non_editable=false}
                            </td>
                        </tr>
                    {/foreach}
                </table>
            </div>
        {else}
            <p class="no-items">{__("no_data")}</p>
        {/if}

        {include file="common/pagination.tpl"}

    </form>

    {capture name="tools"}
        {capture name="add_new_picker"}

            <form action="{""|fn_url}" method="post" name="add_states_form" class="form-horizontal form-edit">
                <input type="hidden" name="city_data[country_code]" value="{$search.country_code}" />
                <input type="hidden" name="country_code" value="{$search.country_code}" />
                <input type="hidden" name="city_id" value="0" />




                    <fieldset>

                        <div class="control-group">
                            <label class="control-label" for="elm_state_name">{__("cp_matrix_destinations_city_name")}:</label>
                            <div class="controls">
                                <input type="text" id="elm_state_name" name="city_data[city_name]" size="55" value="" />
                            </div>
                        </div>


                        {if !$model}
                            <label id="cp_state_id_lbl">{__("state")}:</label>
                            <select name="city_data[state_code]" class="" id="cp_state_id">
                                <option value="">- {__("select_state")} -</option>
                                {if $states}
                                    {foreach from=$states item="state"}
                                        <option {if $_state == $state.code}selected="selected"{/if} value="{$state.code}">{$state.state}</option>
                                    {/foreach}
                                {/if}
                            </select>
                        {/if}

                    </fieldset>


                <div class="buttons-container">
                    {include file="buttons/save_cancel.tpl" create=true but_name="dispatch[cp_{$model}city.update]" cancel_action="close"}
                </div>

            </form>

        {/capture}
    {/capture}

    {capture name="buttons"}
        {capture name="tools_list"}
            {hook name="states:manage_tools_list"}
            {if $cities}
                <li>{btn type="delete_selected" dispatch="dispatch[cp_{$model}city.m_delete]" form="states_form"}</li>
            {/if}
            {/hook}
        {/capture}
        {dropdown content=$smarty.capture.tools_list}

        {if $cities}
            {include file="buttons/save.tpl" but_name="dispatch[cp_{$model}city.m_update]" but_role="action" but_target_form="states_form" but_meta="cm-submit"}
        {/if}
    {/capture}

    {capture name="adv_buttons"}
        {include file="common/popupbox.tpl" id="new_state" action="cp_{$model}city.update" text=$title content=$smarty.capture.add_new_picker title=__("add_state") act="general" icon="icon-plus"}
    {/capture}

    {capture name="sidebar"}
        <div class="sidebar-row">
            <h6>{__("search")}</h6>
            <form action="{""|fn_url}" name="states_filter_form" method="get">
                <div class="sidebar-field">
                    <label>{__("city")}:</label>


                </div>
                {include file="buttons/search.tpl" but_name="dispatch[cp_{$model}city.manage]"}
            </form>
        </div>
    {/capture}


{/capture}
{include file="common/mainbox.tpl" title=__("cities") content=$smarty.capture.mainbox adv_buttons=$smarty.capture.adv_buttons buttons=$smarty.capture.buttons sidebar=$smarty.capture.sidebar select_languages=true}
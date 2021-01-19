{capture name="mainbox"}

    <form action="{""|fn_url}" method="post" name="states_form" class="{if $runtime.company_id} cm-hide-inputs{/if}">
        <input type="hidden" name="country_code" value="{$search.country}" />

        {include file="common/pagination.tpl" save_current_page=true save_current_url=true}

        {if $matrix_data}
            <div class="table-responsive-wrapper">
                <table width="100%" class="table table-middle table--relative table-responsive">
                    <thead>
                    <tr>
                        {*<th width="5%" class="mobile-hide">{include file="common/check_items.tpl"}</th>*}
                        <th width="10%">{__("cp_matrix_destinations_city_from")}</th>
                        <th width="10%">{__("cp_matrix_destinations_city_to")}</th>
                        <th width="10%">{__("cp_matrix_destinations_last_time_update")}</th>
                        <th width="10%">{__("cp_matrix_destinations_time_from")}</th>
                        <th width="10%">{__("cp_matrix_destinations_time_to")}</th>
                        <th width="10%">{__("cp_matrix_destinations_average_time")}</th>

                    </tr>
                    </thead>
                    {foreach from=$matrix_data item=matrix}
                        <tr class="cm-row-status-a">
                            {*<td class="mobile-hide">
                                <input type="checkbox" name="matrix_ids[]" value="{$matrix.city_from}_{$matrix.city_to}" class="cm-item" /></td>*}

                            <td data-th="{__("cp_matrix_destinations_city_from")}">
                                <input type="text" disabled="disabled" size="55" value="{$matrix.city_from}  {$matrix.city_from_state}" class=""/>
                            </td>
                            <td data-th="{__("cp_matrix_destinations_city_to")}">
                                <input type="text" disabled="disabled" size="55" value="{$matrix.city_to} {$matrix.city_to_state}" class=""/>
                            </td>
                            <td data-th="{__("cp_matrix_destinations_last_time_update")}">
                                <input type="text" disabled="disabled" size="55" value="{$matrix.last_time_update|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}" class=""/>
                            </td>
                            <td data-th="{__("cp_matrix_destinations_time_from")}">
                                <input type="text" disabled="disabled" size="55" value="{$matrix.time_from}" class="input-small"/>
                            </td>
                            <td data-th="{__("cp_matrix_destinations_time_to")}">
                                <input type="text" disabled="disabled" size="55" value="{$matrix.time_to}" class="input-small"/>
                            </td>
                            <td data-th="{__("cp_matrix_destinations_average_time")}">
                                <input type="text" disabled="disabled" size="55" value="{$matrix.time_average}" class="input-small"/>
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

                <div class="cm-j-tabs">
                    <ul class="nav nav-tabs">
                        <li id="tab_new_states" class="cm-js active"><a>{__("general")}</a></li>
                    </ul>
                </div>
                <div class="cm-tabs-content">
                    <fieldset>

                        <div class="control-group">
                            <label class="control-label" for="elm_state_name">{__("cp_matrix_destinations_city_name")}:</label>
                            <div class="controls">
                                <input type="text" id="elm_state_name" name="city_data[city_name]" size="55" value="" />
                            </div>
                        </div>

                        {include file="common/select_status.tpl" input_name="city_data[status]" id="elm_state_status"}
                    </fieldset>
                </div>

                <div class="buttons-container">
                    {include file="buttons/save_cancel.tpl" create=true but_name="dispatch[cp_city.update]" cancel_action="close"}
                </div>
            </form>
        {/capture}
    {/capture}

    {capture name="buttons"}
        {capture name="tools_list"}
            {hook name="states:manage_tools_list"}
            {if $cities}
                <li>{btn type="delete_selected" dispatch="dispatch[cp_matrix_destinations.m_delete]" form="states_form"}</li>
            {/if}
            {/hook}
        {/capture}
        {dropdown content=$smarty.capture.tools_list}

        {if $states}
            {include file="buttons/save.tpl" but_name="dispatch[cp_matrix_destinations.m_update]" but_role="action" but_target_form="states_form" but_meta="cm-submit"}
        {/if}
    {/capture}

    {capture name="adv_buttons"}
        {include file="common/popupbox.tpl" id="new_state" action="cp_matrix_destinations.update" text=$title content=$smarty.capture.add_new_picker title=__("add_state") act="general" icon="icon-plus"}
    {/capture}

    {capture name="sidebar"}
        <div class="sidebar-row">
            <h6>{__("search")}</h6>
            <form action="{""|fn_url}" name="states_filter_form" method="get">
                <div class="sidebar-field">
                    <label>{__("country")}:</label>

                    {*
                    <select name="country_code">
                        {foreach from=$countries item="country" key="code"}
                            <option {if $code == $search.country_code}selected="selected"{/if} value="{$code}">{$country}</option>
                        {/foreach}
                    </select>

                    *}
                </div>
                {include file="buttons/search.tpl" but_name="dispatch[cp_matrix_destinations.manage]"}
            </form>
        </div>
    {/capture}


{/capture}
{include file="common/mainbox.tpl" title=__("cp_matrix_manage_title") content=$smarty.capture.mainbox adv_buttons=$smarty.capture.adv_buttons buttons=$smarty.capture.buttons sidebar=$smarty.capture.sidebar select_languages=true}
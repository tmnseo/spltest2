{capture name="mainbox"}

    {if $settings}
        <div class="table-responsive-wrapper">
            <table width="100%" class="table table-middle table--relative table-responsive">
                <thead>
                <tr>
                    <th width="50%">{__("cp_matrix_settings_name")}</th>
                    <th width="50%">{__("cp_matrix_settings_value")}</th>
                </tr>
                </thead>

                {foreach from=$cp_matrix_settings item=setting}
                    <tr class="cm-row-status-">

                        <td data-th="{__("cp_matrix_setting")}">
                            <input type="text" disabled="disabled"  size="55" value="{$setting.settings_id}" class="input-medium"/>
                        </td>
                        <td class="nowrap" data-th="{__("cp_matrix_settings_value")}">

                            <input type="text" disabled="disabled"  size="55" value="{$setting.value}" class="input-medium"/>

                        </td>

                    </tr>
                {/foreach}
            </table>
        </div>
    {else}
        <p class="no-items">{__("no_data")}</p>
    {/if}



{/capture}
{include file="common/mainbox.tpl" title=__("cp_matrix_setting") content=$smarty.capture.mainbox adv_buttons=$smarty.capture.adv_buttons buttons=$smarty.capture.buttons sidebar=$smarty.capture.sidebar select_languages=true}
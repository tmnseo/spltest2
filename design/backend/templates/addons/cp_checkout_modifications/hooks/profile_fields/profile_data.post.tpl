<div class="control-group">
    <label class="control-label">{__("cp_profile_sections")}:</label>
    <div class="controls">
        <select id="elm_field_type" name="field_data[cp_profile_section]" >
            <option value="" {if $field.cp_profile_section == ''}selected="selected"{/if}>----</option>
            <option value="{$smarty.const.CP_SECTION_CONTACT_INFO}" {if $field.cp_profile_section == $smarty.const.CP_SECTION_CONTACT_INFO}selected="selected"{/if}>{__("cp_profile_sections.contact_information")}</option>
            <option value="{$smarty.const.CP_SECTION_COMPANY_INFO}" {if $field.cp_profile_section == $smarty.const.CP_SECTION_COMPANY_INFO}selected="selected"{/if}>{__("cp_profile_sections.company_information")}</option>
            <option value="{$smarty.const.CP_SECTION_ACTUAL_ADDRESS}" {if $field.cp_profile_section == $smarty.const.CP_SECTION_ACTUAL_ADDRESS}selected="selected"{/if}>{__("cp_profile_sections.actual_address")}</option>
            <option value="{$smarty.const.CP_SECTION_ADDITION_INFO}" {if $field.cp_profile_section == $smarty.const.CP_SECTION_ADDITION_INFO}selected="selected"{/if}>{__("cp_profile_sections.additional_info")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label">{__("cp_profile.edited_only_by_admin")}</label>
    <div class="controls">
        <input type="hidden" name="field_data[cp_edited_only_by_admin]" value="N" />
        <input type="checkbox" name="field_data[cp_edited_only_by_admin]" value="Y" {if $field.cp_edited_only_by_admin == "Y"}checked="checked"{/if} />
    </div>
</div>

<div class="control-group">
    <label class="control-label">{__("cp_profile.edit_if_empty")}
        {include file="common/tooltip.tpl" tooltip=__("cp_profile.edit_if_empty_tooltip")}:</label>
    <div class="controls">
        <input type="hidden" name="field_data[cp_edit_if_empty]" value="N" />
        <input type="checkbox" name="field_data[cp_edit_if_empty]" value="Y" {if $field.cp_edit_if_empty == "Y"}checked="checked"{/if} />
    </div>
</div>

{$_hide = "cp_add_hide"}
{$_disabled = "cp_add_disabled"}
<div class="control-group">
    <label class="control-label">{__("registration")} ({__("hide")}&nbsp;/&nbsp;{__("disabled")}):</label>
    <div class="controls">
        <input type="hidden" name="field_data[{$_hide}]" value="N" />
        <input type="checkbox" name="field_data[{$_hide}]" value="Y" {if $field.$_hide == "Y"}checked="checked"{/if} />&nbsp;

        <input type="hidden" name="field_data[{$_disabled}]" value="N" />
        <input type="checkbox" name="field_data[{$_disabled}]" value="Y" {if $field.$_disabled == "Y"}checked="checked"{/if} />
    </div>
</div>

{$_hide = "cp_update_hide"}
{$_disabled = "cp_update_disabled"}
<div class="control-group">
    <label class="control-label">{__("profile")} ({__("hide")}&nbsp;/&nbsp;{__("disabled")}):</label>
    <div class="controls">
        <input type="hidden" name="field_data[{$_hide}]" value="N" />
        <input type="checkbox" name="field_data[{$_hide}]" value="Y" {if $field.$_hide == "Y"}checked="checked"{/if} />&nbsp;

        <input type="hidden" name="field_data[{$_disabled}]" value="N" />
        <input type="checkbox" name="field_data[{$_disabled}]" value="Y" {if $field.$_disabled == "Y"}checked="checked"{/if} />
    </div>
</div>


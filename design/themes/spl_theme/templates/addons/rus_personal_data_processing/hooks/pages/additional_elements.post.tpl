{if $addons.cp_spl_theme.id_page_profiles_add == $page.page_id}
    {$but_text=__("become_a_supplier")}
{elseif $addons.cp_order_pretensions.pretension_page_id == $page.page_id}
    {$but_text=__("send_a_complaint")}
    {$but_meta="ty-btn__secondary ty-btn__order-pretensions "}
{elseif $addons.cp_spl_theme.id_page_feedback == $page.page_id}
    {$but_text=__("cp_ask_question")}
    {$but_meta="ty-btn__secondary ty-btn__feedback"}
{else}
    {$but_text=__("submit")}
{/if}

<div class="controls ty-personal-data cm-field-container">
    <input class="hidden" type="checkbox" id="elm_personal_data" value="Y" checked="checked" />
    <label class="cm-required" for="elm_personal_data">{__("addons.rus_personal_data_processing.confidentiality")}</label>
    <span class="ty-policy-description">{__("addons.rus_personal_data_processing.policy_description", ["[name_button]" => $but_text, "[link]" => $policy_link])}</span>
</div>
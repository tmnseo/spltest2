{hook name="pages:page_content"}{/hook}
{hook name="pages:cp_page_content"}{/hook}
{if $page.page_type == $smarty.const.PAGE_TYPE_FORM}
    {if $smarty.request.sent == "Y"}

        {hook name="pages:form_sent"}
        {assign var="form_submit_const" value=$smarty.const.FORM_SUBMIT}
        <p>{$page.form.general.$form_submit_const nofilter}</p>
        {/hook}

        <div class="ty-form-builder__buttons buttons-container">
        {include file="buttons/button.tpl" but_text=__("continue") but_meta="ty-btn__secondary" but_href=$continue_url|fn_url but_role="action"}
        </div>
    {else}

    {* {if $page.description}
        <div class="ty-form-builder__description">{$page.description nofilter}</div>
    {/if} *}

<div class="ty-form-builder">
    <form action="{""|fn_url}" method="post" name="forms_form" enctype="multipart/form-data">
    <input type="hidden" name="fake" value="1" />
    <input type="hidden" name="page_id" value="{$page.page_id}" />

    {foreach from=$page.form.elements key="element_id" item="element" name="form_lements"}
    
    {if $page.page_id == $addons.cp_popup_notifications.feedback_page_id}
        {$element_id = $element_id|cat:"_`$page.page_id`"}
        {$element.element_id = $element.element_id|cat:"_`$page.page_id`"}
    {/if}
    
    {if $element.element_type == $smarty.const.FORM_SEPARATOR}
        <hr class="ty-form-builder__separator" />
    {elseif $element.element_type == $smarty.const.FORM_HEADER}

    {include file="common/subheader.tpl" title=$element.description}
    
    {elseif $element.element_type != $smarty.const.FORM_IP_ADDRESS && $element.element_type != $smarty.const.FORM_REFERER}
        <div class="ty-control-group{if $element.note} ty-control-group_note {/if}">
            <label for="{if $element.element_type == $smarty.const.FORM_FILE}type_{"fb_files[`$element.element_id`]"|md5}{else}elm_{$element.element_id}{/if}" class="ty-control-group__title {if $element.required == "Y"}cm-required{/if}{if $element.element_type == $smarty.const.FORM_EMAIL} cm-email{/if}{if $element.element_type == $smarty.const.FORM_PHONE} cm-phone{/if} {if $element.element_type == $smarty.const.FORM_MULTIPLE_CB}cm-multiple-checkboxes{/if}">
                {$element.description}
            </label>
            {if $element.note}
                <span class="ty-control-group__note">{$element.note}</span>
            {/if}
                
            {if $element.element_type == $smarty.const.FORM_SELECT}
                <select id="elm_{$element.element_id}" class="ty-form-builder__select" name="form_values[{$element.element_id}]">
                    <option label="" value="">- {__("select")} -</option>
                {foreach from=$element.variants item=var}
                    <option value="{$var.element_id}" {if $form_values.$element_id == $var.element_id}selected="selected"{/if}>{$var.description}</option>
                {/foreach}
                </select>
                
                
            {elseif $element.element_type == $smarty.const.FORM_RADIO}
                {foreach from=$element.variants item=var name="rd"}
                <label class="ty-form-builder__radio-label">
                    <input class="ty-form-builder__radio radio" {if (!$form_values && $smarty.foreach.rd.iteration == 1) || ($form_values.$element_id == $var.element_id)}checked="checked"{/if} type="radio" name="form_values[{$element.element_id}]" value="{$var.element_id}" />{$var.description}&nbsp;&nbsp;
                </label>
                {/foreach}
                
                
            {elseif $element.element_type == $smarty.const.FORM_CHECKBOX}
                <input type="hidden" name="form_values[{$element.element_id}]" value="N" />
                <input id="elm_{$element.element_id}" class="ty-form-builder__checkbox checkbox" {if $form_values.$element_id == "Y"}checked="checked"{/if} type="checkbox" name="form_values[{$element.element_id}]" value="Y" />
                
                
            {elseif $element.element_type == $smarty.const.FORM_MULTIPLE_SB}
                <select class="ty-form-builder__multiple-select" id="elm_{$element.element_id}" name="form_values[{$element.element_id}][]" multiple="multiple" >
                    {foreach from=$element.variants item=var}
                        <option value="{$var.element_id}" {if $var.element_id|in_array:$form_values.$element_id}selected="selected"{/if}>{$var.description}</option>
                    {/foreach}
                </select>
                
                
            {elseif $element.element_type == $smarty.const.FORM_MULTIPLE_CB}
                <div id="elm_{$element.element_id}">
                {foreach from=$element.variants item=var}
                    <label class="ty-form-builder__checkbox-label">
                        <input class="ty-form-builder__checkbox hidden" type="checkbox" {if $var.element_id|in_array:$form_values.$element_id}checked="checked"{/if} id="elm_{$element.element_id}_{$var.element_id}" name="form_values[{$element.element_id}][]" value="{$var.element_id}" />
                        <span>{$var.description}</span>
                    </label>
                {/foreach}
                </div>
                
                
            {elseif $element.element_type == $smarty.const.FORM_INPUT}
                <input id="elm_{$element.element_id}" class="ty-form-builder__input-text ty-input-text {if $element.position == 1}cm-focus {/if}" size="50" type="text" name="form_values[{$element.element_id}]" value="{$form_values.$element_id}" />

            {elseif $element.element_type == $smarty.const.FORM_TEXTAREA}
                <textarea id="elm_{$element.element_id}" class="ty-form-builder__textarea" name="form_values[{$element.element_id}]" cols="67" rows="10">{$form_values.$element_id}</textarea>

            {elseif $element.element_type == $smarty.const.FORM_DATE}
                {include file="common/calendar.tpl" date_name="form_values[`$element.element_id`]" date_id="elm_`$element.element_id`" date_val=$form_values.$element_id}

            {elseif $element.element_type == $smarty.const.FORM_EMAIL || $element.element_type == $smarty.const.FORM_NUMBER || $element.element_type == $smarty.const.FORM_PHONE}

                {if $element.element_type == $smarty.const.FORM_EMAIL}
                <input type="hidden" name="customer_email" value="{$element.element_id}" />
                {/if}
                <input id="elm_{$element.element_id}" class="ty-input-text {if $element.position == 1}cm-focus {/if}" size="50" type="text" name="form_values[{$element.element_id}]" value="{$form_values.$element_id}" />
                
            {elseif $element.element_type == $smarty.const.FORM_COUNTRIES}
                {$_country = $form_values.$element_id|default:$settings.General.default_country}

                {if !$k_country}
                    {assign var="k_country" value=1}
                {else}
                    {$k_country = $k_country + 1}
                {/if}

                <select id="elm_{$element.element_id}" name="form_values[{$element.element_id}]" class="ty-form-builder__country cm-country cm-location-billing_{$k_country}">
                    <option value="">- {__("select_country")} -</option>
                    {assign var="countries" value=1|fn_get_simple_countries}
                    {foreach from=$countries item="country" key="code"}
                    <option value="{$code}" {if $_country == $code}selected="selected"{/if}>{$country}</option>
                    {/foreach}
                </select>

            {elseif $element.element_type == $smarty.const.FORM_STATES}
                {if !$k_state}
                    {assign var="k_state" value=1}
                {else}
                    {$k_state = $k_state + 1}
                {/if}

                {include file="views/profiles/components/profiles_scripts.tpl" states=1|fn_get_all_states}

                {$_state = $form_values.$element_id|default:$settings.General.default_state}
                <select class="ty-form-builder__state cm-state cm-location-billing_{$k_state}" id="elm_{$element.element_id}" name="form_values[{$element.element_id}]">
                    <option label="" value="">- {__("select_state")} -</option>
                    {assign var="states" value=1|fn_get_all_states}
                    {foreach from=$states.$_country item="state"}
                    <option value="{$state.code}" {if $_state == $state.code}selected="selected"{/if}>{$state.state}</option>
                    {/foreach}
                </select>
                <input type="text" class="cm-state cm-location-billing_{$k_state} ty-input-text hidden" id="elm_{$element.element_id}_d" name="form_values[{$element.element_id}]" size="32" maxlength="64" value="{$_state}" disabled="disabled" />
            
            {elseif $element.element_type == $smarty.const.FORM_FILE}
                {if $addons.cp_spl_theme.id_page_feedback == $page.page_id}
                    <div class="cp-form__fileuploader">
                        {script src="js/tygh/fileuploader_scripts.js"}
                        {$file_element_id = ('_'|explode:$element.element_id)|current}
                        {include file="common/fileuploader.tpl" var_name="fb_files[`$file_element_id`]" multiupload="Y" upload_file_text=__("attach") upload_another_file_text=__("attach")}
                    </div>
                {else}
                    {script src="js/tygh/fileuploader_scripts.js"}
                    {include file="common/fileuploader.tpl" var_name="fb_files[`$element.element_id`]"}
                {/if}
            {/if}

            {hook name="pages:form_elements"}
            {/hook}
        </div>
    {/if}
    {/foreach}
    

    {include file="common/image_verification.tpl" option="form_builder"}

    <div class="ty-form-builder__buttons buttons-container">
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
        {include file="buttons/button.tpl" but_role="submit" but_text=$but_text but_meta=$but_meta|default:"ty-btn__primary" but_name="dispatch[pages.send_form]"}
        {if $addons.cp_order_pretensions.pretension_page_id == $page.page_id}
            <a class="ty-btn ty-btn__tertiary ty-btn__rethink" href="{"orders.search"|fn_url}" rel="nofollow">{__("changed_my_mind")}</a>
        {/if}
        {if $addons.cp_spl_theme.id_page_feedback == $page.page_id}
            <a class="ty-btn ty-btn__tertiary ty-btn__rethink" href="{""|fn_url}" rel="nofollow">{__("not_now")}</a>
        {/if}
    </div>
    
    {hook name="pages:additional_elements"}
    {/hook}

    {include file="addons/sw_checkbox_pd/views/check_box_pd.tpl" option="page_form"}



    </form>

</div>
{/if}
{/if}
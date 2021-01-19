<div class="clearfix">
    <div class="controls ty-personal-data">
        {$checkbox_uniq_id = "accept_subscribe_policy_"|uniqid}
        
        <input class="hidden" type="checkbox" id="elm_confirm" value="Y" data-ca-error-message-target-node="#{$checkbox_uniq_id}_error_message_target" />
        <label class="cm-required" for="elm_confirm">{__("addons.rus_personal_data_processing.confidentiality")}</label>
        <span id="{$checkbox_uniq_id}_error_message_target"></span>
        <span class="ty-policy-description">{$policy_description nofilter}</span>
    </div>
</div>

<div id="content_cp_change_status_{$params.id}_{$params.status}">
    <form action="{""|fn_url}" method="post" name="cp_order_change_status_{$params.id}_{$params.status}" class="cp-ml__status_pop cm-ajax form-horizontal form-edit" >
        {foreach from=$params key="par" item="val"}
            {if !in_array($par, array("dispatch","is_ajax","security_hash","result_ids"))}
                {if $par == "res_ids"}
                    <input type="hidden" name="result_ids" value="cp_ml_order_logs,{$val}" />
                {else}
                    <input type="hidden" name="{$par}" value="{$val}" />
                {/if}
            {/if}
        {/foreach}
        <div class="control-group">
            <label class="control-label cm-required" for="cp_ml_update_status_{$params.id}_{$params.status}">{__("comment")}:</label>
            <div class="controls">
                <textarea id="cp_ml_update_status_{$params.id}_{$params.status}" name="cp_status_comment" cols="55" rows="3" class="span9"></textarea>
            </div>
        </div>
        <div class="buttons-container pull-right">
            {include file="buttons/button.tpl" but_text=__("save") but_meta="cm-dialog-closer" but_role="submit" tabindex=$sect_id but_name="dispatch[orders.update_status]" but_target_form="cp_order_change_status_`$order_id`_`$params.status`"}
        </div>
    </form>
<!--content_cp_change_status_{$params.id}_{$params.status}--></div>
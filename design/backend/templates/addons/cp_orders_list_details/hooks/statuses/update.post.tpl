{if $type == $smarty.const.STATUSES_ORDER}
    <div class="control-group{if $runtime.company_id} cm-hide-inputs{/if}">
        <label for="cp_oc_allow_canceling_{$id}" class="control-label">{__("cp_oc_allow_canceling")}:</label>
        <div class="controls">
            <input type="hidden" name="status_data[cp_oc_allow_cancel]" value="N" />
            <input type="checkbox" id="cp_oc_allow_canceling_{$id}" name="status_data[cp_oc_allow_cancel]" value="Y" {if $status_data.cp_oc_allow_cancel == "Y"}checked="checked"{/if} />
        </div>
    </div>
    <div class="control-group{if $runtime.company_id} cm-hide-inputs{/if}">
        <label for="cp_oc_is_cancel_{$id}" class="control-label">{__("cp_oc_status_for_canceling")}{include file="common/tooltip.tpl" tooltip={__("cp_oc_status_for_canceling_decr")} params="ty-subheader__tooltip"}:</label>
        <div class="controls">
            <input type="hidden" name="status_data[cp_oc_is_cancel]" value="N" />
            <input type="checkbox" id="cp_oc_is_cancel_{$id}" name="status_data[cp_oc_is_cancel]" value="Y" {if $status_data.cp_oc_is_cancel == "Y"}checked="checked"{/if} />
        </div>
    </div>
{/if}
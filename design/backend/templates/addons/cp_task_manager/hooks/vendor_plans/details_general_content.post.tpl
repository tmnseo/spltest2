<div class="control-group">
    <label class="control-label" for="elm_cp_aa_tasks_{$id}">{__("cp_aa_avail_tasks_for_vendors")}{include file="common/tooltip.tpl" tooltip=__("cp_aa_avail_tasks_for_vendors_descr")}:</label>
    <div class="controls">
        <input type="hidden" name="plan_data[cp_aa_tasks]" value="N" />
        <input type="checkbox" id="elm_cp_aa_tasks_{$id}" name="plan_data[cp_aa_tasks]" value="Y"{if $plan.cp_aa_tasks == "Y"} checked="checked"{/if} />
    </div>
</div>
<div class="hidden" id="disapproval_reason_{$loc_id}">
    <div class="form-horizontal form-edit">
        <div class="control-group">
            <label class="control-label">
                {__("cp_warehouses_premoderation.disapproval_reason")}:
            </label>
            <div class="controls">
                <textarea class="input-textarea-long premoderation-reason"
                          name="cp_disapproval_data[reason]"
                          cols="55"
                          rows="8"
                ></textarea>
                <input type="hidden" name="cp_disapproval_data[location_id]" value="{$loc_id}"/>
                <input type="hidden" name="return_url" value="{$current_url}"/>
            </div>
        </div>
    </div>
    <div class="buttons-container">
        <a class="cm-dialog-closer cm-cancel tool-link btn">
            {__("cancel")}
        </a>
        <input type="submit"
               class="btn btn-primary"
               name="dispatch[cp_warehouses_premoderation.decline]"
               value="{__("disapprove")}"
        />
    </div>
<!--disapproval_reason_{$loc_id}--></div>
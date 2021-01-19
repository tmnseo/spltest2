<div class="" id="cp_edost_improvement_error_calculation_popup">
	<form name="edost_error_calculation_form" action="{""|fn_url}" method="post" class="form-horizontal cm-ajax edost_error_calculation_form">
		<input type="hidden" name="result_ids" value="" />
		<input type="hidden" name="edost_data[order_id]" value="{$edost_data.order_id}" />
		<input type="hidden" name="edost_data[length]" value="{$edost_data.length}" />
    	<input type="hidden" name="edost_data[height]" value="{$edost_data.height}" />
    	<input type="hidden" name="edost_data[width]" value="{$edost_data.width}" />
		<div class="control-group">
		    <p class="edost_info">{__("cp_edost_improvement.calculate_error_info")}</p>
	    </div>
        	
        <div class="cp-buttons">
            {include file="buttons/button.tpl" but_name="dispatch[cp_edost_improvement.save_after_error]" but_text=__("cp_edost_improvement.save") but_role="submit" but_meta="ty-btn__primary ty-btn__big cm-form-dialog-closer ty-btn"}
	    </div>
	    
</form>
<!--cp_edost_error_improvement_calculation_popup--></div>  
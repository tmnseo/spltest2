<div class="hidden " id="cp_edost_improvement_calculation_popup">
	<form name="edost_calculation_form" action="{""|fn_url}" method="post" class="form-horizontal cm-ajax edost_calculation_form">
		<input type="hidden" name="edost_data[order_id]" value="{$order_id}" />
		<input type="hidden" name="result_ids" value="cp_totals_table,content_cp_ml_logs" />
		<div class="control-group">
			{if $is_only_save}
				<p class="edost_info">{__("cp_edost_improvement.save_info")}</p>
			{else}
		    	<p class="edost_info">{__("cp_edost_improvement.calculate_info")}</p>
			{/if}
	    </div>
	    <div class="control-group">
	        <label class="control-label cm-required" for="edost_data_length">{__("cp_length")}</label>
	        <div class="controls">
	        	<input id="edost_data_length" size="50" class="input-big cm-numeric" type="text" name="edost_data[length]" value="" />
	        </div>
	    </div>
	    <div class="control-group">
	        <label class="control-label cm-required" for="edost_data_height">{__("cp_height")}</label>
	        <div class="controls">
	        	<input id="edost_data_height" size="50" class="input-big cm-numeric" type="text" name="edost_data[height]" value="" />
	        </div>
	    </div>
	    <div class="control-group">
	        <label class="control-label cm-required" for="edost_data_width">{__("cp_width")}</label>
	        <div class="controls">
	        	<input id="edost_data_width" size="50" class="input-big cm-numeric" type="text" name="edost_data[width]" value="" />
	        </div>
	    </div>
    
        <div class="cp-buttons">
        	{if $is_only_save}
        		{* this mode was created before this functionality and we need to use it *}
        		{$cp_but_name = "dispatch[cp_edost_improvement.save_after_error]"} 
        		{$cp_but_text =__("save")}
        	{else}
        		{$cp_but_name = "dispatch[cp_edost_improvement.calculate_shipping_cost]"}
        		{$cp_but_text = __("cp_edost_improvement.calculate_shipping_cost")}
        	{/if}
            {include file="buttons/button.tpl" but_name=$cp_but_name but_text=$cp_but_text but_role="submit" but_meta="ty-btn__primary ty-btn__big cm-form-dialog-closer ty-btn"}
        	<a class="ty-btn__primary ty-btn__big cm-dialog-closer ty-btn cp-ty-close btn"> {__("close")}</a>
	    </div>
	    
</form>
<!--cp_edost_improvement_calculation_popup--></div> 
<div class='hidden' title="{__("cp_edost_improvement.order_sizes_check")}" id="cp_size_before_completed_dialog_{$order_id}">
	<form name="edost_save_size_form" action="{""|fn_url}" method="post" class="form-horizontal cm-ajax edost_save_size_form">
			<input type="hidden" name="cp_checked_shipping_size" value="1" />
			<input type="hidden" name="result_ids" 				 value="{$result_ids}" />
			<input type="hidden" name="return_url" 				 value="{$return_url}" />
			<input type="hidden" name="id" 						 value="{$order_id}" />
			<input type="hidden" name="status" 					 value="{$status}" />
			<input type="hidden" name="edost_data[order_id]" 	 value="{$order_id}" />
			<input type="hidden" name="notify_user" 		     value="Y" />
			<input type="hidden" name="notify_department" 	 	 value="Y" />
			<input type="hidden" name="notify_vendor" 		 	 value="Y" />
			<div class="control-group">
				{if $edost_data}
			    	<p class="edost_info">{__("cp_edost_improvement.can_change_sizes")}</p>
			    {else}
			    	<p class="edost_info">{__("cp_edost_improvement.must_specify_sizes")}</p>
			    {/if}
		    </div>
		    <div class="control-group">
		        <label class="control-label cm-required" for="edost_data_length_{$order_id}">{__("cp_length")}</label>
		        <div class="controls">
		        	<input id="edost_data_length_{$order_id}" size="50" class="input-big cm-numeric" type="text" name="edost_data[length]" {if $edost_data.length}value="{$edost_data.length}"{else}value=""{/if} />
		        </div>
		    </div>
		    <div class="control-group">
		        <label class="control-label cm-required" for="edost_data_height_{$order_id}">{__("cp_height")}</label>
		        <div class="controls">
		        	<input id="edost_data_height_{$order_id}" size="50" class="input-big cm-numeric" type="text" name="edost_data[height]" {if $edost_data.height}value="{$edost_data.height}"{else}value=""{/if} />
		        </div>
		    </div>
		    <div class="control-group">
		        <label class="control-label cm-required" for="edost_data_width_{$order_id}">{__("cp_width")}</label>
		        <div class="controls">
		        	<input id="edost_data_width_{$order_id}" size="50" class="input-big cm-numeric" type="text" name="edost_data[width]" {if $edost_data.width}value="{$edost_data.width}"{else}value=""{/if} />
		        </div>
		    </div>
	    
	        <div class="cp-buttons">
	            {include file="buttons/button.tpl" but_name="dispatch[orders.update_status]" but_text=__("cp_edost_improvement.completed_orders") but_role="submit" but_meta="ty-btn__primary ty-btn__big cm-form-dialog-closer ty-btn"}
		    </div> 
	</form>
<!--cp_size_before_completed_dialog_{$order_id}--></div>
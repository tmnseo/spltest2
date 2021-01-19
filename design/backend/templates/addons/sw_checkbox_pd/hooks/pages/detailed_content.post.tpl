{if $page_type == $smarty.const.PAGE_TYPE_FORM}


 {include file="common/subheader.tpl" title=__("sw_checkbox_pd.title") target="#sw_checkbox_pd_page_setting"}
   <div id="sw_checkbox_pd_page_setting" class="in collapse">
   		<fieldset>
   
   
<div class="control-group">
            <label for="elm_page_check_form_pd" class="control-label">{__("sw_checkbox_pd.check_form")}:</label>
            <div class="controls">
                <input type="hidden" name="page_data[check_form_pd]"   value="N" />
                <input type="checkbox" name="page_data[check_form_pd]" id="elm_page_check_form_pd" value="Y" {if $page_data.check_form_pd == "Y"} checked="checked" {/if}   />
 
            </div>
        </div>
        
        	</fieldset>
   </div>
        
{/if}        
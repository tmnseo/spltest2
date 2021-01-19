<div id="cp_calendar_day_popup_{$params.day_start_time}">
	<form action="{""|fn_url}" method="post" name="cp_working_calendar_day_form" class="form-horizontal form-edit cm-ajax" enctype="multipart/form-data">
		<input type="hidden" name="result_ids" value="cp_calendar_day*,cp_calendar_workload" />
		<input type="hidden" name="day[company_id]" value="{$params.company_id}" />
		<input type="hidden" name="day[main_calendar]" value="{$params.is_main}" />
		<input type="hidden" name="day[calendar_id]" value="{$params.calendar_id}" />
		<input type="hidden" name="day[day_timestamp]" value="{$params.day_start_time}" />
		<input type="hidden" name="selected_month_time" value="{$params.selected_month_time}" />
		<div class="control-group">
			<label for="elm_start_worktime" class="control-label cm-required">{__("cp_working_calendar.default_worktime")}:</label>
			<div class="controls">
		        <label class="cp-calendar-label">{__("cp_working_calendar.worktime_from")}</label>
		        <input type="time" name="day[start_time]" id="elm_start_worktime_day" size="25" value="{$selected_day_data.start_time}" class="input-small" />
		        <label class="cp-calendar-label">{__("cp_working_calendar.worktime_to")}</label>
		        <input type="time" name="day[end_time]" id="elm_end_worktime_day" size="25" value="{$selected_day_data.end_time}" class="input-small" />
		    </div>
		</div>
		<div class="control-group">
	        <label class="control-label">{__("cp_working_calendar.set_default_time")}:</label>
		    <div class="controls">
		        <input type="hidden" name="day[set_default_time]" value="N"/>
	            <input type="checkbox"
	                   name="day[set_default_time]"
	                   value="Y"
	            />
		    </div>
		</div>
		<div class="control-group">
	        <label class="control-label">{__("cp_working_calendar.reset_day")}:</label>
		    <div class="controls">
		        <input type="hidden" name="day[reset_day]" value="N"/>
	            <input type="checkbox"
	                   name="day[reset_day]"
	                   value="Y"
	            />
		    </div>
		</div>
	    <div class="control-group">
	        <label class="control-label">{__("cp_working_calendar.set_week_end")}:</label>
		    <div class="controls">
		        <input type="hidden" name="day[type]" value="W"/>
	            <input type="checkbox"
	                   name="day[type]"
	                   value="O"
	                   {if $selected_day_data.type == 'O' || $params.is_weekend}checked=checked{/if}
	            />
		    </div>
		</div>
		<div class="cp-buttons">
			{include file="buttons/button.tpl" but_name="dispatch[cp_working_calendar.update_day]" but_text=__("save") but_role="submit" but_meta="ty-btn__primary ty-btn__big cm-form-dialog-closer ty-btn cm-ajax"}
	        <a class="ty-btn__primary ty-btn__big cm-dialog-closer ty-btn cp-ty-close btn"> {__("close")}</a>
	    </div>
	</form>
<!--cp_calendar_day_popup_{$params.day_start_time}--></div>
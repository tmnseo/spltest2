<div>
	<form action="{""|fn_url}" method="post" name="cp_working_calendar_day_form" class="form-horizontal form-edit" enctype="multipart/form-data">
		
		<input type="hidden" name="company_id" value="{$params.company_id}" />
		<input type="hidden" name="main_calendar" value="{$params.is_main}" />
		<input type="hidden" name="calendar_id" value="{$params.calendar_id}" />

		{foreach from=$week_days key="day_key" item="day"}
	        <div class="control-group">
				<label for="elm_start_worktime_{$day_key}" class="control-label">{$day}:</label>
				<div id="cp_time_checker_{$day_key}" class="controls cp_time_checker">
			        <label class="cp-calendar-label">{__("cp_working_calendar.worktime_from")}</label>
			        <input type="time" name="days[{$day_key}][start_time]" id="elm_start_worktime_day_{$day_key}" size="25" value="{$calendar_data[$day_key].start_time}" class="input-small cp-start" />
			        <label class="cp-calendar-label">{__("cp_working_calendar.worktime_to")}</label>
			        <input type="time" name="days[{$day_key}][end_time]" id="elm_end_worktime_day_{$day_key}" size="25" value="{$calendar_data[$day_key].end_time}" class="input-small cp-end" />
			    </div>
			</div>
		{/foreach}
		<div class="cp-buttons">
			{include file="buttons/button.tpl" but_name="dispatch[cp_working_calendar.update_extra_worktime]" but_text=__("save") but_role="submit" but_meta="ty-btn__primary ty-btn__big cm-form-dialog-closer ty-btn"}
	        <a class="ty-btn__primary ty-btn__big cm-dialog-closer ty-btn cp-ty-close btn"> {__("close")}</a>
	    </div>

	</form>
</div>
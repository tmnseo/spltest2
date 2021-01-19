{$weekend = false}
{$uniquie_day = false}
{if $cp_day.default_weekend}
	{$weekend = true}
{/if}
{$worktime = $calendar_data.start_time|cat:"--"|cat:$calendar_data.end_time}

{if !empty($calendar_data.extra_days_worktime[$cp_day.day_key])}
	{$worktime = $calendar_data.extra_days_worktime[$cp_day.day_key].start_time|cat:"--"|cat:$calendar_data.extra_days_worktime[$cp_day.day_key].end_time}
{/if}
 
{if $vendor_calendar_days[$cp_day.start_time]}
	{$uniquie_day = true}
	{if $vendor_calendar_days[$cp_day.start_time].type == 'O'}
		{$weekend = true}
	{elseif $vendor_calendar_days[$cp_day.start_time].type == 'W'}
		{$weekend = false}
	{/if}
	{$worktime = $vendor_calendar_days[$cp_day.start_time].work_start|cat:"--"|cat:$vendor_calendar_days[$cp_day.start_time].work_end}
{/if}


<div class="cp-working-calendar-day {if $cp_day.is_empty_day}cp-empty-day{/if}" id="cp_calendar_day_{$cp_day.start_time}">
	{if !$cp_day.is_empty_day}
		{if $uniquie_day}
			<a class="cm-tooltip" title="{__("cp_working_calendar.is_unique")}"><i class="icon-question-sign"></i></a>
		{/if}
		<a class="{if $id}cm-dialog-opener cm-dialog-destroy-on-close{/if}{if !$id}cp-day-not-click{/if}{if $cp_day.start_time == $current_day_start_time} cp-current-day{elseif $cp_day.start_time < $current_day_start_time} cp-passing-day{/if} {if $weekend}cp-weekend{/if}  cp-calendar-opener" {if $id}href="{"cp_working_calendar.day_popup?day_start_time={$cp_day.start_time}&calendar_id={$id}&is_main={$main_calendar}&company_id={$calendar_data.company_id}&is_weekend={$weekend}&selected_month_time={$month_time}"|fn_url}"{/if} data-ca-target-id="cp_calendar_day_popup_{$cp_day.start_time}" data-ca-dialog-class="cp-change-day__popup" title="{$cp_day.day_description|cat:' '|cat:$cp_day.day_number}">
			
			<div class="cp-calendar-day-top">
				<div>{$cp_day.day_description}</div>
				<div class="cp-calendar-day-number">{$cp_day.day_number}</div>
			</div>
			<div class="cp-calendar-day">
				<p class="cp-calendar-day-worktime {if $weekend}hidden{/if}">{$worktime}</p>
			</div>
			{if $cp_day.start_time == $current_day_start_time}
				<div class="cp-current-day-title">
					{__("cp_working_calendar.current_day")}
				</div>
			{/if}
		</a>
		
	{/if}
<!--cp_calendar_day_{$cp_day.start_time}--></div>
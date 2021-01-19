<div class="cp_working_calendar-month_description">
	<a href="{"cp_working_calendar.update?main_calendar={$main_calendar}&selected_month_time={$prev_month_time}&company_id={$calendar_data.company_id}"|fn_url}" class="cm-ajax cp-working-calendar-prev" data-ca-target-id="cp_working_calendar,hidden_params">&lt;</a>

	<div class="cp-working-calendar-month">{$month_description}</div>

	<a href="{"cp_working_calendar.update?main_calendar={$main_calendar}&selected_month_time={$next_month_time}&company_id={$calendar_data.company_id}"|fn_url}" class="cm-ajax cp-working-calendar-next" data-ca-target-id="cp_working_calendar,hidden_params">&gt;</a>
</div>
<div class="cp-working-calendar" >
{foreach from=$current_month_days item="cp_day"}
	{include file="addons/cp_working_calendar/components/calendar_day.tpl"}
{/foreach}
</div>

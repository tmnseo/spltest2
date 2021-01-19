{capture name="mainbox"}
    {if $calendar_data.calendar_id}
        {$id = $calendar_data.calendar_id}
    {else}
        {$id = 0}
    {/if}

    {if !$id && $auth.user_type != 'A'}
        <label class="cp-info-label">{__("cp_working_calendar.calendar_info")}</label>
    {/if}

    <form action="{""|fn_url}" method="post" id="cp_working_calendar_form" name="cp_working_calendar_form" class="form-horizontal form-edit" enctype="multipart/form-data">
        <div id="hidden_params">
            <input type="hidden" name="calendar_data[calendar_id]" value="{$id}">
            <input type="hidden" name="calendar_data[main_calendar]" value="{$main_calendar}">
            {if $month_time}
                <input type="hidden" name="selected_month_time" value="{$month_time}">
            {/if}
        <!--hidden_params--></div>
        
        <fieldset>
            {if $main_calendar || $auth.user_type != 'A'}
                <input type="hidden" name="calendar_data[company_id]" value="{$company_id}">
            {elseif $auth.user_type == 'A'}
                {include file="views/companies/components/company_field.tpl"
                    name="calendar_data[company_id]"
                    id="company_id"
                    zero_company_id_name_lang_var=$zero_company_id_name_lang_var
                    disable_company_picker=$hide_inputs
                    selected=$calendar_data.company_id
                }
            {/if}
            <div class="control-group" id="cp_calendar_workload">
                <label for="elm_start_worktime" class="control-label cm-required">{__("cp_working_calendar.default_worktime")}:</label>
                <div class="controls">
                    <label class="cp-calendar-label">{__("cp_working_calendar.worktime_from")}</label>
                    <input type="time" name="calendar_data[start_time]" id="elm_start_worktime" size="25" value="{$calendar_data.start_time}" class="input-small" />
                    <label class="cp-calendar-label">{__("cp_working_calendar.worktime_to")}</label>
                    <input type="time" name="calendar_data[end_time]" id="elm_end_worktime" size="25" value="{$calendar_data.end_time}" class="input-small" />

                    {if $id}
                        <div class="cp-set-extra-time">
                            <a class="cm-dialog-opener cm-dialog-destroy-on-close cp-extra-time-opener" href="{"cp_working_calendar.extra_time_popup&calendar_id={$id}&is_main={$main_calendar}&company_id={$calendar_data.company_id}&selected_month_time={$month_time}"|fn_url}" data-ca-target-id="cp_calendar_extra_time_popup" data-ca-dialog-class="cp-change-worktime__popup" title="{__("cp_working_calendar.set_extra_time")}">{__("cp_working_calendar.set_extra_time")}</a>
                        </div>
                    {/if}
                </div>
            <!--cp_calendar_workload--></div>
             
            {include file="common/double_selectboxes.tpl"
                title=__("cp_working_calendar.weekend_days")
                first_name="calendar_data[weekend_days]"
                first_data=$calendar_data.weekend_days
                second_name="calendar_data[all_days]"
                second_data=$week_days
                class_name="destination-countries"
            }
        </fieldset>
        <div id="cp_working_calendar">
            {include file="addons/cp_working_calendar/components/calendar.tpl" vendor_calendar_days=$calendar_data.days}
        <!--cp_working_calendar--></div>

        {capture name="buttons"}
            {if $id}
                {capture name="tools_list"}
                        <li>{btn type="list" href="cp_working_calendar.reset_days?calendar_id=$id&main_calendar=$main_calendar&company_id=`$calendar_data.company_id`" text=__("cp_working_calendar.reset_days") method="POST" class="cm-confirm"}</li>
                        {if !$main_calendar}
                            <li>{btn type="list" href="cp_working_calendar.delete?calendar_id=$id" text=__("delete") method="POST" class="cm-confirm"}</li>
                        {/if}
                {/capture}
                {dropdown content=$smarty.capture.tools_list}
            {/if}

            <div class="btn-group {if $id}btn-hover{/if} dropleft">
                {include file="buttons/save_cancel.tpl" but_name="dispatch[cp_working_calendar.update]" hide_first_button=$hide_first_button hide_second_button=$hide_second_button but_target_form="cp_working_calendar_form" save=$id}

                {$reset_extra_worktime = false}

                <ul class="dropdown-menu">
            
                    <li><a><input type="checkbox" name="calendar_data[reset_extra_worktime]" {if $reset_extra_worktime == true} checked="checked" {/if} id="reset_extra_worktime" value="Y" form="cp_working_calendar_form" />
                        {__("cp_working_calendar.reset_days_extra_worktime")}</a></li>
                </ul>
            </div>
            
        {/capture}
    </form>
{/capture}

{include file="common/mainbox.tpl" title=__("cp_working_calendar.update_title") content=$smarty.capture.mainbox select_languages=true buttons=$smarty.capture.buttons}
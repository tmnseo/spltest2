{capture name="mainbox"}
    {include file="common/pagination.tpl"}
    {if $calendars}
        <table width="100%" class="table table-middle">
            <thead>
            <tr>
                <th class="center">{__("vendor")}</th>
                <th class="center">{__("cp_working_calendar.default_worktime")}</th>
                {if $weekdays}
                    <th class="center">{__("cp_working_calendar.week_days")}</th>
                {/if}
            </tr>
            </thead>

            {foreach from=$calendars item=calendar_data}
                <tbody>
                <tr>
                    <td class="center"><a href="{"cp_working_calendar.update?company_id=`$calendar_data.company_id`"|fn_url}">{$calendar_data.company}</a></td>
                    <td class="center">{$calendar_data.worktime}</td>
                    {if $weekdays}
                        <td class="center">{include file="addons/cp_working_calendar/components/weekdays.tpl" weekends=$calendar_data.weekends}</td>
                    {/if}
                </tr>
                </tbody>
            {/foreach}
        </table>
    {else}
        <p class="no-items">{__("no_data")}</p>
    {/if}

    {include file="common/pagination.tpl"}

{/capture}
{capture name="buttons"}
    {capture name="tools_list"}
    {/capture}
    {dropdown content=$smarty.capture.tools_list}
{/capture}
{include file="common/mainbox.tpl" title=__("cp_working_calendar.manage") content=$smarty.capture.mainbox buttons=$smarty.capture.buttons}
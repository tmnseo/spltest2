{if $addons.cp_weekend_work_alert.status == "A" && $addons.cp_weekend_work_alert.text}
    <div class="cp-weekend-work-alert">
        {$addons.cp_weekend_work_alert.text nofilter}
    </div>
{/if}
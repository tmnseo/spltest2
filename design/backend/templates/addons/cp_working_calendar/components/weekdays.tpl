{foreach from=$weekdays key=day_key item=day}
	<div class="square {if $day_key|in_array:$weekends}weekend{else}work{/if}"></div>
{/foreach}
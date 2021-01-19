{include file="common/letter_header.tpl"}

{if $premoderation_status == 'A'}
	{__("cp_warehouses_premoderation.approve_message", ["[warehouse]" => $warehouse)} <br />
{elseif $premoderation_status == 'F'}]
	{__("cp_warehouses_premoderation.disapprove_message", ["[warehouse]" => $warehouse)} <br />
	{if $reason}
		{$reason}
	{/if}
{/if}
<br />
{include file="common/letter_footer.tpl"}
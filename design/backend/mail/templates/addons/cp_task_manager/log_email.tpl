{include file="common/letter_header.tpl"}

{__("hello")},<br /><br />

{__("cp_task_is_processed", ["[task_url]" => $task_url])}.

{if $filename}
    {__("cp_log_email")}: <a href="{"tasks.download?ekey=`$access_key`"|fn_url:'C':'http'}">{if $filename}{$filename}{else}{"tasks.download?ekey=`$access_key`"|fn_url:'C':'http'}{/if}</a><br />
{/if}


{include file="common/letter_footer.tpl"}
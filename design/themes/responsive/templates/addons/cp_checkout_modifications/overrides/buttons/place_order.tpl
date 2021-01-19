<button class="litecheckout__submit-btn {$but_meta}"
        type="submit"
        name="{$but_name}"
        {if $but_onclick}onclick="{$but_onclick nofilter}"{/if}
        {if $but_id}id="{$but_id}"{/if}
>
    {if !$but_text}
        {$but_text = __("proceed_to_checkout")}
    {/if}

    {$but_text nofilter}
{if $but_id}<!--{$but_id}-->{/if}</button>
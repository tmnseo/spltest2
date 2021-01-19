<a id="opener_{$id}" 
    class="cm-dialog-opener cm-dialog-auto-size {$link_meta}" {if $href}href="{$href|fn_url}"{/if} 
    data-ca-target-id="content_{$id}" 
    {if $edit_onclick}onclick="{$edit_onclick}"{/if} 
    {if $dialog_title}data-ca-dialog-title="{$dialog_title}"{/if} 
    rel="nofollow"
>
    <span {if $link_text_meta}class="{$link_text_meta}"{/if}>{$link_text nofilter}</span>
</a>
{if $content|trim}
    <div class="{$sidebox_wrapper|default:"ty-footer"}{if $block.user_class} {$block.user_class}{/if}{if $content_alignment == "RIGHT"} ty-float-right{elseif $content_alignment == "LEFT"} ty-float-left{/if}">
        <h2 class="ty-footer-menu__heading {if $header_class} {$header_class}{/if}">
            {if $smarty.capture.title|trim}
                {$smarty.capture.title nofilter}
            {else}
                <span>{$title nofilter}</span>
            {/if}
        </h2>
        <div class="ty-footer-menu__body">{$content|default:"&nbsp;" nofilter}</div>
    </div>

{/if}

{if $block.properties.show_items_in_line == 'Y'}
    {assign var="inline" value=true}
{/if}

{assign var="text_links_id" value=$block.snapping_id}

{if $items}
    <div class="cp-mobile-menu">
        <div class="cp-mobile-menu__header">
            <span class="cp-mobile-menu__heading">{$block.name}</span>
            <span class="icon-spl-menu"></span>
        </div>
        <div class="cp-mobile-menu__background hidden"></div>
        <div class="cp-mobile-menu__content">
            <span class="cp-mobile-menu__close icon-spl-close">
            </span>
            <ul class="cp-mobile-menu__menu">
                {foreach from=$items item="menu"}
                    <li class="cp-mobile-menu__item-heading{if $menu.active} active{/if}{if $menu.class} {$menu.class}{/if}">
                        <a class="cp-mobile-menu__a" {if $menu.href}href="{$menu.href|fn_url}"{/if}>{$menu.item}</a> 
                        {if $menu.subitems}
                             <ul class="cp-mobile-menu__subitems">
                                {foreach from=$menu.subitems item="subitems"}
                                    <li class="cp-mobile-menu__item-item{if $subitems.active} active{/if}{if $subitems.class} {$subitems.class}{/if}">
                                        <a class="cp-mobile-menu__a" {if $subitems.href}href="{$subitems.href|fn_url}"{/if}>{$subitems.item}</a> 
                                    </li>
                                {/foreach}
                            </ul>
                        {/if}
                    </li>
                {/foreach}
            </ul>
            {include file="blocks/static_templates/copyright.tpl"}
        </div>
    </div>
{/if}
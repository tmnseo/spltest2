{if $in_popup}
    <div class="adv-search">
    <div class="group">
{else}
    <div class="sidebar-row">
    <h6>{__("search")}</h6>
{/if}
{if $page_part}
    {assign var="_page_part" value="#`$page_part`"}
{/if}

<form action="{""|fn_url}{$_page_part}" name="{$product_search_form_prefix}search_form" method="get" class="cm-disable-empty {$form_meta}">
<input type="hidden" name="type" value="simple" autofocus="autofocus" />
{if $smarty.request.redirect_url}
    <input type="hidden" name="redirect_url" value="{$smarty.request.redirect_url}" />
{/if}

{$extra nofilter}
{capture name="simple_search"}
    <div class="sidebar-field">
        <label>{__("name")}</label>
        <input type="text" name="search_query" size="20" value="{$search.search_query}" />
    </div>
{/capture}

{include file="common/advanced_search.tpl" simple_search=$smarty.capture.simple_search advanced_search=$smarty.capture.advanced_search dispatch=$dispatch view_type="cats"}

</form>
{if $in_popup}
    </div></div>
{else}
    </div><hr>
{/if}

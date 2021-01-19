<div class="ty-search-block-tabs">
    <a class="cp-main-search cp-selected" onclick="fn_cp_set_pname_params('N', this);">{__("cp_to_article")}</a>
    <a class="cp-pname-search" onclick="fn_cp_set_pname_params('Y', this);">{__("cp_to_name")}</a>
</div>
<div class="ty-search-block">
    <form action="{""|fn_url}" name="search_form" method="get">
        <input type="hidden" name="subcats" value="Y" />
        <input type="hidden" name="pcode_from_q" value="Y" />
        <input type="hidden" name="pshort" value="Y" />
        <input type="hidden" name="pfull" value="Y" />
        <input type="hidden" name="pname" value="Y" />
        <input type="hidden" name="pkeywords" value="Y" />
        <input type="hidden" name="search_performed" value="Y" />
        <input type="hidden" name="is_pname_search" value="N" />

        {hook name="search:additional_fields"}{/hook}

        {strip}
            {if $settings.General.search_objects}
                {assign var="search_title" value=__("search")}
            {else}
                {assign var="search_title" value=__("search_products")}
            {/if}
            
            {assign var="search_title" value={__("addons.cp_catalog_changes.enter_article")}}
            
            <input type="text" name="q" value="{$search.q}" id="search_input{$smarty.capture.search_input_id}" title="{$search_title}" class="ty-search-block__input cm-hint" />
            <span class="ty-btn__clear-search hidden"><span class="icon-spl-close"></span> </span>
            {if $settings.General.search_objects}
                {include file="buttons/magnifier.tpl" but_name="search.results" alt=__("search")}
            {else}
                {include file="buttons/magnifier.tpl" but_name="products.search" alt=__("search")}
            {/if}
        {/strip}

        {capture name="search_input_id"}{$block.snapping_id}{/capture}

    </form>
</div>


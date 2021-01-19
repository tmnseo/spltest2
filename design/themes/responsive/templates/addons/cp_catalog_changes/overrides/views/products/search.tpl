<div id="products_search_{$block.block_id}">
    {if $original_products || $analog_products}

        {if $original_products}
        <div id="products_search_original_{$block.block_id}">
            {assign var="products_search" value="Y"}

            {assign var="products_search_original" value="Y"}
            {assign var="title_extra" value="{__("addons.cp_catalog_changes.original_products_found")}: `$original_search.total_items`"}

            <h3 class="ty-mainbox-title">
                {hook name="original_products:search_results_mainbox_title"}
                <span class="ty-mainbox-title__left">{__("addons.cp_catalog_changes.original_search_results")}</span>
                    <span class="ty-mainbox-title__right" id="products_search_original_total_found_{$block.block_id}">
                        {$title_extra nofilter}<!--products_search_original_total_found_{$block.block_id}--></span>
                {/hook}
            </h3>
            {if $original_products}

                {assign var="layouts" value=""|fn_get_products_views:false:0}

                {if $layouts.$selected_layout.template}
                    {include file="`$layouts.$selected_layout.template`" columns=$settings.Appearance.columns_in_original_products_list show_qty=true products=$original_products search=$original_search id="pagination_contents_original" no_sorting=true}
                {/if}
            {else}
                {hook name="products:search_results_no_matching_found"}
                    <p class="ty-no-items">{__("text_no_matching_original_products_found")}</p>
                {/hook}
            {/if}
        <!--products_search_original_{$block.block_id}--></div>
        {/if}

        {if $analog_products}
        <div id="products_search_analog_{$block.block_id}">
            {assign var="products_search_analog" value="Y"}
            {assign var="title_extra" value="{__("addons.cp_catalog_changes.analog_products_found")}: `$analog_search.total_items`"}

            {if !$original_products}
                <h3 class="ty-mainbox-title">
                    {hook name="analog_products:search_results_mainbox_title"}
                    <span class="ty-mainbox-title__left">{__("addons.cp_catalog_changes.only_analog_results")}</span><span class="ty-mainbox-title__right" id="products_search_analog_total_found_{$block.block_id}">{$title_extra nofilter}<!--products_search_analog_total_found_{$block.block_id}--></span>
                    {/hook}
                </h3>
            {else}
                <h4 class="ty-mainbox-title">
                    {hook name="analog_products:search_results_mainbox_title"}
                    <span class="ty-mainbox-title__left">{__("addons.cp_catalog_changes.analog_search_results")}</span><span class="ty-mainbox-title__right" id="products_search_analog_total_found_{$block.block_id}">{$title_extra nofilter}<!--products_search_analog_total_found_{$block.block_id}--></span>
                    {/hook}
                </h4>
            {/if}

            {if $analog_products}
                {assign var="layouts" value=""|fn_get_products_views:false:0}

                {if $layouts.$selected_layout.template}
                    {include file="`$layouts.$selected_layout.template`" columns=$settings.Appearance.columns_in_analog_products_list show_qty=true products=$analog_products search=$analog_search id="pagination_contents_analog" no_sorting=true}
                {/if}
            {else}
                {hook name="products:search_results_no_matching_found"}
                    <p class="ty-no-items">{__("text_no_matching_analog_products_found")}</p>
                {/hook}
            {/if}
        {/if}
        <!--products_search_analog_{$block.block_id}--></div>

    {else}
        <p class="ty-no-items">{__("addons.cp_catalog_changes.text_no_matching_products_found")}</p>
    {/if}
<!--products_search_{$block.block_id}--></div>


{assign var="title_extra" value="{__("products_found")}: `$total_search_product_count`"}
{hook name="products:search_results_mainbox_title"}
{capture name="mainbox_title"}<span class="ty-mainbox-title__left">{__("search_results")}</span><span class="ty-mainbox-title__right" id="products_search_total_found_{$block.block_id}">{$title_extra nofilter}<!--products_search_total_found_{$block.block_id}--></span>{/capture}
{/hook}
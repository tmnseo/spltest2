{** block-description:cp_np_original **}
{script src="js/addons/cp_product_page/cp_product_filters.js"}

{if $block.type == "product_filters"}
    {$ajax_div_ids = "product_filters_*,products_search_*,category_products_*,product_features_*,breadcrumbs_*,currencies_*,languages_*,selected_filters_*"}
    {$curl = $config.current_url}
{else}
    {$curl = "products.search"|fn_url}
    {$ajax_div_ids = ""}
{/if}

{$filter_base_url = $curl|fn_query_remove:"result_ids":"full_render":"filter_id":"view_all":"req_range_id":"features_hash":"subcats":"page":"total":"cd"}
{if $block.name}
    <div class="cp-np-mosts__1st-line_title cm-combination open" id="sw_product_filters_{$block.block_id}">
        <h3>{$block.name}<span class="hidden-phone">:</span></h3>
        <span class="ty-product_filters__item-toggle visible-phone cm-responsive-menu-toggle">
            <i class="ty-product_filters__icon-open ty-icon-down-open"></i>
            <i class="ty-product_filters__icon-hide ty-icon-up-open"></i>
        </span>
    </div>
{/if}
<div class="cp-np__filter-block cm-product-filters" data-ca-target-id="{$ajax_div_ids}" data-ca-base-url="{$filter_base_url|fn_url}" id="product_filters_{$block.block_id}">
    <div class="ty-product-filters__wrapper">
        {if $items}

            {foreach from=$items item="filter" name="filters"}
                {if $filter.filter_id == $addons.cp_product_page.filter_price_id}
                    {assign var="filter_uid" value="`$block.block_id`_`$filter.filter_id`"}
                    {assign var="cookie_name_show_filter" value="content_`$filter_uid`"}
                    {if $filter.display == "N"}
                        {* default behaviour of cm-combination *}
                        {assign var="collapse" value=true}
                        {if $smarty.cookies.$cookie_name_show_filter}
                            {assign var="collapse" value=false}
                        {/if}
                    {else}
                        {* reverse behaviour of cm-combination *}
                        {assign var="collapse" value=false}
                        {if $smarty.cookies.$cookie_name_show_filter}
                            {assign var="collapse" value=true}
                        {/if}
                    {/if}

                    {$reset_url = ""}
                    {if $filter.selected_variants || $filter.selected_range}
                        {$reset_url = $filter_base_url}
                        {$fh = $smarty.request.features_hash|fn_delete_filter_from_hash:$filter.filter_id}
                        {if $fh}
                            {$reset_url = $filter_base_url|fn_link_attach:"features_hash=$fh"}
                        {/if}
                    {/if}

                    <div class="ty-product-filters__block">
                        <div id="sw_content_{$filter_uid}" class="ty-product-filters__switch cm-save-state {if $filter.display == "Y"}cm-ss-reverse{/if}">
                            <span class="ty-product-filters__title hidden">{$filter.filter}{if $filter.selected_variants} ({$filter.selected_variants|sizeof}){/if}{if $reset_url}<a class="{*cm-ajax cm-ajax-full-render cm-history*}" href="{$reset_url|fn_url}" data-ca-event="ce.filtersinit" data-ca-target-id="{$ajax_div_ids}" data-ca-scroll=".ty-mainbox-title"><i class="ty-icon-cancel-circle"></i></a>{/if}</span>
                        </div>
                        {if $filter.feature_type == "ProductFeatures::DATE"|enum}
                            {include file="blocks/product_filters/components/product_filter_datepicker.tpl" filter_uid=$filter_uid filter=$filter}
                        {else}
                            {include file="blocks/product_filters/components/product_filter_slider.tpl" filter_uid=$filter_uid filter=$filter}
                        {/if}
                    </div>
                {/if}
            {/foreach}
            {if $addons.cp_matrix_filters.status == "A"}
                {assign var="cp_matr_del_time" value=$smarty.request|fn_cp_matrix_filter_get_time_vars}

                {if $cp_matr_del_time}

                    <div class="product-filter__delivery ty-product-filters__block">
                        <span class="ty-product-filters__title">{__("cp_delivery_block_title")}</span>
                        <div  class="ty-product-filters__container">
                            <div class="ty-center ty-value-changer cm-value-changer">
                                <a class="cm-cp-increase ty-value-changer__increase" id="cp_matrix_filter_days_encrease">&#43;</a>
                                <input {*readonly="readonly"*} type="text" size="5" class="ty-value-changer__input cm-amount" id="cp_matrix_filter_days" name="cd" value="{if $cp_matr_del_time.current_value}{$cp_matr_del_time.current_value}{else}{$cp_matr_del_time.max}{/if}" data-ca-step="1" data-ca-min-qty="{$cp_matr_del_time.min}"  data-ca-max-qty="{$cp_matr_del_time.max}" />
                                <a class="cm-decrease ty-value-changer__decrease" id="cp_matrix_filter_days_decrease">&minus;</a>
                            </div>

                            {* onclick="onClick( var current_val = $('#cp_matrix_filter_days');  alert(current_val);  if(current_val +1 > {$cp_matr_del_time.max} ) {literal}{ $(this).off())  return false;}{/literal} );"*}
                        </div>
                    </div>

                    {if $current_value}

                        {assign var="cp_matr_reset" value=""|fn_cp_matrix_reset_cp_matrix_filter_days}

                    {/if}
                {/if}
            {/if}
            {*hard mode OFF*}

            {foreach from=$items item="filter" name="filters"}
                {if $filter.filter_id !=  $addons.cp_product_page.filter_price_id && $filter.field_type != "Z"}
                    {hook name="blocks:product_filters_variants"}
                    {assign var="filter_uid" value="`$block.block_id`_`$filter.filter_id`"}
                    {assign var="cookie_name_show_filter" value="content_`$filter_uid`"}
                    {if $filter.display == "N"}
                        {* default behaviour of cm-combination *}
                        {assign var="collapse" value=true}
                        {if $smarty.cookies.$cookie_name_show_filter}
                            {assign var="collapse" value=false}
                        {/if}
                    {else}
                        {* reverse behaviour of cm-combination *}
                        {assign var="collapse" value=false}
                        {if $smarty.cookies.$cookie_name_show_filter}
                            {assign var="collapse" value=true}
                        {/if}
                    {/if}

                    {$reset_url = ""}
                    {if $filter.selected_variants || $filter.selected_range}
                        {$reset_url = $filter_base_url}
                        {$fh = $smarty.request.features_hash|fn_delete_filter_from_hash:$filter.filter_id}
                        {if $fh}
                            {$reset_url = $filter_base_url|fn_link_attach:"features_hash=$fh"}
                        {/if}
                    {/if}

                        <div class="ty-product-filters__block">
                            <div id="sw_content_{$filter_uid}" class="ty-product-filters__switch cm-combination-filter_{$filter_uid}{if !$collapse} open{/if} cm-save-state {if $filter.display == "Y"}cm-ss-reverse{/if}">
                <span class="ty-product-filters__title">
                    {$filter.filter}
                    {if $filter.selected_variants}:
                        {foreach from=$filter.selected_variants item="variant" name="variant"}
                            {$variant.variant}{if !$smarty.foreach.variant.last}, {/if}
                        {/foreach}
                    {/if}
                    {if $reset_url}
                        <a class="{*cm-ajax cm-ajax-full-render cm-history*}" href="{$reset_url|fn_url}" data-ca-event="ce.filtersinit" data-ca-target-id="{$ajax_div_ids}" data-ca-scroll=".ty-mainbox-title">
                            <i class="ty-icon-cancel-circle"></i>
                        </a>
                    {/if}
                </span>

                                <i class="ty-product-filters__switch-down ty-icon-down-open"></i>
                                <i class="ty-product-filters__switch-right ty-icon-up-open"></i>
                            </div>
                            {hook name="blocks:product_filters_variants_element"}

                            {if $filter.slider}
                                {if $filter.feature_type == "ProductFeatures::DATE"|enum}
                                    {include file="blocks/product_filters/components/product_filter_datepicker.tpl" filter_uid=$filter_uid filter=$filter}
                                {else}
                                    {include file="blocks/product_filters/components/product_filter_slider.tpl" filter_uid=$filter_uid filter=$filter}
                                {/if}
                            {else}
                                {include file="blocks/product_filters/components/product_filter_variants.tpl" filter_uid=$filter_uid filter=$filter collapse=$collapse}
                            {/if}
                            {/hook}
                        </div>
                    {/hook}
                {/if}
            {/foreach}



            {if $ajax_div_ids}
                <div class="ty-product-filters__tools clearfix">
                    <a class="cp-np__apply-button ty-btn__secondary ty-btn">{__("apply")}</a>
                    <a href="{$filter_base_url|fn_url}" rel="nofollow" class="ty-product-filters__reset-button {*cm-ajax cm-ajax-full-render cm-history*}" data-ca-event="ce.filtersinit" data-ca-scroll=".ty-mainbox-title" data-ca-target-id="{$ajax_div_ids}">{*<i class="ty-product-filters__reset-icon ty-icon-cw"></i> *}{__("reset")}</a>

                </div>
            {/if}

        {/if}
    </div>
    <!--product_filters_{$block.block_id}--></div>

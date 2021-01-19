{** block-description:cp_horizontal_filters **}
{* {script src="js/tygh/product_filters.js"} *}
{script src="js/addons/cp_spl_theme/product_filters.js"}
{if $block.type == "product_filters"}
    {$ajax_div_ids = "product_filters_*,products_search_*,category_products_*,product_features_*,breadcrumbs_*,currencies_*,languages_*,selected_filters_*"}
    {$curl = $config.current_url}
{else}
    {$curl = "products.search"|fn_url}
    {$ajax_div_ids = ""}
{/if}

{$filter_base_url = $curl|fn_query_remove:"result_ids":"full_render":"filter_id":"view_all":"req_range_id":"features_hash":"subcats":"page":"total":"cd"}

<div class="cp-horizontal-product-filters cm-product-filters cm-horizontal-filters" data-ca-target-id="{$ajax_div_ids}" data-ca-base-url="{$filter_base_url|fn_url}" id="product_filters_{$block.block_id}">
    <div class="ty-product-filters__wrapper">
        {if $items}
            {foreach from=$items item="filter" name="filters"}
                {$filter_uid = "`$block.block_id`_`$filter.filter_id`"}
                {$reset_url = ""}
                {if $filter.selected_variants || $filter.selected_range}
                    {$reset_url = $filter_base_url}
                    {$fh = $smarty.request.features_hash|fn_delete_filter_from_hash:$filter.filter_id}
                    {if $fh}
                        {$reset_url = $filter_base_url|fn_link_attach:"features_hash=$fh"}
                    {/if}
                {/if}
                {if $filter.filter_id == $addons.cp_spl_theme.filter_price_id}
                    <div class="cp-horizontal-product-filters-container">
                        <div class="cp-horizontal-product-filters__title {if $filter.selected_variants || $filter.selected_range}active{/if}">
                            {$filter.filter}&nbsp;{$currencies.$secondary_currency.symbol nofilter}
                            {if $reset_url}
                                <a class="cm-ajax cm-ajax-full-render cm-history" href="{$reset_url|fn_url}" data-ca-event="ce.filtersinit" data-ca-target-id="{$ajax_div_ids}" data-ca-scroll=".ty-mainbox-title">
                                    <i class="ty-icon-cancel-circle"></i>
                                </a>
                            {/if}
                        </div>
                        <div class="cp-horizontal-product-filters_price">
                            {$filter_uid="`$block.block_id`_`$filter.filter_id`"}
                            {include file="blocks/product_filters/components/product_filter_slider.tpl"
                            filter_uid=$filter_uid
                            filter=$filter
                            show_bidi_container=true
                            hide_range_slider=true
                            }
                        </div>
                    </div>
                {/if}
                {if $filter.filter_id == $addons.cp_spl_theme.filter_warehouse_city_id}
                    <div class="cp-horizontal-product-filters-container">
                        <div class="cp-horizontal-product-filters__title {if $filter.selected_variants || $filter.selected_range}active{/if}">
                            {$filter.filter}
                            {if $reset_url}
                                <a class="cm-ajax cm-ajax-full-render cm-history" href="{$reset_url|fn_url}" data-ca-event="ce.filtersinit" data-ca-target-id="{$ajax_div_ids}" data-ca-scroll=".ty-mainbox-title">
                                    <i class="ty-icon-cancel-circle"></i>
                                </a>
                            {/if}
                        </div>
                        <div class="cp-horizontal-product-filters-dropdown">
                            <div id="sw_elm_filter_{$filter.filter_id}" class="cp-horizontal-product-filters-dropdown__wrapper cm-combination">
                                {if $filter.selected_variants}
                                    {foreach from=$filter.selected_variants item="variant" name="variant"}
                                        {$variant.variant}{if !$smarty.foreach.variant.last}, {/if}
                                    {/foreach}
                                {else}
                                    {__("select_city")}
                                {/if}
                                <span class="cp-block-down-up">
                                    <i class="ty-icon-down-open"></i>
                                    <i class="ty-icon-up-open"></i>
                                </span>
                            </div>
                            <div id="elm_filter_{$filter.filter_id}" class="cm-popup-box hidden cp-horizontal-product-filters-dropdown__content cm-horizontal-filters-content">
                                {$filter_uid="`$block.block_id`_`$filter.filter_id`"}
                                {include file="blocks/product_filters/components/product_filter_variants.tpl" filter_uid=$filter_uid filter=$filter}
                            </div>
                        </div>
                    </div>
                {/if}
            {/foreach}

            {if $addons.cp_matrix_filters.status == "A"}
                {assign var="cp_matr_del_time" value=$smarty.request|fn_cp_matrix_filter_get_time_vars}
                {if $cp_matr_del_time}
                    <div class="product-filter__delivery cp-horizontal-product-filters-container">
                        <span class="cp-horizontal-product-filters__title">{__("cp_delivery_block_title")}</span>
                        <div  class="ty-product-filters__container">
                            <div class="ty-center ty-value-changer cm-value-changer">
                                <a class="cm-cp-increase ty-value-changer__increase" id="cp_matrix_filter_days_encrease">&#43;</a>
                                <input {*readonly="readonly"*} type="text" size="5" class="ty-value-changer__input cm-amount" id="cp_matrix_filter_days" name="cp_matrix_filter_days" value="{if $cp_matr_del_time.current_value}{$cp_matr_del_time.current_value}{else}{$cp_matr_del_time.max}{/if}" data-ca-step="1" data-ca-min-qty="{$cp_matr_del_time.min}"  data-ca-max-qty="{$cp_matr_del_time.max}" />
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
                {if $filter.filter_id != $addons.cp_spl_theme.filter_price_id && $filter.filter_id != $addons.cp_spl_theme.filter_warehouse_city_id}

                    {$filter_uid = "`$block.block_id`_`$filter.filter_id`"}
                    {$reset_url = ""}
                    {if $filter.selected_variants || $filter.selected_range}
                        {$reset_url = $filter_base_url}
                        {$fh = $smarty.request.features_hash|fn_delete_filter_from_hash:$filter.filter_id}
                        {if $fh}
                            {$reset_url = $filter_base_url|fn_link_attach:"features_hash=$fh"}
                        {/if}
                    {/if}
                    <div class="cp-horizontal-product-filters-container{if $smarty.foreach.filters.last} cp-horizontal-product-filters-container_last{/if}{if $filter.field_type == "Z" } cp-horizontal-product-filters-container__qty{/if}">
                        <div class="cp-horizontal-product-filters__title {if $filter.selected_variants || $filter.selected_range}active{/if}">
                            {$filter.filter}
                            {if $reset_url}
                                <a class="cm-ajax cm-ajax-full-render cm-history" href="{$reset_url|fn_url}" data-ca-event="ce.filtersinit" data-ca-target-id="{$ajax_div_ids}"data-ca-scroll=".ty-mainbox-title">
                                    <i class="ty-icon-cancel-circle"></i>
                                </a>
                            {/if}
                        </div>
                        {if $filter.field_type == "Z"}
                            <div class="cp-horizontal-product-filters_z">
                                {hook name="blocks:product_filters_variants_element"}
                                {if $filter.slider}
                                    {if $filter.feature_type == "ProductFeatures::DATE"|enum}
                                        {include file="blocks/product_filters/components/product_filter_datepicker.tpl" filter_uid=$filter_uid filter=$filter}
                                    {else}
                                        {include file="blocks/product_filters/components/product_filter_slider.tpl" filter_uid=$filter_uid filter=$filter}
                                    {/if}
                                {else}
                                    {include file="blocks/product_filters/components/product_filter_variants.tpl" filter_uid=$filter_uid filter=$filter}
                                {/if}
                                {/hook}
                            </div>
                        {else}
                            <div class="cp-horizontal-product-filters-dropdown">
                                <div id="sw_elm_filter_{$filter.filter_id}" class="cp-horizontal-product-filters-dropdown__wrapper cm-combination">
                                    {if $filter.selected_variants}
                                        {foreach from=$filter.selected_variants item="variant" name="variant"}
                                            {$variant.variant}{if !$smarty.foreach.variant.last}, {/if}
                                        {/foreach}
                                    {else}
                                        {$filter.filter}
                                    {/if}
                                    <span class="cp-block-down-up">
                                <i class="ty-icon-down-open"></i>
                                <i class="ty-icon-up-open"></i>
                            </span>
                                </div>
                                <div id="elm_filter_{$filter.filter_id}" class="cm-popup-box hidden cp-horizontal-product-filters-dropdown__content cm-horizontal-filters-content">
                                    {$filter_uid="`$block.block_id`_`$filter.filter_id`"}
                                    {hook name="blocks:product_filters_variants_element"}
                                    {if $filter.slider}
                                        {if $filter.feature_type == "ProductFeatures::DATE"|enum}
                                            {include file="blocks/product_filters/components/product_filter_datepicker.tpl" filter_uid=$filter_uid filter=$filter}
                                        {else}
                                            {include file="blocks/product_filters/components/product_filter_slider.tpl" filter_uid=$filter_uid filter=$filter}
                                        {/if}
                                    {else}
                                        {include file="blocks/product_filters/components/product_filter_variants.tpl" filter_uid=$filter_uid filter=$filter}
                                    {/if}
                                    {/hook}
                                </div>
                            </div>
                        {/if}
                    </div>
                {/if}
            {/foreach}
        {/if}

        {if $ajax_div_ids}
            <div class="ty-product-filters__tools clearfix">
                <a class="cp-np__apply-button ty-btn__secondary ty-btn">{__("apply")}</a>
                <a href="{$filter_base_url|fn_url}" rel="nofollow" class="ty-product-filters__reset-button cm-ajax cm-ajax-full-render cm-history" data-ca-event="ce.filtersinit" data-ca-scroll=".cp-vendor-products" data-ca-target-id="{$ajax_div_ids}">{*<i class="ty-product-filters__reset-icon ty-icon-cw"></i> *}{__("reset")}</a>
            </div>
        {/if}
    </div>
    <!--product_filters_{$block.block_id}--></div>

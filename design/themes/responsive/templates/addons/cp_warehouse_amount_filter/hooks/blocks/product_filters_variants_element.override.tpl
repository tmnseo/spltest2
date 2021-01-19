{hook name="blocks:product_filters_variants_element"}
    {if $filter.slider}
        {if $filter.feature_type == "ProductFeatures::DATE"|enum}
            {include file="blocks/product_filters/components/product_filter_datepicker.tpl" filter_uid=$filter_uid filter=$filter}
        {else}
            {include file="blocks/product_filters/components/product_filter_slider.tpl" filter_uid=$filter_uid filter=$filter}
        {/if}
    {elseif $filter.field_type == "Z"}
    	{include file="addons/cp_warehouse_amount_filter/blocks/product_filters/components/product_filter_warehouse_amount.tpl" filter_uid=$filter_uid filter=$filter collapse=$collapse}
    {else}
        {include file="blocks/product_filters/components/product_filter_variants.tpl" filter_uid=$filter_uid filter=$filter collapse=$collapse}
    {/if}
{/hook}
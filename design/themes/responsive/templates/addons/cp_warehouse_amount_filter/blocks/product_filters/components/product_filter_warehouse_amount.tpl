<div class="cp-warehouse-amount-filter ty-product-filters {if $collapse}hidden{/if} ty-value-changer cm-value-changer" id="content_{$filter_uid}">
    <a class="cm-increase ty-value-changer__increase">&#43;</a>
    <input class="ty-value-changer__input warehouses_amount" type="text" name="warehouses_amount" data-ca-filter-uid="{$filter_uid}" data-ca-filter-id="{$filter.filter_id}" value="{$filter.warehouse_amount|default:1}" id="elm_filter_warehouses_amount_{$filter_uid}"" data-ca-min-qty="1" onchange="fn_cp_change_filter_amount(this.value);">
    <a class="cm-decrease ty-value-changer__decrease">&minus;</a>
</div>
<input id="elm_checkbox_warehouses_amount_{$filter_uid}" data-ca-filter-id="{$filter.filter_id}" class="cm-product-filters-checkbox hidden" type="checkbox" name="product_filters[{$filter.filter_id}]" value="{$filter.warehouse_amount|default:1}" checked="checked" />
<ul class="cp-types-equipment__list">
{foreach from=$warranties item=brand}
    <li class="cp-types-equipment__item">
        <div class="cp-types-equipment__item-brand cm-combination" id="sw_types_equipment_{$brand.variant_id}">
            <span class="cp-types-equipment__brand">
                {$brand.name}
            </span>
            <span class="cp-block-down-up">
                <i class="ty-icon-down-open"></i>
                <i class="ty-icon-up-open"></i>
            </span>
        </div>
        <ul class="cp-types-equipment__categories hidden" id="types_equipment_{$brand.variant_id}">
            {foreach from=$brand.categories item="category"}
                <li class="cp-types-equipment__category">
                    <span class="cp-types-equipment__category-name">{$category.category}</span>
                    <span class="cp-types-equipment__category-guarantee">{$category.warranty_txt nofilter}</span>
                </li>
            {/foreach}
        </ul>
    </li>
{/foreach}
</ul>
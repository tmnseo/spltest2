{** block-description:part_applicability **}
{if $features.$brand_techniques_feat_id.variants}
<div class="ty-product-detail-tabs ty-part-applicability">
    <div class="ty-product-detail__column">
        <div class="ty-part-applicability__title">
            {__("cp_product_page.detail", ["[product]" => $product.product])}
            {if $manuf_code_feat_id}
                <span>({$features.$manuf_code_feat_id.variant})</span>
            {/if}
            {__("cp_np_part_applicability_text")}:
        </div>
        <p class="ty-part-applicability__notice hidden-phone">{__("cp_np_part_applicability_notice")}</p>
    </div>
    <div class="ty-product-detail__column">
        <ul class="ty-part-applicability__list">
            {foreach from=$features.$brand_techniques_feat_id.variants item="variant"}
                <li class="ty-part-applicability__item">{$variant.variant}</li>
            {/foreach}
        </ul>
        <p class="ty-part-applicability__notice visible-phone">{__("cp_np_part_applicability_notice")}</p>
    </div>
</div>
{/if}

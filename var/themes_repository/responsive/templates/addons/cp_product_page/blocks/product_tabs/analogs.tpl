{** block-description:analogs **}
{if $addons.cp_product_page.brand_code_id}
    {$article_feature_id=$addons.cp_product_page.brand_code_id}
    {$article = $features.$article_feature_id.variants|current}
{/if}
{if $features.$analogs.variants}
<div class="ty-product-detail-tabs ty-part-applicability">
    <div class="ty-product-detail__column">
        <div class="ty-part-applicability__title">
            {__("cp_product_page.analogs", ["[product]" => $product.product])}
            {if $manuf_code_feat_id}
                <span>({$features.$manuf_code_feat_id.variant})</span>
            {/if}
            {__("cp_np_analogs_text")}:
        </div>
        <p class="ty-part-applicability__notice hidden-phone">{__("cp_np_analogs_notice")}</p>
    </div>
    <div class="ty-product-detail__column">
        <ul class="ty-part-applicability__list">
            {foreach from=$features.$analogs.variants item="variant"}
                {if $article.variant|strtolower != $variant.variant|strtolower}
                    <li class="ty-part-applicability__item">{$variant.variant}</li>
                {/if}
            {/foreach}
        </ul>
        <p class="ty-part-applicability__notice visible-phone">{__("cp_np_analogs_notice")}</p>
    </div>
</div>
{/if}
{$obj_prefix = "`$block.block_id`000"}
<div class="brands-flex cp_mCustomScrollbar" id="brands_flex_{$block.block_id}">
    <div class="brands-flex__content">
    {foreach from=$brands item="brand" name="for_brands"}
        {include file="common/image.tpl" assign="object_img" image_width=$block.properties.thumbnail_width image_height=$block.properties.thumbnail_width images=$brand.image_pair no_ids=true lazy_load=false obj_id="scr_`$block.block_id`000`$brand.variant_id`"}
            {if $brand.image_pair}
                <a class="brands-flex__item" href="{"product_features.view?variant_id=`$brand.variant_id`"|fn_url}">{$object_img nofilter}</a>
            {/if}
    {/foreach}
    </div>
</div>
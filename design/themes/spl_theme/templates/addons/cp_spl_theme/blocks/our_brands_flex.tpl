{$obj_prefix = "`$block.block_id`000"}
{$item_count = $block.properties.item_quantity|default:6}
{$view_brands = 0}
<div class="brands-flex" id="brands_flex_{$block.block_id}">
	<div class="brands-flex__content-wrap">
    	<div class="brands-flex__content">
		{foreach from=$brands item="brand" name="for_brands"}
			{if $view_brands < $item_count && $brand.image_pair}
				{include file="common/image.tpl" 
					assign="object_img" 
					image_width=$block.properties.thumbnail_width_flex 
					image_height=$block.properties.thumbnail_height_flex 
					images=$brand.image_pair no_ids=true 
					lazy_load=false 
					obj_id="scr_`$block.block_id`000`$brand.variant_id`"
				}
				
						<span class="brands-flex__item">{$object_img nofilter}</span>
						{* <a class="brands-flex__item" href="{"product_features.view?variant_id=`$brand.variant_id`"|fn_url}">{$object_img nofilter}</a> *}
				{$view_brands = $view_brands + 1}
			{elseif $view_brands >= $item_count}
				{break}
			{/if}
		{/foreach}
		</div>
	</div>
	{if $block.properties.filter_id}
		<a class="ty-btn__all-brands" href="{"product_features.view_all&filter_id=`$block.properties.filter_id`"|fn_url}">{__("view_all_brands")}</a>
	{/if}
</div>

{if $variants}
{$size = 4}
{split data=$variants size=$size assign="splitted_filter" preverse_keys=true}
<div class="ty-features-all__wrap">
{include file="common/pagination.tpl"}
<div class="ty-features-all">
    {foreach from=$splitted_filter item="group"}
        {foreach from=$group item="ranges" key="index"}
            {strip}
                {foreach from=$ranges item="range"}
                    {if $ranges}
                        <div class="ty-features-all__item">
                            <div class="ty-features-all__item-img">
                                {include file="common/image.tpl"
                                    show_detailed_link=false
                                    images=$range.image_pair
                                    no_ids=true
                                    image_width="120"
                                    image_height="70"
                                    class="ty-features-all__item_img"
                                }
                            </div>
                            <div class="ty-features-all__item-info">
                                {* <a href="{"product_features.view?variant_id=`$range.variant_id`"|fn_url}" class="ty-features-all__item-name">{$range.variant|fn_text_placeholders}</a> *}
                                <div class="ty-features-all__item-name">{$range.variant|fn_text_placeholders}</div>
                                {if $range.description}
                                    <div class="ty-features-all__item-desc_wrap">
                                        <div class="ty-features-all__item-desc">
                                            {$range.description nofilter}
                                        </div>
                                    </div>
                                    <span class="ty-features-all__item-show-more hidden">
                                        <span>{__("show_more")}</span>
                                        <span>{__("advanced_import.show_less")}</span>
                                    </span>
                                {/if}
                            </div>
                        </div>
                    {/if}
                {/foreach}
            {strip}
        {/foreach}
    {/foreach}
</div>
{include file="common/pagination.tpl"}
</div>
{/if}

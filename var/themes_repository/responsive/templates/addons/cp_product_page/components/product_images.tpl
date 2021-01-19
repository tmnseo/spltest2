{assign var="product_detail_view_url" value="products.view?product_id=`$product.product_id`"}
{capture name="product_detail_view_url"}
{** Sets product detail view link *}
{hook name="products:product_detail_view_url"}
{$product_detail_view_url}
{/hook}
{/capture}

{if $product.main_pair.icon || $product.main_pair.detailed}
    {assign var="image_pair_var" value=$product.main_pair}
{elseif $product.option_image_pairs}
    {assign var="image_pair_var" value=$product.option_image_pairs|reset}
{/if}

{if $image_pair_var.image_id}
    {assign var="image_id" value=$image_pair_var.image_id}
{else}
    {assign var="image_id" value=$image_pair_var.detailed_id}
{/if}

{if !$preview_id}
    {assign var="preview_id" value=$product.product_id|uniqid}
{/if}

{$product_detail_view_url = $smarty.capture.product_detail_view_url|trim}

{capture name="main_icon"}
        {include file="common/image.tpl" obj_id="`$preview_id`_`$image_id`" images=$product.main_pair link_class="cm-image-previewer" image_width=$image_width image_height=$image_height image_id="preview[product_images_`$preview_id`]"}
{/capture}

{if $product.image_pairs && $show_gallery}
    <div class="ty-product-img__gallery" data-ca-items-count="3" data-ca-items-responsive="true" id="product_images_{$preview_id}">
        {if $product.main_pair}
            <div class="ty-product-img__gallery-item slide-item">
                <div>
                    {$smarty.capture.main_icon nofilter}
                </div>
            </div>
        {/if}
        {foreach from=$product.image_pairs item="image_pair"}
            {if $image_pair}
                <div class="ty-product-img__gallery-item slide-item">
                    <div>
                        {include file="common/image.tpl" obj_id="`$preview_id`_`$image_id`" no_ids=true images=$image_pair link_class="cm-image-previewer" image_width=$image_width image_height=$image_height image_id="preview[product_images_`$preview_id`]"}
                    </div>
                </div>
            {/if}
        {/foreach}
    </div>
{else}
    {$smarty.capture.main_icon nofilter}
{/if}

<script type="text/javascript">
(function(_, $) {
    $.ceEvent('on', 'ce.commoninit', function(context) {
        $('#images').on('click', function(elm) {
            var elm = $('.ty-product-img__gallery');
            var width = $(document).width();
            if (elm.length) {
                if (width > 768) {
                    elm.not('.slick-initialized').slick({
                        infinite: false,
                        slidesToShow: 3,
                        slidesToScroll: 1,
                        dots: false,
                        variableWidth: true,
                        responsive: [
                            {
                            breakpoint: 1024,
                                settings: {
                                    slidesToShow: 2
                                },
                            breakpoint: 450,
                                settings: {
                                    slidesToShow: 1
                                }
                            }
                        ]
                    });
                }else{
                    elm.not('.slick-initialized').slick({
                        infinite: false,
                        rows: 3,
                        slidesToShow: 2,
                        slidesToScroll: 2,
                        dots: false,
                    });
                }
            }
        });     
    });
}(Tygh, Tygh.$));
</script>
{** block-description:images_product **}
{if $product.main_pair || $product.image_pairs}
    {if !$no_images}
        <div class="ty-product-block__img cm-reload-{$product.product_id}" data-ca-previewer="true" id="product_images_{$product.product_id}_update">
            {include file="addons/cp_product_page/components/product_images.tpl" product=$product show_detailed_link="Y" image_width=$settings.Thumbnails.product_details_thumbnail_width image_height=$settings.Thumbnails.product_details_thumbnail_height show_gallery=true}
        <!--product_images_{$product.product_id}_update--></div>
    {/if}
{/if}

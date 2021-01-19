{if $addons.vendor_communication.show_on_product == "Y"}
    <div class="ty-product-detail-desc__vendor-communication ty-product-detail__item">
        {include file="addons/vendor_communication/views/vendor_communication/components/new_thread_button.tpl" object_id=$product.product_id show_form=false but_title=__("cp_spl_theme.question_product") show_icon=false}
    </div>
{/if}
{* {script src="js/tygh/exceptions.js"} *}
{$object_id = $block.block_id}
<div class="cp-vendor-certificatest" data-ca-previewer="true">
    <div class="cp-vendor-certificates__scroller cm-preview-wrapper" id="vendor_certificate_{$object_id}">
        {foreach from=$certificates item="certificate" name="certificates"}
         {$attachment_id = $certificate.attachment_id}
            <div class="cp-vendor-certificates__scroller-item">
                <div class="cp-vendor-certificates__item">
                    <a class="cm-previewer cm-image-previewer" 
                        id="det_img_link_{$object_id}_{$attachment_id}" 
                        data-ca-image-id="preview[vendor_certificate_{$object_id}]"
                        data-ca-image-order="{$smarty.foreach.certificates.index}"
                        href="{$certificate.cert_file_path}" 
                        data-ca-image-width="1200" 
                        data-ca-image-height="1200"
                    >
                        <img class="cm-image cp-vendor-certificates__item-img" id="det_img_{$object_id}_{$attachment_id}" src="{$certificate.cert_file_path}" alt="{$certificate.description}">
                    </a>
                    <span class="cp-vendor-certificates__item-name">{$certificate.description}</span>
                </div>
            </div>
        {/foreach}
        
    </div>
</div>

{script src="js/addons/cp_spl_theme/owl.previewer.js"}
<script type="text/javascript">
    (function(_, $) {
        $.ceEvent('on', 'ce.commoninit', function(context) {
            $('#certificates_{$block.block_id}').on('click', function(elm) {
                var elm = $('.cp-vendor-certificates__scroller');
                var width = $('body').width();
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
                            rows: 2,
                            slidesToShow: 3,
                            slidesToScroll: 3,
                            dots: false,
                        });
                    }
                }
            });     
        });
    }(Tygh, Tygh.$));
</script>
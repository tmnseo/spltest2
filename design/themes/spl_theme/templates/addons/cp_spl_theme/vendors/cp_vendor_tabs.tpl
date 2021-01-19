
{script src="js/tygh/tabs.js"}
<div class="ty-tabs ty-tabs_spl cm-j-tabs cm-j-tabs-disable-convertation clearfix">
    <ul class="ty-tabs__list__{$block.block_id} ty-tabs__list-scroll">
        {if $cp_vp_additional_data.warranties}
            <li id="warranties_{$block.block_id}" class="ty-tabs__item cm-js active">
                <a class="ty-tabs__a">{__("cp_types_equipment")}</a>
            </li>
        {/if}
        <li id="rating_reviews_{$block.block_id}" class="ty-tabs__item cm-js">
            <a class="ty-tabs__a">{__("rating_and_reviews")}</a>
        </li>
        {if $cp_vp_additional_data.certificates}
            <li id="certificates_{$block.block_id}" class="ty-tabs__item cm-js">
                <a class="ty-tabs__a">{__("cp_vp_certificates")}</a>
            </li>
        {/if}
        {if $cp_vendor_warehouses}
            <li id="warehouses_{$block.block_id}" class="ty-tabs__item cm-js">
                <a class="ty-tabs__a">{__("cp_warehouses")}</a>
            </li>
        {/if}
    </ul>
</div>
<div class="cm-tabs-content ty-tabs__content ty-tabs__content_vendor-store clearfix" id="tabs_content">
    {if $cp_vp_additional_data.warranties}
        <div id="content_warranties_{$block.block_id}" class="ty-tabs-content__item cp-types-equipment">
            <h3 class="ty-tabs-content__item-title">{__("cp_types_equipment_title")}:</h3>
           {include file="addons/cp_spl_theme/vendors/components/types_equipment.tpl" warranties=$cp_vp_additional_data.warranties }
        </div>
    {/if}
    <div id="content_rating_reviews_{$block.block_id}">
        {include
            file="addons/discussion/views/discussion/view.tpl"
            object_id=$vendor_info.company_id
            object_type="Addons\\Discussion\\DiscussionObjectTypes::COMPANY"|enum
            locate_to_review_tab=true
            wrap=false
            vendor_discussion=true
            hide_discussion_data=true
        }
    </div>
    {if $cp_vp_additional_data.certificates}
        <div id="content_certificates_{$block.block_id}">
            {include file="addons/cp_spl_theme/vendors/components/certificates.tpl" certificates=$cp_vp_additional_data.certificates}
        </div>
    {/if}
    <div id="content_warehouses_{$block.block_id}">
        {hook name="cp_vendor_tabs:warehouses_tab"}
        {/hook}
    </div>
</div>

<script type="text/javascript">
(function(_, $) {
    $.ceEvent('on', 'ce.commoninit', function(context) {
        var elm = context.find('.ty-tabs__list__{$block.block_id}');
        var widthDocument = $(document).width();

        if (elm.length && widthDocument <= 550) {
            elm.slick({
                dots: false,
                arrows: true,
                infinite: false,
                slidesToShow: 1,
                variableWidth: true,
                responsive: [
                    {
                    breakpoint: 767,
                        settings: {
                            arrows: false
                        }
                    }
                ]
            });
        }
    });
}(Tygh, Tygh.$));
</script>


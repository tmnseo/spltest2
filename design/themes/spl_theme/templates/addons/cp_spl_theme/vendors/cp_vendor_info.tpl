{** block-description:cp_vendor_info **}
<div class="cp-vendor-info">
    {if $vendor_info.logos.theme.image}
    <div class="cp-vendor-info__logo">
        {include file="common/image.tpl"
            obj_id=$vendor_info.company_id
            images=$vendor_info.logos.theme.image
            class="ty-logo-container-vendor__image"
            image_additional_attrs=["width" => 180, "height" => $vendor_info.logos.theme.image.image_y]
            show_no_image=false
            show_detailed_link=false
            capture_image=false
        }
    </div>
    {/if}
    
    <div class="cp-vendor-info-desc">
        <h2 class="cp-vendor-info-desc__name">
            {* <a class="spl-vendor-information__link" href="{"companies.view?company_id=`$vendor_info.company_id`"|fn_url}">
                {$vendor_info.company}
            </a> *}
            <span class="spl-vendor-information__link">{$vendor_info.company}</span>
        </h2>
        <div class="cp-vendor-info-desc__desc">
            {$vendor_info.company_description nofilter}
        </div>
    </div>

    {if $addons.vendor_communication.show_on_vendor == "Y"}
    <div class ="cp-vendor-info__communication">
        {include file="addons/vendor_communication/views/vendor_communication/components/new_thread_button.tpl" object_id=$company_id show_form=true}
    </div>
        {include
            file="addons/vendor_communication/views/vendor_communication/components/new_thread_form.tpl"
            object_type=$smarty.const.VC_OBJECT_TYPE_COMPANY
            object_id=$company_id
            company_id=$company_id
            vendor_name=$company_id|fn_get_company_name
        }
    {/if}
</div>

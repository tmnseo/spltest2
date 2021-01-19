{** block-description:block_vendor_information **}

<div class="ty-vendor-information">
    <h1>
        <a class="spl-vendor-information__link" href="{"companies.view?company_id=`$vendor_info.company_id`"|fn_url}">
            {$vendor_info.company}
        </a>
    </h1>
    <span>{$vendor_info.company_description nofilter}</span>
</div>
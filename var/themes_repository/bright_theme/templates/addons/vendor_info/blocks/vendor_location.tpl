{** block-description:my_block_vendor_info **}

<h3>{__("location")}</h3>

<p class="spl-seller-text">
	<span>{$vendor_info.zipcode}</span>&#44;&nbsp;<span>{$vendor_info.city}</span>
</p>

<p class="spl-seller-text">
	<span>{$vendor_info.address}</span>
</p>

 <p class="ty-cr-link ty-vendor-link ty-vendor-location">
<img src="/design/themes/bright_theme/media/images/geolocation.png">
{include file="common/popupbox.tpl"
            href="store_locator.search"
            link_text=__("view_on_map")
            text=__("view_on_map")
            id=""
            content=""
        } 
 </p>

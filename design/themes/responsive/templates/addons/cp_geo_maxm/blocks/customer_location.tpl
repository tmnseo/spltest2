{** block-description:cp_geo_maxm_customer_location **}
    {$block_id = $block.snapping_id|default:$id}
    <div class="ty-geo-maps__geolocation"
         data-ca-geo-map-location-is-location-detected="{if $location_detected|default:false}true{else}false{/if}"
         data-ca-geo-map-location-element="location_block"
         id="cp_geo_maps_location_block_{$block_id}"
    >
        {capture name="geo_maps_location_popup_opener"}
            {strip}
                <span data-ca-geo-map-location-element="location" class="ty-geo-maps__geolocation__location">
                {$location.city|default:__("geo_maps.your_city")}
            </span>
            {/strip}
        {/capture}
        <span id="geo_maps_location_dialog_{$block_id}" class="ty-geo-maps__geolocation__opener">
            <i class="icon-spl-location"></i>
            <span class="ty-geo-maps__geolocation__opener-text cm-combination" id="sw_geo_list_{$block.block_id}">
                {$smarty.capture.geo_maps_location_popup_opener nofilter}
            </span>
        </span>
        {* {include file="common/popupbox.tpl"
                 href="geo_maps.customer_geolocation"
                 link_text=$smarty.capture.geo_maps_location_popup_opener
                 link_text_meta="ty-geo-maps__geolocation__opener-text"
                 link_icon="icon-spl-location"
                 link_icon_first=true
                 link_meta="ty-geo-maps__geolocation__opener"
                 text=__("geo_maps.select_your_city")
                 id="geo_maps_location_dialog_{$block_id}"
                 content=""
        } *}


        {include file="addons/cp_geo_maps_ext/components/geo_list_cities.tpl"}
        <!--cp_geo_maps_location_block_{$block_id}--></div>

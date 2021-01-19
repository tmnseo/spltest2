{** block-description:cp_geo_maxm_customer_location **}
    {$block_id = $block.snapping_id|default:$id}
    <div class="ty-geo-maps__geolocation"
         data-ca-geo-map-location-is-location-detected="{if $location_detected|default:false}true{else}false{/if}"
         data-ca-geo-map-location-element="location_block"
         id="cp_geo_maps_location_block_{$block_id}"
    >



        {capture name="geo_maps_location_popup_opener"}
            {strip}
                <span class="ty-geo-maps__geolocation__location">
                {$location.s_city|default:__("geo_maps.your_city")}
            </span>
            {/strip}
        {/capture}
        <span id="geo_maps_location_dialog_{$block_id}" class="ty-geo-maps__geolocation__opener">
            <i class="icon-spl-location"></i>
            <span class="ty-geo-maps__geolocation__opener-text cm-combination" id="sw_geo_list_{$block.block_id}">
                {$smarty.capture.geo_maps_location_popup_opener nofilter}
            </span>
        </span>



        {if !$location.cp_location_confirmed && $location.city}
            <div class="ty-geo-maps-confirm__background"></div>
            <div class="ty-geo-maps-confirm">
                <div class="ty-geo-maps-confirm__container">
                    <div class="ty-geo-maps-confirm__header">
                        <div class="ty-geo-maps-confirm__title ">
                        <span class="cp-ty-geo-maps-confirm__city">{$location.city}</span> - {__("addons.cp_geo_maps_ext.title")}?
                        </div>
                    </div>
                    <div class="ty-geo-maps-confirm__buttons">
                        <a class="ty-btn__secondary ty-btn__geo-maps">
                                       <span class="ty-geo-maps-confirm__yes">
                                            {__("addons.cp_geo_maps_ext.yes")}
                                        </span>
                        </a>
                        <a onclick="$('#sw_geo_list_{$block.block_id}').click();"><span class="ty-geo-maps-confirm__no ty-btn__geo-maps">{__("addons.cp_geo_maps_ext.no")}</span></a>
                    </div>
                </div>
            </div>
        {/if}


        {include file="addons/cp_geo_maxm/components/geo_list_cities.tpl"}
        <!--cp_geo_maps_location_block_{$block_id}--></div>

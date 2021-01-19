{if !$location.cp_location_confirmed && $location.city}
    <div class="ty-geo-maps-confirm__background"></div>
    <div class="ty-geo-maps-confirm">
        <div class="ty-geo-maps-confirm__container">
            <div class="ty-geo-maps-confirm__header">
                <div class="ty-geo-maps-confirm__title ">{__("addons.cp_geo_maps_ext.title")} <span class="ty-geo-maps-confirm__city">{$location.city}</span>?</div>
            </div>
            <div class="ty-geo-maps-confirm__buttons">
                <a class="ty-btn__secondary ty-btn__geo-maps">
                   <span class="ty-geo-maps-confirm__yes">
                        {__("addons.cp_geo_maps_ext.yes")}
                    </span>
                </a>
                {include file="addons/cp_geo_maps_ext/common/popup_open_link.tpl"
                         href="geo_maps.customer_geolocation"
                         link_text={__("addons.cp_geo_maps_ext.no")}
                         link_text_meta="ty-geo-maps-confirm__no ty-btn__geo-maps"
                         link_meta=""
                         text=__("geo_maps.select_your_city")
                         id="geo_maps_location_dialog_{$block_id}"
                         content=""
                }
            </div>
        </div>
    </div>
{/if}
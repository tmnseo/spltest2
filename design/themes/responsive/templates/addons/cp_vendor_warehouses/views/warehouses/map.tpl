<div class="hidden " id="map_{$warehouse_data.warehouse_id}">
	{capture name="marker_content"}
	    <div class="cp-marker">
			<span class="cp-marker__item">
				<span class="cp-marker__label">{__("stock")}: </span>
				<span class="cp-marker__value">{$warehouse_data.city},<br>{$warehouse_data.address}</span>
			</span>
			{if $warehouse_data.worktime}
			<span class="cp-marker__item">
				<span class="cp-marker__label">{__("working_hours")}:</span><br>
				<span class="cp-marker__value">{$warehouse_data.worktime}</span>
			</span>
			{/if}
			{if $warehouse_data.phone}
			<span class="cp-marker__item">
				<span class="cp-marker__label">{__("phone")}:</span><br>
				<span class="cp-marker__value">{$warehouse_data.phone}</span>
			</span>
			{/if}
	    </div>
	{/capture}
	<div class="cm-warehouse-desc-{$warehouse_data.warehouse_id} hidden"
	     data-ca-geo-map-marker-lat="{$warehouse_data.latitude}"
	     data-ca-geo-map-marker-lng="{$warehouse_data.longitude}"
	     data-ca-geo-map-marker-selected="true"
	>{$smarty.capture.marker_content nofilter}</div>
    <div class="cm-geo-map-container cm-aom-map-container cp-vendor-warehouse-map"
        data-ca-geo-map-language="{$smarty.const.CART_LANGUAGE}"
        data-ca-geo-map-initial-lat="{$warehouse_data.latitude}"
     	data-ca-geo-map-initial-lng="{$warehouse_data.longitude}"
        data-ca-geo-map-marker-selector=".cm-warehouse-desc-{$warehouse_data.warehouse_id}"
        data-ca-geo-map-controls-enable-zoom="true"
        data-ca-geo-map-behaviors-enable-drag="true"
        data-ca-geo-map-behaviors-enable-drag-on-mobile="true"
        data-ca-geo-map-behaviors-enable-dbl-click-zoom="true"
        data-ca-geo-map-behaviors-enable-multi-touch="true"
        data-ca-geo-map-behaviors-enable-scroll-zoom="true"
        id="container_{$warehouse_data.warehouse_id}"
    ></div>
<!--map_{$warehouse_data.warehouse_id}--></div>
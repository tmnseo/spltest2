{if $cp_vendor_warehouses}
{$object_id = $block.block_id}
<div class="cp-vendor-warehouses">
    <div class="cp-vendor-warehouses__scroller cm-preview-wrapper" id="cp_warehouses_{$object_id}">
		{foreach from=$cp_vendor_warehouses item="warehouse_data"}
		<div class="cp-vendor-warehouse__scroller-item">
			<a class="cp-vendor-warehouse__item cm-dialog-opener cm-dialog-auto-size" href="{"cp_vendor_warehouses.view_map?warehouse_id={$warehouse_data.warehouse_id}"|fn_url}" data-ca-target-id="map_{$warehouse_data.warehouse_id}" data-ca-dialog-class="cp-vendor-warehouse__popup" style="width: {$addons.cp_vendor_warehouses.map_width}px;">
				<span class="cp-vendor-warehouse__item-img" style="width: {$addons.cp_vendor_warehouses.map_width}px; height: {$addons.cp_vendor_warehouses.map_height}px;">
					<img src="{$warehouse_data.image_href}" width="{$addons.cp_vendor_warehouses.map_width}" height="{$addons.cp_vendor_warehouses.map_width}" alt="{$warehouse_data.description}"/>
					<span class="icon-spl-location"></span>
				</span>
				<span class="cp-vendor-warehouse__item-address">{$warehouse_data.description}</span>
				<span class="cp-vendor-warehouse__item-view-link">{__("cp_vendor_warehouses.view_on_map")}</span>
			</a>
		</div>
		{/foreach}
	</div>
</div>
{/if}
<script type="text/javascript">
	$.ceEvent('on', 'ce.commoninit', function (context) {
        fn_cp_init_address_on_map(context);

		/*This function is added because if 
		there is more than 1 map on the page 
		the placemark initialization function 
		on the map is triggered 1 time.
		*/

		$('#warehouses_{$block.block_id}').on('click', function(elm) {
			var elm = $('.cp-vendor-warehouses__scroller');
			var width = $('body').width();
			if (elm.length) {
				if (width > 768) {
					elm.not('.slick-initialized').slick({
						infinite: false,
						slidesToShow: 3,
						slidesToScroll: 1,
						dots: false,
						//variableWidth: true,
						responsive: [
							{
							breakpoint: 1240,
							settings: {
								slidesToShow: 2
							}
							},
							{
							breakpoint: 870,
							settings: {
								slidesToShow: 1
							}
							}
						]
					});
				}else{
					elm.not('.slick-initialized').slick({
						infinite: false,
						rows: 3,
						slidesToShow: 1,
						slidesToScroll: 1,
						dots: false,
					});
				}
			}
		});
    });
</script>
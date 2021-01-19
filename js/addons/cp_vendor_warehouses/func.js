function fn_cp_init_address_on_map(context) {
    var $address_on_map_container = $(context).find('.cm-aom-map-container');
    
    var address = [$address_on_map_container.data('caAomCountry'), $address_on_map_container.data('caAomCity'), $address_on_map_container.data('caAomAddress')]
        .filter(function (item) {
            return !!item;
        })
        .join(', ');

    if (!address) {
        return;
    }

    $.ceGeoCode('getCoords', address)
        .done(function (data) {
            if (data.lat && data.lng) {
                data.static = true;
                data.content = address;
                $address_on_map_container.ceGeoMap('removeAllMarkers');
                $address_on_map_container.ceGeoMap('addMarkers', [data]);
                $address_on_map_container.ceGeoMap('setCenter', data.lat, data.lng);
            }
        });
}
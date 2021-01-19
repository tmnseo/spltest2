(function (_, $) {
    $("a .ty-geo-maps-confirm__yes" ).on( "click", function() {
            fn_cp_confirm_location('Y');
    });
    $("a .ty-geo-maps-confirm__no" ).on( "click", function() {
            fn_cp_confirm_location('N');
    });

    $.ceEvent('on', 'ce.dialogshow', function ($context) {
        if (!$('[data-ca-geo-map-location-element="map"]', $context).length) {
            return;
        }
        $('.ty-geo-maps-confirm').data('popup-confirmed', true);
    });
       
    $.ceEvent('on', 'ce:geomap:location_set_after', function (location, $container, response, auto_detect) {
        
            $container.each(function (i, elm) {
                var $elm = $(elm);
                $('.ty-geo-maps-confirm__city', $elm).text(response.city);
            });
            if ($(".ty-geo-maps-confirm").data('popup-confirmed')) {
                    fn_cp_confirm_location('N');
            }
    });
    
    $(document).mouseup(function (e) {
        var container = $('.ty-geo-maps__geolocation').children();
        if (container.has(e.target).length === 0){
            fn_cp_confirm_location('Y');
        }
    });
    
})(Tygh, Tygh.$);

function fn_cp_confirm_location(status) {
    
    $.ceAjax('request', fn_url('cp_geo_maps_ext.confirm_location'), {
        method: 'post',
        data: {status: status},
        hidden: true,
        caching: false,
        callback: function (response) {
            $(".ty-geo-maps-confirm").remove();
            $(".ty-geo-maps-confirm__background").remove();

        }
    });
}
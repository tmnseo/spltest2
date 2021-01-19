(function (_, $) {

    var $autocompleteCity;

    var methods = {
        init: function () {
          
            //$country = methods.getElement('country');
            //$city = methods.getElement('city');
            //$zipCode = methods.getElement('zipcode');
            $autocompleteCity = methods.getElement('city-autocomplete');


            //alert($autocompleteCity);
            

            if ($autocompleteCity.length) {
                
                //methods.initAutocompleteCity();
            } else {
               // methods.initPlainCityInput();
            }

        },


        autocompleteCity: function (query, countryCode, callback) {
            var url = $autocompleteCity.data('caLiteCheckoutAutocompleteUrl'),
                method = $autocompleteCity.data('caLiteCheckoutAutocompleteRequestMethod'),
                cityParam = $autocompleteCity.data('caLiteCheckoutAutocompleteCityParam'),
                countryParam = $autocompleteCity.data('caLiteCheckoutAutocompleteCountryParam'),
                itemsPerPageParam = $autocompleteCity.data('caLiteCheckoutAutocompleteItemsPerPageParam'),
                itemsPerPage = $autocompleteCity.data('caLiteCheckoutAutocompleteItemsPerPage'),
                hidden = $autocompleteCity.data('caLiteCheckoutAutocompleteHidden') !== false,
                caching = $autocompleteCity.data('caLiteCheckoutAutocompleteCaching') !== false,
                ajaxCallback = callback || $.noop;

            var requestData = {};
            requestData[cityParam] = query;
            requestData[countryParam] = countryCode;
            requestData[itemsPerPageParam] = itemsPerPage;

            $.ceAjax('request', url, {
                method: method,
                hidden: hidden,
                caching: caching,
                data: requestData,
                callback: function (data) {
                    ajaxCallback(data);
                }
            })
        },

        getElement: function (role, getAll) {
            var selector = '[data-ca-lite-checkout-element="' + role + '"]';
            if (getAll !== true) {
                selector += ':not(:disabled)';
            }

            return $(selector);
        },

        autocomplete: {
            source: function( request, response ) {
                $.ceAjax('request', fn_url('tags.list?q=' + encodeURIComponent(extractLast(request.term))), {callback: function(data) {
                    response(data.autocomplete);
                }});
            }
        },

        initAutocompleteCity: function () {
            $autocompleteCity.on('focus', function (e) {
                if ($autocompleteCity.val() !== '') {
                    return;
                }
            }).on('input', function (e) {

            });

            $autocompleteCity.autocomplete({
                appendTo: "#litecheckout_autocomplete_dropdown",
                source: function (request, response) {
                   // var countryCode = $country.val();
                    var countryCode = "RU";


                    methods.autocompleteCity(request.term, countryCode, function(data) {
                        for (var i = 0; i < data.autocomplete.length; i++) {
                            data.autocomplete[i].label = data.autocomplete[i].value
                                + (data.autocomplete[i].state
                                        ? ' (' + data.autocomplete[i].state + ')'
                                        : ''
                                );
                        }
                        $autocompleteCity.data('caLiteCheckoutAutocompleteList', data.autocomplete);
                        response(data.autocomplete);
                    });
                },
                select: function (event, ui) {
                    event.preventDefault();
                    methods.setLocation(ui.item.value, ui.item.state_code, ui.item.state, ui.item.zipcode);
                }
            });
        },
    }


    $.extend({
        ceCpGeoDetection: function (method) {
            if (methods[method]) {
                return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
            } else {
                $.error('ty.ceCpGeoDetection: method ' + method + ' does not exist');
            }
        }
    });


///
  //  $.ceCpGeoDetection('init');
  //
    

})(Tygh, Tygh.$);
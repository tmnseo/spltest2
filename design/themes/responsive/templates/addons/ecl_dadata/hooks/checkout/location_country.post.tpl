{if !$smarty.capture.dadata_init && $addons.ecl_dadata.api_key}
<script type="text/javascript" class="cm-ajax-force">
$(document).ready(function(){
    var token = "{$addons.ecl_dadata.api_key}",
        type_address  = "ADDRESS",
        type_name  = "NAME",
        type_email = "EMAIL",
        $lc_name = $("input[name='user_data[fullname]']"),
        $lc_city = $('#litecheckout_city'),
        $lc_address = $('#litecheckout_s_address'),
        $lc_zipcode = $('#litecheckout_s_zipcode');
        $lc_state = $('#litecheckout_state'),
        $lc_state_i = $('#litecheckout_state_d'),
        $lc_country = $('#litecheckout_country'),
        $email = $("input[name*='email']");

    $email.suggestions({
        token: token,
        type: type_email,
        params: {
            parts: ["NAME"]
        }
    });
    $lc_name.suggestions({
        token: token,
        type: type_name,
    });
    if ($lc_state.prop('type') == 'text') {
        $lc_state.suggestions({
            token: token,
            type: type_address,
            hint: false,
            bounds: "region-area",
            constraints: {
                locations: { 
                    country: "*" 
                }
            }
        });
    }
    if ($lc_state_i.prop('type') == 'text') {
        $lc_state_i.suggestions({
            token: token,
            type: type_address,
            hint: false,
            bounds: "region-area",
            constraints: {
                locations: { 
                    country: "*" 
                }
            }
        });
    }
    $lc_city.suggestions({
        token: token,
        type: type_address,
        hint: false,
        bounds: "city-settlement",
        //constraints: $lc_state,
        constraints: {
            locations: { 
                country: "*" 
            }
        },
        onSelect: function(suggestion) {
            $(this).val(suggestion.data.city);
            fn_ecl_set_location(suggestion);
        }
    });
    $lc_address.suggestions({
        token: token,
        type: type_address,
        hint: false,
        onSelect: function(suggestion) {
            fn_ecl_set_location(suggestion);
        },
        formatSelected: function(suggestion) {
            return fn_join([
                suggestion.data.street_with_type,
                fn_join([suggestion.data.house_type, suggestion.data.house,
                      suggestion.data.block_type, suggestion.data.block], " "),
                fn_join([suggestion.data.flat_type, suggestion.data.flat], " ")
              ]);
        },
        constraints: {
            locations: {
                city: $lc_city.val()
            },
            //deletable: true
        }
    });
    
    {if $addons.ecl_dadata.geolocation == 'Y'}
    if ($lc_city.is(":visible") == true && $lc_address.val() == '') {
        $lc_city.suggestions().getGeoLocation().done(function(locationData) {
            var suggestion = {
                value: null,
                data: locationData
            };
            suggestion = fn_ecl_set_location(suggestion);
            $lc_city.suggestions().setSuggestion(suggestion);
        });
    }
    {/if}
    function fn_ecl_set_location(suggestion)
    {
        suggestion = fn_isofy(suggestion);
        if (suggestion.data.country_iso_code != null && suggestion.data.country_iso_code != $lc_country.val()) {
            $lc_country.val(suggestion.data.country_iso_code);
            $lc_country.trigger('change');
        }
        if (suggestion.data.region_iso_code != null) {
            $lc_state.val(suggestion.data.region_iso_code);
            $lc_state_i.val(suggestion.data.region_iso_code);
        } else if (suggestion.data.region) {
            if ($lc_state.prop('type') == 'text') {
                $lc_state.val(suggestion.data.region);
            }
            if ($lc_state_i.prop('type') == 'text') {
                $lc_state_i.val(suggestion.data.region);
            }
        }
        $lc_zipcode.val(suggestion.data.postal_code);
        $lc_city.val(suggestion.data.city);

        return suggestion;
    }
});

function fn_join(arr) {
    var separator = arguments.length > 1 ? arguments[1] : ", ";
    return arr.filter(function(n){
        return n
    }).join(separator);
}
function fn_isofy(suggestion) {
    var ISO_COUNTRIES = {
        "Россия": "RU",
        "Грузия": "GE",
        "Беларусь": "BY",
        "Казахстан": "KZ",
        "Украина": "UA"
    };
    var ISO_REGIONS = {
        "01": "AD", "02": "BA", "03": "BU", "04": "AL", "05": "DA", "06": "IN", "07": "KB", "08": "KL", "09": "KC", "10": "KR", "11": "KO", "12": "ME", "13": "MO", "14": "SA", "15": "SE", "16": "TA", "17": "TY", "18": "UD", "19": "KK", "20": "CE", "21": "CU", "22": "ALT", "23": "KDA", "24": "KYA", "25": "PRI", "26": "STA", "27": "KHA", "28": "AMU", "29": "ARK", "30": "AST", "31": "BEL", "32": "BRY", "33": "VLA", "34": "VGG", "35": "VLG", "36": "VOR", "37": "IVA", "38": "IRK", "39": "KGD", "40": "KLU", "41": "KAM", "42": "KEM", "43": "KIR", "44": "KOS", "45": "KGN", "46": "KRS", "47": "LEN", "48": "LIP", "49": "MAG", "50": "MOS", "51": "MUR", "52": "NIZ", "53": "NGR", "54": "NVS", "55": "OMS", "56": "ORE", "57": "ORL", "58": "PNZ", "59": "PER", "60": "PSK", "61": "ROS", "62": "RYA", "63": "SAM", "64": "SAR", "65": "SAK", "66": "SVE", "67": "SMO", "68": "TAM", "69": "TVE", "70": "TOM", "71": "TUL", "72": "TYU", "73": "ULY", "74": "CHE", "75": "ZAB", "76": "YAR", "77": "MOW", "78": "SPE", "79": "YEV", "83": "NEN", "86": "KHM", "87": "CHU", "89": "YAN"
    };
    var address = suggestion.data;

    address.country_iso_code = null;
    if (address.country in ISO_COUNTRIES) {
        address.country_iso_code = ISO_COUNTRIES[address.country];
    }

    address.region_iso_code = null;
    var region_id = address.region_kladr_id && address.region_kladr_id.substr(0, 2) || null;

    if (region_id in ISO_REGIONS) {
        address.region_iso_code = ISO_REGIONS[region_id];
    }
    suggestion.data = address;

    return suggestion;
}
</script>
{capture name="dadata_init"}Y{/capture}
{/if}
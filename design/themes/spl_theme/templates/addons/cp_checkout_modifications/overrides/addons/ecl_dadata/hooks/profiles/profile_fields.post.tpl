{if !$smarty.capture.dadata_init && $addons.ecl_dadata.api_key}
{$suggestion_kpp = __("kpp")}
{$suggestion_inn = __("inn")}

<script type="text/javascript" class="cm-ajax-force">
$(document).ready(function(){
    var token = "{$addons.ecl_dadata.api_key}",
        type_address  = "ADDRESS",
        type_name  = "NAME",
        type_email = "EMAIL",
        type_party  = "PARTY",
        $b_firstname = $("input[name*='b_firstname']"),
        $b_lastname = $("input[name*='b_lastname']"),
        $b_country = $("select[name*='b_country']"),
        $b_state = $("select[name*='b_state']"),
        $b_state_i = $("input[name*='b_state']"),
        $b_city   = $("input[name*='b_city']"),
        $b_address = $("input[name*='b_address']"),
        $b_address2 = $("input[name*='b_address_2']"),
        $b_zipcode = $("input[name*='b_zipcode']"),
        $s_firstname = $("input[name*='s_firstname']"),
        $s_lastname = $("input[name*='s_lastname']"),
        $s_country = $("select[name*='s_country']"),
        $s_state = $("select[name*='s_state']"),
        $s_state_i = $("input[name*='s_state']"),
        $s_city   = $("input[name*='s_city']"),
        $s_address = $("input[name*='s_address']"),
        $s_address2 = $("input[name*='s_address_2']"),
        $s_zipcode = $("input[name*='s_zipcode']"),
        
        $a_country = $('[data-field-name="a_country"]'),
        $a_state = $('[data-field-name="a_state"]'),
        $a_state_i = $('[data-field-name="a_state"]'),
        $a_city   = $('[data-field-name="a_city"]'),
        $a_address = $('[data-field-name="a_address"]'),
        $a_address2 = $('[data-field-name="a_address_2"]'),
        $a_zipcode = $('[data-field-name="a_zipcode"]'),
        
        /*cart-power gmelnikov modifs*/
        $lc_s_area = $('#elm_s_street'),
        $lc_s_street = $('#elm_s_street'),
        $lc_s_house = $('#elm_s_home'),
        $lc_s_flat = $('#elm_s_office'),
        $lc_s_block = $('#elm_s_corp'),
        
        $lc_b_area = $('#elm_b_area'),
        $lc_b_street = $('#elm_b_street'),
        $lc_b_house = $('#elm_b_home'),
        $lc_b_flat = $('#elm_b_office'),
        $lc_b_block = $('#elm_b_corp'),
        
        $lc_a_area = $('[data-field-name="a_area"]'),
        $lc_a_street = $('[data-field-name="a_street"]'),
        $lc_a_house = $('[data-field-name="a_home"]'),
        $lc_a_flat = $('[data-field-name="a_office"]'),
        $lc_a_block = $('[data-field-name="a_corp"]'),
        
        $company = $("input[name='user_data[company]']"),
        $lastname = $("input[name='user_data[lastname]']"),
        $firstname = $("input[name='user_data[firstname]']"), 
        
        $inn = $('[data-field-name="inn"]').not('[readonly]'),
        $kpp = $('[data-field-name="kpp"]'),
        $ogrn = $('[data-field-name="ogrn"]'),
        /*cart-power gmelnikov modifs*/
        $email = $("input[name*='email']");
    /*$email.suggestions({
        token: token,
        type: type_email,
        params: {
            parts: ["NAME"]
        }
    });*/
    $b_firstname.suggestions({
        token: token,
        type: type_name,
        params: {
            parts: ["NAME"]
        }
    });
    $b_lastname.suggestions({
        token: token,
        type: type_name,
        params: {
            parts: ["SURNAME"]
        }
    });
    $b_state_i.suggestions({
        token: token,
        type: type_address,
        hint: false,
        bounds: "region-area"
    });
    $b_city.suggestions({
        token: token,
        type: type_address,
        hint: false,
        bounds: "city-settlement",
        constraints: $b_state,
        onSelect: function(suggestion) {
            $(this).val(suggestion.data.city);
        }
    });

    {if $addons.ecl_dadata.geolocation == 'Y'}
    if ($b_city.is(":visible") == true && $b_city.val() == '') {
        $b_city.suggestions().getGeoLocation().done(function(locationData) {
            var suggestion = {
                value: null,
                data: locationData
            };
            suggestion = fn_isofy(suggestion);
            $b_city.suggestions().setSuggestion(suggestion);
            if (suggestion.data.country_iso_code != null && suggestion.data.country_iso_code != $b_country.val()) {
                $b_country.val(suggestion.data.country_iso_code);
                $b_country.trigger('change');
            }
            if (suggestion.data.region_iso_code != null) {
                $b_state.val(suggestion.data.region_iso_code);
                $b_state_i.val(suggestion.data.region_iso_code);
            }
            $b_city.val(suggestion.data.city == null ? suggestion.data.settlement : suggestion.data.city);
            $b_zipcode.val(suggestion.data.postal_code);
            $a_zipcode.val(suggestion.data.postal_code);
        });
    }
    {/if}

    $b_address.suggestions({
        token: token,
        type: type_address,
        hint: false,
        constraints: ($b_city.length && $b_city.val()) ? $b_city : {
            locations: { 
                country: "*" 
            }
        },
        onSelect: function(suggestion) {
            suggestion = fn_isofy(suggestion);
            //console.log(suggestion.data);
            if (suggestion.data.country_iso_code != null && suggestion.data.country_iso_code != $b_country.val()) {
                $b_country.val(suggestion.data.country_iso_code);
                $b_country.trigger('change');
            }
            if (suggestion.data.region_iso_code != null) {
                $b_state.val(suggestion.data.region_iso_code);
                $b_state_i.val(suggestion.data.region_iso_code);
            }
            $b_zipcode.val(suggestion.data.postal_code);
            $a_zipcode.val(suggestion.data.postal_code);
            $b_city.val(suggestion.data.city == null ? suggestion.data.settlement : suggestion.data.city);
            /*cart-power gmelnikov modifs*/
            if (suggestion.data.area != null) {
                $lc_b_area.val(suggestion.data.area);
            }  
            if (suggestion.data.street != null) {
                $lc_b_street.val(suggestion.data.street);
            }
            if (suggestion.data.house != null) {
                $lc_b_house.val(suggestion.data.house);
            }
            if (suggestion.data.flat != null) {
                $lc_b_flat.val(suggestion.data.flat);
            }
            if (suggestion.data.block != null) {
                $lc_b_block.val(suggestion.data.block);
            }
            /*cart-power gmelnikov modifs*/
            
            fn_cp_profile_readonly_toggle($b_address.parent().parent());
        },
        formatSelected: function(suggestion) {
            /*cart-power gmelnikov modifs*/
            /*
            return fn_join([
                suggestion.data.city_with_type,
                suggestion.data.street_with_type,
                fn_join([suggestion.data.house_type, suggestion.data.house,
                      suggestion.data.block_type, suggestion.data.block], " "),
                fn_join([suggestion.data.flat_type, suggestion.data.flat], " ")
            ]);
            */
            /*cart-power gmelnikov modifs*/
            
            return fn_join([
                suggestion.data.postal_code,
                suggestion.data.region_with_type,
                suggestion.data.area_with_type,
                suggestion.data.city_with_type,
                suggestion.data.street_with_type,
                fn_join([suggestion.data.house_type, suggestion.data.house,
                      suggestion.data.block_type, suggestion.data.block], " "),
                fn_join([suggestion.data.flat_type, suggestion.data.flat], " ")
            ]);
        }
    });
    $b_address2.suggestions({
        token: token,
        type: type_address,
        hint: false,
        bounds: "street-house",
        constraints: $b_city
    });
    $s_firstname.suggestions({
        token: token,
        type: type_name,
        params: {
            parts: ["NAME"]
        }
    });
    $s_lastname.suggestions({
        token: token,
        type: type_name,
        params: {
            parts: ["SURNAME"]
        }
    });
    $s_state_i.suggestions({
        token: token,
        type: type_address,
        hint: false,
        bounds: "region-area"
    });
    $s_city.suggestions({
        token: token,
        type: type_address,
        hint: false,
        bounds: "city-settlement",
        constraints: $s_state,
        onSelect: function(suggestion) {
            $(this).val(suggestion.data.city);
        }
    });
    {if $addons.ecl_dadata.geolocation == 'Y'}
    if ($s_city.is(":visible") == true && $s_city.val() == '') {
        $s_city.suggestions().getGeoLocation().done(function(locationData) {
            var suggestion = {
                value: null,
                data: locationData
            };
            suggestion = fn_isofy(suggestion);
            $s_city.suggestions().setSuggestion(suggestion);
            if (suggestion.data.country_iso_code != null && suggestion.data.country_iso_code != $s_country.val()) {
                $s_country.val(suggestion.data.country_iso_code);
                $s_country.trigger('change');
            }
            if (suggestion.data.region_iso_code != null) {
                $s_state.val(suggestion.data.region_iso_code);
                $s_state_i.val(suggestion.data.region_iso_code);
            }

            $s_city.val(suggestion.data.city == null ? suggestion.data.settlement : suggestion.data.city);
            $s_zipcode.val(suggestion.data.postal_code);
            $a_zipcode.val(suggestion.data.postal_code);
        });
    }
    {/if}
    $s_address.suggestions({
        token: token,
        type: type_address,
        hint: false,
        constraints: ($s_city.length && $s_city.val()) ? $s_city : {
            locations: { 
                country: "*" 
            }
        },
        onSelect: function(suggestion) {
            suggestion = fn_isofy(suggestion);
            if (suggestion.data.country_iso_code != null && suggestion.data.country_iso_code != $s_country.val()) {
                $s_country.val(suggestion.data.country_iso_code);
                $s_country.trigger('change');
            }
            if (suggestion.data.region_iso_code != null) {
                $s_state.val(suggestion.data.region_iso_code);
                $s_state_i.val(suggestion.data.region_iso_code);
            }
            $s_zipcode.val(suggestion.data.postal_code);
            $a_zipcode.val(suggestion.data.postal_code);
            $s_city.val(suggestion.data.city == null ? suggestion.data.settlement : suggestion.data.city);
            /*cart-power gmelnikov modifs*/
            if (suggestion.data.area != null) {
                $lc_s_area.val(suggestion.data.area);
            }  
            if (suggestion.data.street != null) {
                $lc_s_street.val(suggestion.data.street);
            }
            if (suggestion.data.house != null) {
                $lc_s_house.val(suggestion.data.house);
            }
            if (suggestion.data.flat != null) {
                $lc_s_flat.val(suggestion.data.flat);
            }
            if (suggestion.data.block != null) {
                $lc_s_block.val(suggestion.data.block);
            }
            /*cart-power gmelnikov modifs*/
            
            fn_cp_profile_readonly_toggle($s_address.parent().parent());
            
        },
        formatSelected: function(suggestion) {
            return fn_join([
                suggestion.data.street_with_type,
                fn_join([suggestion.data.house_type, suggestion.data.house,
                      suggestion.data.block_type, suggestion.data.block], " "),
                fn_join([suggestion.data.flat_type, suggestion.data.flat], " ")
              ]);
        }
    });
    $s_address2.suggestions({
        token: token,
        type: type_address,
        hint: false,
        bounds: "street-house",
        constraints: $s_city
    });
    
    $inn.suggestions({
        token: token,
        type: type_party,
        hint: false,
        formatResult: function (value, currentValue, suggestion, options) {
            var kpp,
                inn,
                name,
                kppTag = '',
                innTag = '',
                nameTag = '',
                kppData = suggestion.data.kpp,
                innData = suggestion.data.inn,
                nameData = suggestion.data.name.short_with_opf;
                
            if (nameData != undefined) { 
                var nameСonsilience = nameData.replace(currentValue, '<span class="suggestions-suggestion__consilience">' + '$&' + '</span>');
                if (nameСonsilience != 'null') {
                    name = nameСonsilience;
                } else {
                    name = nameData;
                } 
                nameTag = '<span class="suggestions-suggestion__name">' + name + '</span>';    
            }

            if (kppData != undefined) { 
                var kppСonsilience = kppData.replace(currentValue, '<span class="suggestions-suggestion__consilience">' + '$&' + '</span>');
                if (kppСonsilience != 'null') {
                    kpp = kppСonsilience;
                } else {
                    kpp = kppData;
                } 
                
                kppTag = '<span class="suggestions-suggestion__kpp">{$suggestion_kpp}: ' + kpp + '</span>';
            }

            if (innData != undefined) { 
                var innСonsilience = innData.replace(currentValue, '<span class="suggestions-suggestion__consilience">' + '$&' + '</span>');
                if (innСonsilience != 'null') {
                    inn = innСonsilience;
                } else {
                    inn = innData;
                }  
                innTag = '<span class="suggestions-suggestion__inn">{$suggestion_inn}: ' + inn + '</span>'; 
            }

            var hint = nameTag + kppTag + innTag;

            return hint;
        },
        onSelect: function(suggestion) {
            
            suggestion = fn_isofy(suggestion);
            //console.log(suggestion.data);
            
            const LEGAL = 'LEGAL';
            const INDIVIDUAL = 'INDIVIDUAL';
            const KPP_NULL = '----';
            var type = suggestion.data.type != null ? suggestion.data.type : LEGAL;
            
            // clear prev values
            var empty_val = "";
            var default_country = "RU";
            var default_state = "ALT";
            
            $kpp.val(empty_val);
            $ogrn.val(empty_val);
            $company.val(empty_val);
            //$lastname.val(empty_val);
            //$firstname.val(empty_val);
            $b_address.val(empty_val);
            $b_country.val(empty_val);
            $b_country.trigger('change');
            $b_state.val(empty_val);
            $b_state.trigger('change');
            $b_state_i.val(empty_val);
            $b_zipcode.val(empty_val);
            $b_city.val(empty_val);
            $lc_b_area.val(empty_val);
            $lc_b_street.val(empty_val);
            $lc_b_house.val(empty_val);
            $lc_b_flat.val(empty_val);
            $lc_b_block.val(empty_val);
            
            
            $kpp.attr('type', 'number');
            if (suggestion.data.kpp != null) {
                $kpp.val(suggestion.data.kpp);
            }
            else {
                if (type == INDIVIDUAL) {
                    $kpp.attr('type', 'text');
                    $kpp.val(KPP_NULL);
                }
            }
            
            if (suggestion.data.ogrn != null) {
                $ogrn.val(suggestion.data.ogrn);
            }
            
            if (suggestion.data.name != null) {
                if (suggestion.data.name.short_with_opf != null) {
                    $company.val(suggestion.data.name.short_with_opf);
                }
                else if (suggestion.data.name.short != null) {
                    $company.val(suggestion.data.name.short);
                }
                else if (suggestion.data.name.full != null) {
                    $company.val(suggestion.data.name.full);
                }
            }
            
            //INDIVIDUAL USER
            /*
            if (type == INDIVIDUAL && suggestion.data.name != null) {
                var full_name = suggestion.data.name.full;
                if (full_name) {
                    split_name = full_name.split(' ');
                    if (split_name[0].length > 0) {
                        $lastname.val(split_name[0]);
                    }  
                    if (split_name[1].length > 0) {
                        $firstname.val(split_name[1]);
                    }
                }
            }
            */
            
            //LEGAL COMPANY
            /*
            if (type == LEGAL && suggestion.data.management != null) {
                if (suggestion.data.management.name != null) {
                    var full_name = suggestion.data.management.name;
                    if (full_name) {
                        split_name = full_name.split(' ');
                        if (split_name[0].length > 0) {
                            $lastname.val(split_name[0]);
                        }  
                        if (split_name[1].length > 0) {
                            $firstname.val(split_name[1]);
                        }
                    }
                }
            }
            */
            
            if (suggestion.data.address != null) {
                /*
                var format_adress = fn_join([
                        suggestion.data.address.data.city_with_type,
                        suggestion.data.address.data.street_with_type,
                        fn_join([suggestion.data.address.data.house_type, suggestion.data.address.data.house,
                              suggestion.data.address.data.block_type, suggestion.data.address.data.block], " "),
                        fn_join([suggestion.data.address.data.flat_type, suggestion.data.address.data.flat], " ")
                ]);
                */
                
               var format_adress = fn_join([
                        suggestion.data.address.data.postal_code,
                        suggestion.data.address.data.region_with_type,
                        suggestion.data.address.data.area_with_type,
                        suggestion.data.address.data.city_with_type,
                        suggestion.data.address.data.street_with_type,
                        fn_join([suggestion.data.address.data.house_type, suggestion.data.address.data.house,
                              suggestion.data.address.data.block_type, suggestion.data.address.data.block], " "),
                        fn_join([suggestion.data.address.data.flat_type, suggestion.data.address.data.flat], " ")
                ]);
            
                if (format_adress != null && format_adress) {
                    $b_address.val(format_adress);
                    /*
                    $b_address.val(function(suggestion) {
                        return fn_join([
                            suggestion.data.address.data.city_with_type,
                            suggestion.data.address.data.street_with_type,
                            fn_join([suggestion.data.address.data.house_type, suggestion.data.address.data.house,
                                  suggestion.data.address.data.block_type, suggestion.data.address.data.block], " "),
                            fn_join([suggestion.data.address.data.flat_type, suggestion.data.address.data.flat], " ")
                        ]);
                    });
                    */
                }
                
                if (suggestion.data.address.data.country_iso_code != null && suggestion.data.address.data.country_iso_code != $b_country.val()) {
                    $b_country.val(suggestion.data.address.data.country_iso_code);
                    $b_country.trigger('change');
                }
                if (suggestion.data.address.data.region_iso_code != null) {
                    var region_iso_code = suggestion.data.address.data.region_iso_code;
                    if (suggestion.data.address.data.country_iso_code != null) {
                        var country_iso_code = suggestion.data.address.data.country_iso_code;
                        region_iso_code = region_iso_code.replace(country_iso_code + '-', '');
                    }
                    $b_state.val(region_iso_code);
                    $b_state_i.val(region_iso_code);
                }
                $b_zipcode.val(suggestion.data.address.data.postal_code);
                $a_zipcode.val(suggestion.data.address.data.postal_code);
                $b_city.val(suggestion.data.address.data.city == null ? suggestion.data.address.data.settlement : suggestion.data.address.data.city);
                if (suggestion.data.address.data.area != null) {
                    $lc_b_area.val(suggestion.data.address.data.area);
                }  
                if (suggestion.data.address.data.street != null) {
                    $lc_b_street.val(suggestion.data.address.data.street);
                }
                if (suggestion.data.address.data.house != null) {
                    $lc_b_house.val(suggestion.data.address.data.house);
                }
                if (suggestion.data.address.data.flat != null) {
                    $lc_b_flat.val(suggestion.data.address.data.flat);
                }
                if (suggestion.data.address.data.block != null) {
                    $lc_b_block.val(suggestion.data.address.data.block);
                }

            }
            
            fn_cp_profile_readonly_toggle($inn.parent().parent());

        },
        formatSelected: function(suggestion) {
            showFieldsProfile();
            return suggestion.data.inn;
        }
    });
    
    /*$a_state_i.suggestions({
        token: token,
        type: type_address,
        hint: false,
        bounds: "region-area"
    });*/
    $a_city.suggestions({
        token: token,
        type: type_address,
        hint: false,
        bounds: "city-settlement",
        constraints: $a_state,
        onSelect: function(suggestion) {
            $(this).val(suggestion.data.city);
        }
    });

    {if $addons.ecl_dadata.geolocation == 'Y'}
    if ($a_city.is(":visible") == true && $a_city.val() == '') {
        $a_city.suggestions().getGeoLocation().done(function(locationData) {
            var suggestion = {
                value: null,
                data: locationData
            };
            suggestion = fn_isofy(suggestion);
            $a_city.suggestions().setSuggestion(suggestion);
            if (suggestion.data.country_iso_code != null && suggestion.data.country_iso_code != $a_country.val()) {
                $a_country.val(suggestion.data.country_iso_code);
                $a_country.trigger('change');
            }
            if (suggestion.data.region_iso_code != null) {
                $a_state.val(suggestion.data.region_iso_code);
                $a_state_i.val(suggestion.data.region_iso_code);
            }
            $a_city.val(suggestion.data.city == null ? suggestion.data.settlement : suggestion.data.city);
            $a_zipcode.val(suggestion.data.postal_code);
        });
    }
    {/if}
    $lc_a_street.suggestions({
        token: token,
        type: type_address,
        hint: false,
        bounds: "street-house",
        constraints: ($a_city.length && $a_city.val()) ? $a_city : {
            locations: { 
                country: "*" 
            }
        },
        onSelect: function(suggestion) {
            suggestion = fn_isofy(suggestion);
            if (suggestion.data.street != null) {
                $(this).val(suggestion.data.street);
            }
            if (suggestion.data.house != null) {
                $lc_a_house.val(suggestion.data.house);
            }
            if (suggestion.data.flat != null) {
                $lc_a_flat.val(suggestion.data.flat);
            }
        }
    });
    $a_address.suggestions({
        token: token,
        type: type_address,
        hint: false,
        constraints: ($a_city.length && $a_city.val()) ? $a_city : {
            locations: { 
                country: "*" 
            }
        },
        onSelect: function(suggestion) {
            suggestion = fn_isofy(suggestion);
            //console.log(suggestion.data);
            if (suggestion.data.country_iso_code != null && suggestion.data.country_iso_code != $a_country.val()) {
                $a_country.val(suggestion.data.country_iso_code);
                $a_country.trigger('change');
            }
            if (suggestion.data.region_iso_code != null) {
                $a_state.val(suggestion.data.region_iso_code);
                $a_state_i.val(suggestion.data.region_iso_code);
            }
            $a_zipcode.val(suggestion.data.postal_code);
            $a_city.val(suggestion.data.city == null ? suggestion.data.settlement : suggestion.data.city);
            /*cart-power gmelnikov modifs*/
            if (suggestion.data.area != null) {
                $lc_a_area.val(suggestion.data.area);
            }  
            if (suggestion.data.street != null) {
                $lc_a_street.val(suggestion.data.street);
            }
            if (suggestion.data.house != null) {
                $lc_a_house.val(suggestion.data.house);
            }
            if (suggestion.data.flat != null) {
                $lc_a_flat.val(suggestion.data.flat);
            }
            if (suggestion.data.block != null) {
                $lc_a_block.val(suggestion.data.block);
            }
            /*cart-power gmelnikov modifs*/
            
            fn_cp_profile_readonly_toggle($a_address.parent().parent());

        },
        formatSelected: function(suggestion) {
            /*cart-power gmelnikov modifs*/           
            return fn_join([
                suggestion.data.city_with_type,
                suggestion.data.street_with_type,
                fn_join([suggestion.data.house_type, suggestion.data.house,
                      suggestion.data.block_type, suggestion.data.block], " "),
                fn_join([suggestion.data.flat_type, suggestion.data.flat], " ")
            ]);
            /*cart-power gmelnikov modifs*/
        }
    });
    $a_address2.suggestions({
        token: token,
        type: type_address,
        hint: false,
        bounds: "street-house",
        constraints: $a_city
    });
     
});

function fn_cp_profile_readonly_toggle(context) {
    
    $('[readonly][data-edit-if-empty]', context).each(function() {
        if ($(this).val() == "") {
            $(this).attr("readonly", false); 
        }
    });

    $('[data-edit-if-empty]', context).each(function() {
        if ($(this).val() != "") {
            $(this).attr("readonly", true); 
        }
    });
}

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
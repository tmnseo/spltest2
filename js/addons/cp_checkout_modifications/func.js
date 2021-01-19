(function(_, $){

    $(document).ready(function(){
        cp_initialization_dadata();
    });
    $.ceEvent('on', 'ce.ajaxdone', function(elms, inline_scripts, params, data) {
        cp_initialization_dadata();
    });
    function cp_initialization_dadata()
    {
        dadata_api_key = $('#dadata_api_key').val();
        var token = dadata_api_key,
            type_address  = "ADDRESS",
            $lc_address = $('#litecheckout_s_address'),
            $lc_city = $('#litecheckout_s_city'),
            $lc_street = $('#litecheckout_s_street'),
            $lc_house = $('#litecheckout_s_home'),
            $lc_block = $('#litecheckout_s_corp');
            $lc_zipcode = $('#litecheckout_s_zipcode');

        $lc_city.focus(function(){
            $lc_city.suggestions({
            token: token,
            type: type_address,
            hint: false,
            bounds: "city",
            constraints: {
                locations: { 
                    country: "*" 
                }
            },
            onSelect: function(suggestion) {
                $(this).val(suggestion.data.city);
                cp_update_city(suggestion.data);
            }
            });    
        });    
        $lc_street.focus(function(){
            $lc_street.suggestions({
                token: token,
                type: type_address,
                hint: false,
                bounds: "street",
                constraints: {
                    locations: { 
                        city: $lc_city.val() 
                    }
                },
                onSelect: function(suggestion) {
                    $(this).val(suggestion.data.street);
                    $lc_address.val(suggestion.data.street);
                    $lc_zipcode.val(suggestion.data.postal_code);
                },
            });
        });
        $lc_house.focus(function(){
            $lc_house.suggestions({
                token: token,
                type: type_address,
                hint: false,
                bounds: "house",
                constraints: {
                    locations: {
                        city: $lc_city.val(),
                        street: $lc_street.val()  
                    }
                },
                onSelect: function(suggestion) {
                    $(this).val(suggestion.data.house);
                    $lc_address.val(suggestion.data.street + " " + suggestion.data.house);
                    $lc_block.val(suggestion.data.block);
                    $lc_zipcode.val(suggestion.data.postal_code);
                }
            });
        });
    }
    function cp_update_city(data){
        s_state = data.region_iso_code.replace(data.country_iso_code + '-', '');
        $.ceAjax('request', fn_url('checkout.update_steps'), {
            method: 'post',
            result_ids: 'litecheckout_form,checkout_info*,checkout_order_info*',
            full_render: "Y",
            data:{
                user_data:{
                    s_city: data.city,
                    s_country: data.country_iso_code, 
                    s_state: s_state,
                    s_zipcode: data.postal_code,   
                },
                
            }
        });
    }
})(Tygh, Tygh.$);
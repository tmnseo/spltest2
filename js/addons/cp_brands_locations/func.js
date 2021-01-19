function cp_get_existed_destinations(elem)
{
    var variant_id = $(elem).val();
    var company_id = $("input[name=company_id]").val();

    $.ceAjax('request', fn_url("cp_brands_locations.get_current_locations"), {
        data: {
            variant_id: variant_id,
            company_id: company_id,
            result_ids: 'cp_locations_' + company_id
        },
        hidden: false,
        method: 'get',
        callback: function (response) {

        }
    });
}
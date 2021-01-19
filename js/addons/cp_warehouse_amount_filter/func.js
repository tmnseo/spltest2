function fn_cp_change_filter_amount(value)
{	
	var filert_uid = $(".warehouses_amount").data("ca-filter-uid");
	$("#elm_checkbox_warehouses_amount_"+filert_uid).val(value);
	$("#elm_checkbox_warehouses_amount_"+filert_uid).change();
}
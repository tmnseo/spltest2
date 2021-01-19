(function(_, $){
	
	$("#elm_start_worktime").focusout(function() {
		fn_cp_set_restrictions('#elm_start_worktime', '#elm_end_worktime', 'min');
	});
	$("#elm_end_worktime").focusout(function() {
		fn_cp_set_restrictions('#elm_end_worktime', '#elm_start_worktime', 'max');
	});


	$.ceEvent('on', 'ce.commoninit', function(context) {

		fn_cp_set_default_restrictions();

		$("#elm_start_worktime_day").focusout(function() {
			fn_cp_set_restrictions('#elm_start_worktime_day', '#elm_end_worktime_day', 'min');
		});
		$("#elm_end_worktime_day").focusout(function() {
			fn_cp_set_restrictions('#elm_end_worktime_day', '#elm_start_worktime_day', 'max');
		});

		$checked_div = $(".cp_time_checker");

		if ($checked_div.length) {
			fn_cp_set_popup_restrictions($checked_div);
		}
	});

	function fn_cp_set_restrictions(elem, elem_for_set, type_of_restriction)
	{
		var time = $(elem).val();
		if (type_of_restriction == 'min') {
			$(elem_for_set).attr({
				'min' : time
			});
		}else if (type_of_restriction == 'max') {
			$(elem_for_set).attr({
				'max' : time
			});
		}
	}

	function fn_cp_set_default_restrictions()
	{	
		var start_element = "#elm_start_worktime";
		var end_element = "#elm_end_worktime";

		var start_element_day = "#elm_start_worktime_day";
		var end_element_day = "#elm_end_worktime_day";

		if ($(start_element).length && $(end_element).length) {
			
			fn_cp_set_restrictions(start_element, end_element, 'min');
			fn_cp_set_restrictions(end_element, start_element, 'max');
		}

		if ($(start_element_day).length && $(end_element_day).length) {

			fn_cp_set_restrictions(start_element_day, end_element_day, 'min');
			fn_cp_set_restrictions(end_element_day, start_element_day, 'max');

		}
	}

	function fn_cp_set_popup_restrictions($div)
	{	
		

		$($div).each(function(div_key, div_elem) {
			
			$selectedElements = $(div_elem).find("input");

			$($selectedElements).each(function(index, elem) {

				if (index == 0) {
					$start_time_elem = elem;
					$(".cp-start").focusout(function(event) {

						if (elem == event.target) {
							fn_cp_set_restrictions(event.target, $(div_elem).find("input")[1], 'min');
						}
					});
				}else if (index == 1) {
					$end_time_elem = elem;
					$(".cp-end").focusout(function(event) {
						if (elem == event.target) {
							fn_cp_set_restrictions(event.target, $(div_elem).find("input")[0], 'max');
						}
					});
				}

			});

			if ($($start_time_elem).length && $($end_time_elem).length) {
			
				fn_cp_set_restrictions($start_time_elem, $end_time_elem, 'min');
				fn_cp_set_restrictions($end_time_elem, $start_time_elem, 'max');
			}
			
		});
	}


})(Tygh, Tygh.$);

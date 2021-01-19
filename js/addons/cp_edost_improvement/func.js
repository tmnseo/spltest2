function fn_cp_check_order_statuses(obj, status, id)
{
	controller_checker = 'cp_edost_improvement.size_checker_popup';
	change_status_href = $(obj).attr('href');

	url = change_status_href.replace('orders.update_status', controller_checker);

	$finder_dialog = $('#cp_size_before_completed_dialog_' + id);

    $finder_dialog.ceDialog('destroy');
    $finder_dialog.empty();
    
    $finder_dialog.ceDialog('open', {
    	'width': 'auto',
    	'height': 'auto',
    	'destroyOnClose': true,
        'href': url,
    });
}

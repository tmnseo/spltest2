(function(_, $){
	$.ceEvent('on', 'ce.ajaxdone', function(elms, inline_scripts, params, data) {
		if (data.update_confirm_status != undefined && data.order_id != undefined && data.result == 1) {
			var order_id = data.order_id;
            var status = data.update_confirm_status;
            var swObj = $('#sw_select_' + order_id + '_wrap');
            var obj = swObj.siblings('.dropdown-menu').find('.status-link-' + status);

            if (obj.hasClass('cm-promo-popup')) {
                return false;
            }

            if (obj.hasClass('active')) {
                obj.removeClass('cm-ajax').removeClass('cm-post');
                return false;
            }

            var upd_elm = obj.parents('.cm-popup-box:first');
            var button_elm = $('a:first', upd_elm);

            if (obj.prop('href')) {
                obj.prop('href', fn_query_remove(obj.prop('href'), ['notify_user', 'notify_department']));

                $('input[name^=__notify_]:checked', upd_elm).each(function(e) {
                    var name = $(this).attr('name').substr(2);
                    obj.prop('href', obj.prop('href') + '&' + name + '=Y');
                });

                $('li a:not(:has([type=checkbox]))', upd_elm).addClass('cm-ajax').addClass('cm-post');

            } else if (obj.data('caResultId')) {
                $('input[id="' + obj.data('caResultId') + '"]').val(status.toUpperCase());
            }

            var p_active = $('li a.active', upd_elm);
            if (p_active.length) {
                var p_status = p_active.prop('class').match(/status-link-([^\s]+)/i);

                var row = upd_elm.parents('.cm-row-status-' + p_status[1] + ':first');
                if (row.length) {
                    row.removeClass('cm-row-status-' + p_status[1]);
                    row.addClass('cm-row-status-' + status);
                }

                var ch_item = $('.cm-item-status-' + p_status[1], upd_elm.parents('tr:first'));
                if (ch_item.length) {
                    ch_item.removeClass('cm-item-status-' + p_status[1]);
                    ch_item.addClass('cm-item-status-' + status);
                }

                if (swObj.hasClass('o-status-' + p_status[1])) {
                	swObj.removeClass('o-status-' + p_status[1]).addClass('o-status-' + status);
                }
            }

            $('li a', upd_elm).removeClass('active');
            $('li', upd_elm).removeClass('disabled');

            $('.status-link-' + status, upd_elm).addClass('active');
            $('.status-link-' + status, upd_elm).parents('li:first').addClass('disabled');

            button_elm.html($('.status-link-' + status, upd_elm).text() + ' <span class="caret"></span>');
		}
	});
})(Tygh, Tygh.$);
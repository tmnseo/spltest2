(function (_, $) {
    $(_.doc).on('click', '#cp_matrix_filter_days_encrease', function (e) {
        cp_qty_recalculate($(this));
    });
    function cp_qty_recalculate(jelm) {

         if ((jelm.is('a.cm-cp-increase')  || jelm.parents('a.cm-cp-increase').length) && jelm.parents('.cm-value-changer').length) {

            var inp = $('input', jelm.closest('.cm-value-changer')),
                step = 1,
                min_qty = 0,
                currentValue = inp.val();

            if (inp.attr('data-ca-step')) {
                step = parseInt(inp.attr('data-ca-step'));
            }

            //cart-power lemuria
            if (inp.data('caMaxQty')) {
                max_qty = parseInt(inp.data('caMaxQty'));
            }

            //var new_val = parseInt(inp.val()) + ((jelm.is('a.cm-cp-increase') || jelm.parents('a.cm-cp-increase').length) ? step : -step),

             var new_val = parseInt(inp.val());


             newValue = new_val+step <= max_qty ? new_val+step : max_qty;
            inp.val(newValue);
            inp.keypress();
        }
    }
})(Tygh, Tygh.$);
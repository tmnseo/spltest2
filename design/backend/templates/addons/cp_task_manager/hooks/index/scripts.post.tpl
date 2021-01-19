<script type="text/javascript">
    (function(_, $) {
        _.tr({
            'error_minutes': '{__("cp_error_minutes")|escape:"javascript"}',
            'error_hours': '{__("cp_error_hours")|escape:"javascript"}',
            'error_days': '{__("cp_error_days")|escape:"javascript"}',
            'error_months': '{__("cp_error_months")|escape:"javascript"}',
            'error_dws': '{__("cp_error_dws")|escape:"javascript"}'
        });
        $.ceFormValidator('registerValidator', {
            class_name: 'cm-value-factory-minutes-lbl',
            message: _.tr('error_minutes'),
            func: function(id) {
                var input = $('#' + id);
                var elm_val = $.trim(input.val());
                
                return ({$smarty.const.REGEXP_MINUTES}.test(elm_val) && elm_val != "") ? true : false;

            }
        });
        $.ceFormValidator('registerValidator', {
            class_name: 'cm-value-factory-hours-lbl',
            message: _.tr('error_hours'),
            func: function(id) {
                var input = $('#' + id);
                var elm_val = $.trim(input.val());
               
                return ({$smarty.const.REGEXP_HOURS}.test(elm_val) && elm_val != "") ? true : false;

            }
        });
        $.ceFormValidator('registerValidator', {
            class_name: 'cm-value-factory-days-lbl',
            message: _.tr('error_days'),
            func: function(id) {
                var input = $('#' + id);
                var elm_val = $.trim(input.val());
               
                return ({$smarty.const.REGEXP_DAYS}.test(elm_val) && elm_val != "") ? true : false;

            }
        });
        $.ceFormValidator('registerValidator', {
            class_name: 'cm-value-factory-months-lbl',
            message: _.tr('error_months'),
            func: function(id) {
                var input = $('#' + id);
                var elm_val = $.trim(input.val());
               
                return ({$smarty.const.REGEXP_MONTHS}.test(elm_val) && elm_val != "") ? true : false;

            }
        });
        $.ceFormValidator('registerValidator', {
            class_name: 'cm-value-factory-dws-lbl',
            message: _.tr('error_dws'),
            func: function(id) {
                var input = $('#' + id);
                var elm_val = $.trim(input.val());
               
                return ({$smarty.const.REGEXP_DWS}.test(elm_val) && elm_val != "") ? true : false;

            }
        });
    }(Tygh, Tygh.$));
</script>

{if $runtime.controller == 'tasks'}
{script src="js/addons/cp_task_manager/cp_task_manager.js"}
{/if}

(function(_, $){
    $.ceEvent('on', 'ce.commoninit', function(context) {

        var minutes_elements = context.find('.cm-value-factory-minutes');
        var hours_elements = context.find('.cm-value-factory-hours');
        var days_elements = context.find('.cm-value-factory-days');
        var months_elements = context.find('.cm-value-factory-months');
        var dws_elements = context.find('.cm-value-factory-dws');

        if (minutes_elements.length === 0) {
            return true;
        }
        if (hours_elements.length === 0) {
            return true;
        }
        if (days_elements.length === 0) {
            return true;
        }
        if (months_elements.length === 0) {
            return true;
        }
        if (dws_elements.length === 0) {
            return true;
        }
    });
    
    function fn_cp_task_manager_get_server_time()  
    { 

        var url = fn_url('tasks.get_server_time');
        $.ceAjax('request', url, {
            result_ids: 'server_time',
            force_exec: true,
            cache: false,  
            method: 'get'
        });
    }
    
      
    $(document).ready(function(){
        setInterval(fn_cp_task_manager_get_server_time, 60 * 1000);  
    });
})(Tygh, Tygh.$);


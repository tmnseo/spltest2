$(function(){
    $.mask.definitions['r']='[А-Яа-я]';
    var mask = $("#cp_worktime_mask").val()
    if (mask !== null) {
        $("#elm_pickup_work_time").mask(mask);
    } 
});
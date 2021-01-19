function fn_cp_catalog_changes_search_by_q(elm, val) {
    var input = $(elm).parents('.ty-search-block').find('input[type="text"].ty-search-block__input');
    var submit = $(elm).parents('.ty-search-block').find('button[type="submit"].ty-search-magnifier');
    input.val(val);
    submit.click();
}
$(document).mouseup(function (e) {
    var container = $('#live_reload_box').children();
    if (container.has(e.target).length === 0){
        container.parents('.ty-search-block').find('input[type="text"].ty-search-block__input').removeClass('cp-live-search-input');
    }
});
function fn_cp_set_pname_params(value, elm)
{
    $("input[name='is_pname_search']").val(value);
    tabs = $(elm).siblings();
    
    tabs.each(function(e, child_elm) {
        $(child_elm).removeClass('cp-selected');
    });
    
    $(elm).addClass('cp-selected');

    search_elm = $('#search_input');
    id_class = $(search_elm).attr('id');
    var id = id_class.replace("search_input", "");

    search_title = $(search_elm).attr("title")

    if (search_title == search_elm.val()){
        $(search_elm).val('');
        $(search_elm).removeClass('cm-hint');
    }
    if (value == 'N') {
        var reg = /[а-яА-ЯёЁ]/g;
        if ($(search_elm).val().search(reg) !=  -1) {
            $(search_elm).val($(search_elm).val().replace(reg, ''));
        }
    }

    $(search_elm).focus();

    ls_go_search(search_elm, id);
}
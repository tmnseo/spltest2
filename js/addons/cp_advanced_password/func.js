(function(_, $){
    
    $.ceEvent('on', 'ce.formpre_profile_form', function (form, clicked_elm) {
        if (_.area === 'C' || (_.area === 'A' && _.cp_advanced_password.settings.use_in_admin_panel == true)) {

            var pass_elm = $("[name='user_data[password1]']");
            var lbl_elm = $("[for='password1']");
            var pass_length = pass_elm.val().length;
            var min_pass_length = _.cp_advanced_password.settings.pass_min_length;
            var is_err = false;
            var errs = [];

            $(".cp-error-mess").remove();
            $(lbl_elm.parent()).removeClass('cp-password-error');
            
            if (!_cpIsEmpty(pass_elm.val())) {

                if (pass_length < min_pass_length) {

                    is_err = true;
                    lang_var = _.tr('cp_advanced_password.error_pass_length').str_replace('[pass_min_length]', min_pass_length);
                    errs.push(lang_var);
                }
                if (_.cp_advanced_password.settings.pass_numbers == true) {

                    var numbers = pass_elm.val().match(/\d{1,}/);

                    if (numbers == null) {
                        is_err = true;
                        lang_var = _.tr('cp_advanced_password.use_pass_numbers');
                        errs.push(lang_var); 
                    }
                }
                if (_.cp_advanced_password.settings.pass_upper == true) {

                    if (_cpHasUpperCase(pass_elm.val()) == false) {

                        is_err = true;
                        lang_var = _.tr('cp_advanced_password.use_pass_upper');
                        errs.push(lang_var);
                    }
                }
                if (_.cp_advanced_password.settings.pass_sumbols == true) {

                    if (_cpHasSumbols(pass_elm.val()) == false) {

                        is_err = true;
                        lang_var = _.tr('cp_advanced_password.use_pass_sumbols');
                        errs.push(lang_var);
                    }
                }

                if (is_err == true) {
                    _cpShowErrorMess(pass_elm.parent(), pass_elm, lbl_elm, errs);
                    return false;
                }
            }
        }   
    });
    function _cpShowErrorMess(parent_elm, pass_elm, lbl_elm, errs)
    {   
        
        if (_.area == 'C') {
            parent_elm.addClass('cp-password-error');
        }else if (_.area == 'A') {
            parent_elm.parent().addClass('cp-password-error');
        }
        _cpAddErrMess(parent_elm, errs);
        _cpScrollToElm(pass_elm);
        
    }   
    
})(Tygh, Tygh.$);
 
function _cpAddErrMess(parent_elm, errs)
{   
    parent_elm.append(
        "<span class='help-inline cp-error-mess'></span>"
    );
    $.each(errs,function(indx, element) {
        $(".cp-error-mess").append(
            "<p>"+ element + "</p>"
        );
    });
}
function _cpScrollToElm(elm)
{
    $('html, body').animate({
        scrollTop: elm.offset().top
    }, 1000);
}
function _cpHasUpperCase(str) {
    
    if(str.toLowerCase() != str) {
        return true;
    }
    return false;
}
function _cpHasSumbols(str){

    var regex = /[ !@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/g;
    return regex.test(str);
}
function _cpIsEmpty(str) {
  if (str.trim() == '') 
    return true;
    
  return false;
}

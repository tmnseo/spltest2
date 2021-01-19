(function(_, $){
    $('.cp-special-input').change(function(){

         if ($(this).prop('checked') !== true) {
            $('.cp-special-input').removeAttr('checked');
         } else {
            $('.cp-special-input').removeAttr('checked');
            $(this).prop('checked', true);
         }
          
    });
})(Tygh, Tygh.$);


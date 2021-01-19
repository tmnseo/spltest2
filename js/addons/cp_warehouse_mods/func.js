(function(_, $){
    function fn_cp_add_product_to_cart_by_warehouses(product_id,warehouse_id){
        $('#warehouse_id_'+product_id).val(warehouse_id);
        $("#product_form_"+product_id).submit();
    }
})(Tygh, Tygh.$);

function fn_cp_add_product_to_cart_by_warehouse(product_id,warehouse_id){
   $('#warehouse_id_'+product_id).val(warehouse_id);
   
   // $("#product_form_"+product_id).submit();

   $('form[name="product_form_'+product_id+'"]').submit();
}
function fn_cp_set_warehouse_id_and_amount(product_id,warehouse_id){
  
   $('#warehouse_id_'+product_id).val(warehouse_id);
   var warehouse_amount = $('#qty_count_'+product_id+'_'+warehouse_id).val();
   if (warehouse_amount !== null) {
      $('#cp_qty_count_'+product_id).val(warehouse_amount);
   }
}
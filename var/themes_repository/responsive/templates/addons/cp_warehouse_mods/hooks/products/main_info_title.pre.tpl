{if $product}
   {if !$cp_warehouse_id}
    <input type="hidden" value="" id="warehouse_id_{$product.product_id}" name="product_data[{$obj_id}][extra][warehouse_id]">
    <input type="hidden" value="" id="cp_qty_count_{$product.product_id}" name="product_data[{$obj_id}][amount]">
   {/if}
{/if}
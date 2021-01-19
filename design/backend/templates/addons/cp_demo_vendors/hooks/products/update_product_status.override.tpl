{hook name="products:update_product_status"}
{if !fn_cp_check_demo_mode($product_data.company_id)} 
	{$non_editable_status = false}
{else}
	{$non_editable_status = true}
{/if}
{include file = "views/products/components/status_on_update.tpl"
    input_name = "product_data[status]"
    id = "elm_product_status"
    obj = $product_data
    hidden = true
    non_editable_status = $non_editable_status
}
{/hook}
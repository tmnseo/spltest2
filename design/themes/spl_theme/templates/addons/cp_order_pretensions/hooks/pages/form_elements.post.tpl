{if $element.element_type == $smarty.const.CP_FORM_PRODUCTS}
	<input type="hidden" name="order_id" value="{$order_info.order_id}">
	<table class="cp-form__poducts">
    {foreach from=$products key=product_id item=product_data}
    	<tr class="cp-form__poducts-item">
    		<td class="cp-form__poducts-item-checkbox">
				<input class="cp-form__poducts-checkbox" type="checkbox" name="cp_data[product_ids][]" value="{$product_data.product_id}" id="cp_data_poduct_{$product_data.product_id}"/>
				<span class="cp-form__poducts-checkbox_pseudo">
			</td>
    		<td class="cp-form__poducts-item-name">
				<label for="cp_data_poduct_{$product_data.product_id}" >{$product_data.product}</label>
			</td>
    		<td class="cp-form__poducts-item-amount">
				{$product_data.amount}&nbsp;{__("items")}
			</td>
    		<td class="cp-form__poducts-item-price">
				{include file="common/price.tpl" value=$product_data.price}
			</td>
    	</tr>
    {/foreach}
    </table>
{elseif $element.element_type == $smarty.const.CP_FORM_PRETENSION_DESCR}
	<textarea id="elm_{$element.element_id}" class="ty-form-builder__textarea" name="cp_data[pretension_description]" cols="67" rows="10"></textarea>
{elseif $element.element_type == $smarty.const.CP_FORM_MULTIUPLOADER}
	<div class="cp-form__fileuploader">
		{script src="js/tygh/fileuploader_scripts.js"}
		{include file="common/fileuploader.tpl" var_name="fb_files[`$element.element_id`]" multiupload="Y" upload_file_text=$element.description upload_another_file_text=$element.description} 
	</div>
{/if}
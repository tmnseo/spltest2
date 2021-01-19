{if !$smarty.capture.dadata_init && $addons.ecl_dadata.api_key}
<input type="hidden" id="dadata_api_key" value="{$addons.ecl_dadata.api_key}">
{script src="js/addons/cp_checkout_modifications/func.js"}
{capture name="dadata_init"}Y{/capture}
{/if}


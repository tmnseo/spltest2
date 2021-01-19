{if $smarty.request.success_registration == "Y"}
	<div class="hidden cm-dialog-auto-open cm-dialog-auto-size page-popup" id="cp_after_vend_reg_dialog" data-ca-dialog-class="success-registration-popup">
		{include file="addons/cp_popup_notifications/components/succes_registration_popup.tpl" type="V"}
	</div>
{elseif $smarty.request.success_customer_registration == "Y"}
	<div class="hidden cm-dialog-auto-open cm-dialog-auto-size page-popup" id="cp_after_user_reg_dialog" data-ca-dialog-class="success-registration-popup">
		{include file="addons/cp_popup_notifications/components/succes_registration_popup.tpl" type="C"}
	</div>
{/if}
<div class="success-registration-popup__banner">
	<img src="images/banner/sofi.png" width="391" height="247">
</div>
<div class="success-registration-popup__content">
{if $type == "C"}
	<div class="success-registration-popup__desc">
		{include file="common/subheader.tpl" title=__("cp_popup_notifications.user_popup_header_text")}
		<p class="success-registration-popup__text">{__("cp_popup_notifications.user_popup_text")}</p>
	</div>
	<div class="success-registration-popup__buttons">
		{include file="buttons/button.tpl" but_href=""|fn_url but_text=__("cp_popup_notifications.great") but_role="action" but_meta="ty-btn__secondary cm-form-dialog-closer"}
		<a class="ty-btn ty-btn__tertiary cm-form-dialog-closer visible-phone" href="https://support.service.parts/" target="_blank">
			{__("cp_popup_notifications.knowledge_base")}
		</a>
	</div>
{elseif $type == "V"}
	<div class="success-registration-popup__desc">
		{include file="common/subheader.tpl" title=__("cp_popup_notifications.vendor_popup_header_text")}
		<p class="success-registration-popup__text">{__("cp_popup_notifications.vendor_popup_text")}</p>
	</div>
	<div class="success-registration-popup__buttons">
		{include file="buttons/button.tpl" but_href=""|fn_url but_text=__("cp_popup_notifications.to_home_page") but_role="action" but_meta="ty-btn__secondary cm-form-dialog-closer ty-btn"}
		<a class="ty-btn ty-btn__tertiary cm-form-dialog-closer" href="https://support.service.parts/" target="_blank">
			{__("cp_popup_notifications.knowledge_base")}
		</a>
	</div>
{/if}
</div>
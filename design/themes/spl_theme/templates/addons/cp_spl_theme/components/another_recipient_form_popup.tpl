<div class="cp-recipient-popup__content" id="cp_recipient_popup">
	<form class="cm-ajax cm-ajax-full-render" name="recipient_form" action="{""|fn_url}" method="get">
		<input type="hidden" name="result_ids" value="cp_recipient_info" />
		<input type="hidden" name="full_render" value="1" />
		<div class="ty-control-group ty-profile-field__item">
		    <label for="recipient_firstname" class="ty-control-group__title">{__("first_name")}</label>  
		    <input type="text" id="recipient_firstname" name="recipient_data[firstname]" class="ty-input-text"/>
	    </div>
		<div class="ty-control-group ty-profile-field__item">
		    <label for="recipient_lastname" class="ty-control-group__title">{__("last_name")}</label>  
		    <input type="text" id="recipient_lastname" name="recipient_data[lastname]" class="ty-input-text"/>
	    </div>
		<div class="ty-control-group ty-profile-field__item">
		    <label for="recipient_middlename" class="ty-control-group__title">{__("cp_middle_name")}</label>  
		    <input type="text" id="recipient_middlename" name="recipient_data[middlename]" class="ty-input-text"/>
	    </div>
		<div class="ty-control-group ty-profile-field__item">
		    <label for="recipient_phone" class="ty-control-group__title cm-mask-phone-label">{__("phone")}</label>  
		    <input type="text" id="recipient_phone" name="recipient_data[phone]" class="ty-input-text cm-mask-phone">
	    </div>
	    <div class="cp-recipient-popup__button">
	        {include file="buttons/button.tpl"
	                 but_text=__("cp_add_recipient")
	                 but_role="text"
	                 but_name="dispatch[checkout.checkout]"
	                 but_meta="cm-dialog-closer ty-btn__secondary"
	        }
	    </div>
	</form>
</div>
{if $cart.chosen_shipping.$group_key == $shipping.shipping_id && $shipping.cp_is_door_delivery == 'Y'}
	<div class="litecheckout__group">
		<div class="litecheckout__item">
			<h2 class="litecheckout__step-title">
				{__("shipping_address")}
			</h2>
		</div>
	</div>
	<div class="litecheckout__group {if $shipping.cp_is_door_delivery == 'N'} hidden{/if}" >
		{include
	        file="views/checkout/components/profile_fields.tpl"
	        profile_fields=$s_profile_fields
	        section="ProfileFieldSections::SHIPPING_ADDRESS"|enum
	    }
	</div>
{/if}

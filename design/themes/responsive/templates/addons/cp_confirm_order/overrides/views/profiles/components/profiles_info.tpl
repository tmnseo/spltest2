{include file="common/subheader.tpl" title=__("customer_information")}

{assign var="profile_fields" value=$location|fn_get_profile_fields}
{$contact_fields = $profile_fields.C}
{capture name="cp_actual_address_front"}
   {$user_actual_address = $order_info.cp_address_data.C}
   {if $user_actual_address}
      {if $user_actual_address.a_address}
          <p>{$user_actual_address.a_address}</p>
      {/if}
   <p>
      {if $user_actual_address.a_city}
          {$user_actual_address.a_city},&nbsp; 
      {/if}
      {if $user_actual_address.a_area}
          {$user_actual_address.a_area},&nbsp;
      {/if}
      {if $user_actual_address.a_street || $user_actual_address.a_home}
          {$user_actual_address.a_street}
          
          {if $user_actual_address.a_home}
              {$user_actual_address.a_home},&nbsp;
          {else}
          ,&nbsp;
          {/if}
          
      {/if} 
      {if $user_actual_address.a_corp}
          {$user_actual_address.a_corp},&nbsp;
      {/if}
      {if $user_actual_address.a_office}
          {$user_actual_address.a_office},&nbsp;
      {/if}
      {if $user_actual_address.a_zipcode}
          {$user_actual_address.a_zipcode},&nbsp;
      {/if}
   </p>
   {/if}
{/capture}
<div class="ty-profiles-info">
    {if $profile_fields.B}
        <div id="tygh_order_billing_adress" class="ty-profiles-info__item ty-profiles-info__billing">
            <h5 class="ty-profiles-info__title">{__("billing_address")}</h5>
            <div class="ty-profiles-info__field">{include file="views/profiles/components/profile_fields_info.tpl" fields=$profile_fields.B title=__("billing_address")}</div>
        </div>
    {/if}
    {if $order_info.cp_address_data.C}
        <div id="tygh_order_shipping_adress" class="ty-profiles-info__item ty-profiles-info__shipping">
            <h5 class="ty-profiles-info__title">{__("cp_actual_address")}</h5>
            <div class="ty-profiles-info__field">{$smarty.capture.cp_actual_address_front nofilter}</div>
        </div>
    {/if}
    {if $contact_fields}
        <div class="ty-profiles-info__item">
            {capture name="contact_information"}
                {include file="views/profiles/components/profile_fields_info.tpl" fields=$contact_fields title=__("contact_information")}
            {/capture}
            {if $smarty.capture.contact_information|trim != ""}
                <h5 class="ty-profiles-info__title">{__("contact_information")}</h5>
                <div class="ty-profiles-info__field">{$smarty.capture.contact_information nofilter}</div>
            {/if}
        </div>
    {/if}
</div>


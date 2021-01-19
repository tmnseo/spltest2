{if $user_type == "C"}
   <div class="control-group">
      <label class="control-label" for="storefront_id">{__("storefront")}</label>
      <div class="controls">
         {include file="pickers/storefronts/picker.tpl"
            item_ids=$user_data['storefront_id'] 
            display_input_id=storefront_id
         }
      </div>
  </div>
{/if}
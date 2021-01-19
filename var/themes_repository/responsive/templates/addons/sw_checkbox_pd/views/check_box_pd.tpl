{if $addons.sw_checkbox_pd.id_page}
  {assign var="id_page" value="`$addons.sw_checkbox_pd.id_page`"}
{else}
   {assign var="id_page" value="3"}
{/if}  
   
   {if $option == "call_request"}
   
     {if $addons.sw_checkbox_pd.check_call_requests == "Y" && !$product}
   
        <div class="ty-control-group sw-check_pd">
            <input id="call_data_{$id}_check_pd" class="ty-checkbox" type="checkbox" {if $addons.sw_checkbox_pd.checked_allways == "Y"}checked="checked"{/if} name="call_data[checkbox]" value="Y" />
          
                <label for="call_data_{$id}_check_pd" class="ty-control-group__title cm-required">{__("sw_checkbox_pd.text_pd",["[link]" => $id_page])}</label>
         
             
        </div>
		
		{elseif $addons.sw_checkbox_pd.check_one_click == "Y" && $product}
		
		<div class="ty-control-group sw-check_pd">
            <input id="call_data_{$id}_check_pd" class="ty-checkbox" type="checkbox" {if $addons.sw_checkbox_pd.checked_allways == "Y"}checked="checked"{/if} name="call_data[checkbox]" value="Y" />
          
                <label for="call_data_{$id}_check_pd" class="ty-control-group__title cm-required">{__("sw_checkbox_pd.text_pd",["[link]" => $id_page])}</label>
         
             
        </div>
		
		
		{/if}
		
		
    
    {/if}
    
    
    {if $option == "newsletters" && $addons.sw_checkbox_pd.check_subscr == "Y"}
        <div class="ty-control-group sw-check_pd pd-newsletters">
            <input id="subscr_{$block.block_id}_check_pd" class="ty-checkbox" {if $addons.sw_checkbox_pd.checked_allways == "Y"}checked="checked"{/if} type="checkbox" name="subscr[checkbox]" value="Y" />
          
                <label for="subscr_{$block.block_id}_check_pd" class="ty-control-group__title cm-required">{__("sw_checkbox_pd.text_pd",["[link]" => $id_page])}</label>
   
             
        </div>
    
    {/if}
    
      {$addons.sw_checkbox_pd.check_discuss = "Y" && $discussion.object_type != "O"}
     {if $option == "discussion" && $addons.sw_checkbox_pd.check_discuss == "Y"}
        <div class="ty-control-group sw-check_pd pd-discussion">
            <input id="post_data_{$block.block_id}_check_pd" class="ty-checkbox" type="checkbox" {if $addons.sw_checkbox_pd.checked_allways == "Y"}checked="checked"{/if} name="post_data[checkbox]" value="Y" />
          
                <label for="post_data_{$block.block_id}_check_pd" class="ty-control-group__title cm-required">{__("sw_checkbox_pd.text_pd",["[link]" => $id_page])}</label>
   
             
        </div>
        
       
        
    
    {/if}
    
    {if $option == "account_info" && $addons.sw_checkbox_pd.check_registration == "Y"}
        <div class="ty-control-group sw-check_pd pd-account_info">
            <input id="user_data_{$block.block_id}_check_pd" class="ty-checkbox" type="checkbox" {if $addons.sw_checkbox_pd.checked_allways == "Y"}checked="checked"{/if} name="user_data[checkbox]" value="Y" />
          
                <label for="user_data_{$block.block_id}_check_pd" class="ty-control-group__title cm-required">{__("sw_checkbox_pd.text_pd",["[link]" => $id_page])}</label>
   
             
        </div>
    
    {/if}
	
	
    
    {if $option == "checkout" && $addons.sw_checkbox_pd.check_checkout == "Y"}
        <div class="ty-control-group sw-check_pd pd-checkout">
            <input id="checkout_{$block.block_id}_check_pd" class="ty-checkbox" type="checkbox" {if $addons.sw_checkbox_pd.checked_allways == "Y"}checked="checked"{/if} name="checkout_checkbox_pd" value="Y" />
          
                <label for="checkout_{$block.block_id}_check_pd" class="ty-control-group__title cm-required">{__("sw_checkbox_pd.text_pd",["[link]" => $id_page])}</label>
   
             
        </div>
    
    {/if}
    
    
     {if $option == "page_form" && $page.check_form_pd == "Y"}
     
        <div class="ty-control-group sw-check_pd pd-page_form">
            <input id="page_{$page.page_id}_check_pd" class="ty-checkbox" type="checkbox" {if $addons.sw_checkbox_pd.checked_allways == "Y"}checked="checked"{/if} name="checkout_checkbox_pd" value="Y" />
          
                <label for="page_{$page.page_id}_check_pd" class="ty-control-group__title cm-required">{__("sw_checkbox_pd.text_pd",["[link]" => $id_page])}</label>
   
             
        </div>
        
        
    
    {/if}
    
    
    {*MVE*}
{if "MULTIVENDOR"|fn_allowed_for}

{if $addons.sw_checkbox_pd.id_page_reg_vendor}
  {assign var="id_page" value="`$addons.sw_checkbox_pd.id_page_reg_vendor`"}
{else}
   {assign var="id_page" value="3"}
{/if}  
    
     {if $option == "reg_vendor" && $addons.sw_checkbox_pd.check_reg_vendor == "Y"}
        <div class="ty-control-group sw-check_pd pd-reg_vendor">
            <input id="company_{$block.block_id}_check_pd" class="ty-checkbox" type="checkbox" {if $addons.sw_checkbox_pd.checked_allways == "Y"}checked="checked"{/if} name="company_data[checkbox_pd]" value="Y" />
          
                <label for="company_{$block.block_id}_check_pd" class="ty-control-group__title cm-required">{__("sw_checkbox_pd.text_pd_mve",["[link]" => $id_page])}</label>
   
             
        </div>
    
    {/if}

{/if} 
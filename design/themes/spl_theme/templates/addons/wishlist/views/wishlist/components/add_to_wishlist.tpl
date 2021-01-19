{if !$block.properties || $block.properties.hide_add_to_wishlist_button != "Y"}
    {if !$auth.user_id}
        {$but_meta = $wishlist_but_meta|default:"ty-btn__primary"}
        {$current_url = $config.current_url}
        {$return_url = $current_url|cat:"&cp_add_to_wishlist=1&but_id=`$wishlist_but_id|default:$but_id`"}
              
        {if $wishlist_but_meta}
            {$wishlist_but_meta = $wishlist_but_meta|cat :"cm-dialog-opener cm-dialog-auto-size"}
        {/if}
        {include file="buttons/button.tpl"
            but_id=     $wishlist_but_id|default:       $but_id
            but_meta=   $wishlist_but_meta|default:     "ty-btn__add-to-wish cm-dialog-opener cm-dialog-auto-size"
            but_name=   $wishlist_but_name|default:     $but_name
            but_text=   $wishlist_but_text|default:     false
            but_title=  __("sign_in")
            but_role=   $wishlist_but_role|default:     "text"
            but_href=   "{"wishlist.cp_wishlist_login_form?return_url=`$return_url|urlencode`"}"
            but_icon=   $wishlist_but_icon|default:     "icon-spl-star"
            but_target_id="wishlist_login_form"
        }
    {else}
        {$warehouse_id = $cp_store_data.store_location_id|default:$product.extra.warehouse_id}
        {$product_id = $product.product_id}

        {$wishlist_button_type = $wishlist_button_type|default:  "icon"}
        {$but_id               = $wishlist_but_id|default:       $but_id}
        {$but_name             = $wishlist_but_name|default:     $but_name}
        {$but_title            = $wishlist_but_title|default:    __("add_to_wishlist")}
        {$but_role             = $wishlist_but_role|default:     "text"}
        {$but_onclick          = $wishlist_but_onclick|default:  $but_onclick}
        {$but_href             = $wishlist_but_href|default:     $but_href}

        {if $wishlist_button_type == "icon"}
            {$but_icon         = $wishlist_but_icon|default:     "icon-spl-star"}
            {$but_text         = $wishlist_but_text|default:     false}
            {$but_meta         = $wishlist_but_meta|default:     "ty-btn__add-to-wish"}
        {else}
            {$but_icon         = ($wishlist_but_icon === true) ? "icon-spl-star" : $wishlist_but_icon}
            {$but_text         = $wishlist_but_text|default:     __("wishlist")}
            {$but_meta         = $wishlist_but_meta|default:     "ty-btn__add-to-wish "}
        {/if}

        {$wishlist_id=$product_id|fn_cp_check_wishlist:$warehouse_id} 
        {if $wishlist_id}
            {$but_meta=$but_meta|cat:" active cm-submit"}
            {$but_name=""}
            {if $runtime.controller == 'products'}
                {$cp_location="'product'"}
            {elseif $runtime.controller == 'wishlist'} 
                {$cp_location="'wishlist'"}
            {elseif $runtime.controller == 'checkout' && $runtime.mode == 'cart'}
                {$cp_location="'cart'"}
            {/if}
            {$but_onclick="fn_cp_delete_from_wishlist($wishlist_id, $product_id, $warehouse_id, $cp_location);"}
        {/if}
        {$but_meta=$but_meta|cat:" cm-ajax"}

        {include file="buttons/button.tpl"
            but_id=$but_id
            but_meta=$but_meta
            but_name=$but_name 
            but_text=$but_text
            but_title=$but_title
            but_role=$but_role
            but_onclick=$but_onclick
            but_href=$but_href
            but_icon=$but_icon
        }
    {/if}
{/if}
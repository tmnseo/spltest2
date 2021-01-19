{assign var="columns" value=4}
{if !$wishlist_is_empty}

    {script src="js/tygh/exceptions.js"}

    {assign var="show_hr" value=false}
    {assign var="location" value="cart"}
{/if}
{if $products}
    {include file="blocks/list_templates/grid_list.tpl" 
        columns=3
        show_empty=true
        show_name=true 
        show_old_price=true 
        show_price=true 
        show_clean_price=true 
        show_list_discount=true
        no_pagination=true
        no_sorting=true
        show_features=true
        show_add_to_cart=true
        show_list_buttons=true
        is_wishlist=true
        show_rating=false
        custom_class="grid-list_wishlist"
    }

{else}
    <p class="ty-no-items cm-pagination-container">{__("text_no_products")}</p>
{/if}

{* {if !$wishlist_is_empty}
    <div class="buttons-container ty-wish-list__buttons">
        {include file="buttons/button.tpl" but_text=__("clear_wishlist") but_href="wishlist.clear" but_meta="ty-btn__tertiary"}
        {include file="buttons/continue_shopping.tpl" but_href=$continue_url|fn_url but_role="text"}
    </div>
{else}
    <div class="buttons-container ty-wish-list__buttons ty-wish-list__continue">
        {include file="buttons/continue_shopping.tpl" but_href=$continue_url|fn_url but_role="text"}
    </div>
{/if} *}

{capture name="mainbox_title"}{__("wishlist_content")}{/capture}

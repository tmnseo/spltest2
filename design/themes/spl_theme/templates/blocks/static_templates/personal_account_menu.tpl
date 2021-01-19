{$page_dispatch = "`$runtime.controller`.`$runtime.mode`"}
{if $auth.user_id}
    <div class="personal-account-menu_wrap">
        <ul class="personal-account-menu">
            {include file="blocks/components/my_account_list_menu.tpl" 
                account_orders_text=__("orders")
                account_support_text=__("support")
            }
        </ul>
    </div>
{/if}

<h2 class="ty-minor-heading__account hidden-tablet hidden-desktop">
    {if $page_dispatch == "orders.search"}
        {__("orders")}
    {elseif $page_dispatch == "wishlist.view"}
        {__("wishlist_content")}
    {elseif $page_dispatch == "vendor_communication.threads"}
        {__("vendor_communication.messages")}
    {elseif $page_dispatch == "profiles.update"}
        {__("edit_profile")}
    {/if}
</h2>
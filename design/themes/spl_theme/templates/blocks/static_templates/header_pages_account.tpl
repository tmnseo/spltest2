<h1 class="ty-main-heading__account">
    {__("personal_area")} 
    <span class="hidden-phone">-</span> 
    <span class="cp-user-info">
    {$cp_user_info = $user_info.user_id|fn_get_user_info}
    {if $cp_user_info.company}
        {$cp_user_info.company}
    {elseif $user_info.firstname && $user_info.lastname}
        {$user_info.lastname} {$user_info.firstname}
    {elseif $user_info.email}
        {$user_info.email}
    {/if}
    </span>
</h1>
{$page_dispatch = "`$runtime.controller`.`$runtime.mode`"}
<h2 class="ty-minor-heading__account hidden-phone">
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
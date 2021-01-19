{hook name="profiles:my_account_menu"}
    <li class="ty-account-info__item ty-dropdown-box__item ty-account-info__item-order{if $page_dispatch == "orders.search"} active{/if}">
        <a class="ty-account-info__a underlined" href="{"orders.search"|fn_url}" rel="nofollow">
            {$account_orders_text|default:__("my_orders")}
        </a>
    </li>

    {if $addons.wishlist.status == "A"}
        <li class="ty-account-info__item ty-dropdown-box__item ty-account-info__item-wishlist{if $page_dispatch == "wishlist.view"} active{/if}">
            <a class="ty-account-info__a" href="{"wishlist.view"|fn_url}" rel="nofollow">
                {__("wishlist")}
                <span class="ty-account-info__quantity">
                    {if $wishlist_count > 0} 
                        ({$wishlist_count})
                    {/if}
                </span>
            </a>
        </li>
    {/if}

    {if $addons.vendor_communication.status == "A"}
        <li class="ty-account-info__item ty-dropdown-box__item ty-account-info__item-communication{if $page_dispatch == "vendor_communication.threads"} active{/if}">
            <a class="ty-account-info__a underlined" href="{"vendor_communication.threads"|fn_url}" rel="nofollow" >
                {__("vendor_communication.messages")}
            </a>
        </li>
    {/if}

    <li class="ty-account-info__item ty-dropdown-box__item ty-account-info__item-support">
        <a class="ty-account-info__a underlined" href="" rel="nofollow" >
            {$account_support_text|default:__("care_service")}
        </a>
    </li>

    <li class="ty-account-info__item ty-dropdown-box__item ty-account-info__item-profiles{if $page_dispatch == "profiles.update"} active{/if}">
        <a class="ty-account-info__a underlined" href="{"profiles.update"|fn_url}" rel="nofollow" >
            {__("edit_profile")}
        </a>
    </li>

    {if $settings.General.enable_edp == "Y"}
        <li class="ty-account-info__item ty-dropdown-box__item ty-account-info__item-downloads{if $page_dispatch == "orders.downloads"} active{/if}">
            <a class="ty-account-info__a underlined" href="{"orders.downloads"|fn_url}" rel="nofollow">
                {__("downloads")}
            </a>
        </li>
    {/if}

    {if $settings.General.enable_compare_products == 'Y'}
        {assign var="compared_products" value=""|fn_get_comparison_products}
        <li class="ty-account-info__item ty-dropdown-box__item ty-account-info__item-compare{if $page_dispatch == "product_features.compare"} active{/if}">
            <a class="ty-account-info__a underlined" href="{"product_features.compare"|fn_url}" rel="nofollow">
                {__("view_comparison_list")}
                {if $compared_products} 
                    ({$compared_products|count})
                {/if}
            </a>
        </li>
    {/if}
{/hook}
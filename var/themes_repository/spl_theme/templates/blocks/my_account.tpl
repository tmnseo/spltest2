{** block-description:my_account **}

{capture name="title"}
    {if $auth.user_id}
        <a class="ty-account-info__title" href="{"profiles.update"|fn_url}">
            <span class="icon-spl-user"></span>
            <span>{__("personal_area_footer")}</span>
        </a>
    {else}
        <div class="ty-account-info__title">
            <span class="icon-spl-user"></span>
            <span>{__("sign_in")}</span>
        </div>
    {/if}
{/capture}

<div id="account_info_{$block.snapping_id}">
    {assign var="return_current_url" value=$config.current_url|escape:url}
    {if $auth.user_id}
        <ul class="ty-account-info">
            {include file="blocks/components/my_account_list_menu.tpl" account_support_text=__("support")}
        </ul>
        {if $settings.Appearance.display_track_orders == 'Y'}
            <div class="ty-account-info__orders updates-wrapper track-orders" id="track_orders_block_{$block.snapping_id}">
                <form action="{""|fn_url}" method="POST" class="cm-ajax cm-post cm-ajax-full-render" name="track_order_quick">
                    <input type="hidden" name="result_ids" value="track_orders_block_*" />
                    <input type="hidden" name="return_url" value="{$smarty.request.return_url|default:$config.current_url}" />

                    <div class="ty-account-info__orders-txt">{__("track_my_order")}</div>

                    <div class="ty-account-info__orders-input ty-control-group ty-input-append">
                        <label for="track_order_item{$block.snapping_id}" class="cm-required hidden">{__("track_my_order")}</label>
                        <input type="text" size="20" class="ty-input-text cm-hint" id="track_order_item{$block.snapping_id}" name="track_data" value="{__("order_id")}{if !$auth.user_id}/{__("email")}{/if}" />
                        {include file="buttons/go.tpl" but_name="orders.track_request" alt=__("go")}
                        {include file="common/image_verification.tpl" option="track_orders" align="left" sidebox=true}
                    </div>
                </form>
            <!--track_orders_block_{$block.snapping_id}--></div>
        {/if}
        <div class="ty-account-info__buttons">
                {$is_vendor_with_active_company="MULTIVENDOR"|fn_allowed_for && ($auth.user_type == "V") && ($auth.company_status == "A")}
                {if $is_vendor_with_active_company}
                    <a href="{$config.vendor_index|fn_url}" rel="nofollow" class="ty-btn ty-btn__primary" target="_blank">{__("go_to_admin_panel")}</a>
                {/if}
                {$return_current_url = "index.php"}
                <a href="{"auth.logout?redirect_url=`$return_current_url`"|fn_url}" rel="nofollow" class="ty-btn_sign-out ">{__("sign_out_account")}</a>
        </div>
    {else}
        {if !$is_checkout && !$is_wishlist}
        <div class="ty-tabs-login cm-j-tabs cm-j-tabs-disable-convertation clearfix ">
            <ul class="ty-tabs__list">
                <li id="buyer-login" class="ty-tabs-login__item cm-js active">{__("customer")}</li>
                <li id="supplier-login" class="ty-tabs-login__item cm-js">{__("supplier")}</li>
            </ul>
        </div>
        {/if}
        <div class="cm-tabs-content ty-tabs-login__content clearfix" id="tabs_content">
            <div id="content_buyer-login">
                {include file="views/auth/login_form.tpl"  id="buyer_login_`$block.snapping_id`" title=__("authorization") hide_remember=true show_register=true hide_label=true show_restore_btn=true dispatch="profiles.add" is_vendor="N"}
            </div>
            {if !$is_checkout && !$is_wishlist}
                <div id="content_supplier-login">
                    {include file="views/auth/login_form.tpl"  id="supplier_login_`$block.snapping_id`" title=__("authorization") hide_remember=true show_register=true hide_label=true show_restore_btn=true dispatch="pages.view&page_id={$addons.cp_spl_theme.id_page_profiles_add}" is_vendor="Y"} 
                </div>
            {/if}
        </div>
        <div class="cp-restore-password hidden" id="restore_password_login_buyer_{$block.snapping_id}">
            <div class="cp-restore-password_return cp-btn-return" data-ca-id="restore_password_login_buyer_{$block.snapping_id}"><span class="icon-spl-arrow-mini-left"></span>{__("come_back")}</div>
            <h3 class="ty-login__title">{__("recover_password_subj")}</h3>
            {include file="views/auth/recover_password.tpl" action="request" hide_label=true id="buyer_login_`$block.snapping_id`"}
        </div>
        <div class="cp-restore-password hidden" id="restore_password_login_supplier_{$block.snapping_id}">
            <div class="cp-restore-password_return cp-btn-return" data-ca-id="restore_password_login_supplier_{$block.snapping_id}"><span class="icon-spl-arrow-mini-left"></span>{__("come_back")}</div>
            <h3 class="ty-login__title">{__("recover_password_subj")}</h3>
            {include file="views/auth/recover_password.tpl" action="request" hide_label=true id="supplier_login_`$block.snapping_id`"}
        </div>
        {script src="js/tygh/tabs.js"}
    {/if}
<!--account_info_{$block.snapping_id}--></div>

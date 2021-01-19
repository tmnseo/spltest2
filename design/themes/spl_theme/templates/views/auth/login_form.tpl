{assign var="id" value=$id|default:"main_login"}

{capture name="login"}
    {if $title}
        <h3 class="ty-login__title">{$title}</h3>
    {/if}
    <form name="{$id}_form" action="{""|fn_url}" method="post" {if $style == "popup"}class="cm-ajax cm-ajax-full-render"{/if}>
        {if $style == "popup"}
            <input type="hidden" name="result_ids" value="login_error_{$id}" />
            <input type="hidden" name="error_container_id" value="login_error_{$id}" />
            <input type="hidden" name="quick_login" value="1" />
        {/if}
        {if $is_vendor}
            <input type="hidden" name="cp_area" value="{if $is_vendor == 'Y'}A{else}C{/if}" />
        {/if}
        <input type="hidden" name="return_url" value="{$smarty.request.return_url|default:$config.current_url}" />
        <input type="hidden" name="redirect_url" value="{$redirect_url|default:$config.current_url}" />

        {if $style == "checkout"}
            <div class="ty-checkout-login-form">{include file="common/subheader.tpl" title=__("returning_customer")}
        {/if}

        <div class="ty-control-group">
            <label for="login_{$id}" class="ty-login__filed-label ty-control-group__label cm-required cm-trim cm-email{if $hide_label} hidden{/if}">{__("email")}</label>
            <input type="text" id="login_{$id}" name="user_login" size="30" value="{$config.demo_username}" placeholder="{__("email")}" class="ty-login__input cm-focus" />
        </div>

        <div class="ty-control-group ty-password-forgot">
            <label for="psw_{$id}" class="ty-login__filed-label ty-control-group__label ty-password-forgot__label cm-required{if $hide_label} hidden{/if}">{__("password")}</label>
                {if $show_restore_btn}
                    <span class="ty-password-forgot__a cm-combination" id="sw_restore_password_login_{if $is_vendor == 'Y'}supplier{else}buyer{/if}_{$block.snapping_id}">{__("forgot_password_question")}</span>
                {else}
                    <a href="{"auth.recover_password"|fn_url}" class="ty-password-forgot__a"  tabindex="5">{__("forgot_password_question")}</a>
                {/if}
            <input type="password" id="psw_{$id}" name="password" size="30" value="{$config.demo_password}" placeholder="{__("password")}" class="ty-login__input" maxlength="32" />
        </div>

        {if $style == "popup"}
            {include file="views/auth/components/login_errors.tpl"}
        {/if}

        {if $style == "popup"}
            <div class="ty-login-reglink ty-center">
                <a class="ty-login-reglink__a" href="{"profiles.add"|fn_url}" rel="nofollow">{__("register_new_account")}</a>
            </div>
        {/if}

        {include file="common/image_verification.tpl" option="login" align="left"}

        {if $style == "checkout"}
            </div>
        {/if}

        {hook name="index:login_buttons"}
            <div class="buttons-container clearfix">
                {if $show_register}
                    <a href="{$dispatch|fn_url}" rel="nofollow" class="ty-btn ty-btn__register">{__("register")}</a>
                {/if}
                <div class="ty-float-right">
                    {include file="buttons/login.tpl" but_name="dispatch[auth.login]" but_role="submit"}
                </div>
                {if !$hide_remember}
                    <div class="ty-login__remember-me">
                        <label for="remember_me_{$id}" class="ty-login__remember-me-label"><input class="checkbox" type="checkbox" name="remember_me" id="remember_me_{$id}" value="Y" />{__("remember_me")}</label>
                    </div>
                {/if}
            </div>
        {/hook}
    </form>
{/capture}

{if $style == "popup"}
    {$smarty.capture.login nofilter}
{else}
    <div class="ty-login">
        {$smarty.capture.login nofilter}
    </div>

    {capture name="mainbox_title"}{__("sign_in")}{/capture}
{/if}

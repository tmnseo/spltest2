{if !$nothing_extra}
    {include file="common/subheader.tpl" title=__("user_account_info")}
{/if}

{hook name="profiles:account_info"}
    {if $profiles_user}
        <div class="account-info__column">
            <span class="account-info__column-title">{__("account")}</span>
            <div class="ty-control-group">
                <label for="email" class="ty-control-group__title cm-required cm-email cm-trim hidden">{__("email")}</label>
                <input type="text" id="email" name="user_data[email]" size="32" maxlength="128" value="{$user_data.email}" placeholder="{__("email")}" class="ty-input-text cm-focus" />
            </div>
        </div>

        <div class="account-info__column">
            <span class="account-info__column-title">{__("change_password")}</span>
            <div class="ty-control-group">
                <label for="password1" class="ty-control-group__title cm-required cm-password hidden">{__("password")}</label>
                <input type="password" id="password1" name="user_data[password1]" size="32" maxlength="32" value="{if $runtime.mode == "update"}            {/if}" placeholder="{__("password")}" class="ty-input-text cm-autocomplete-off" />
            </div>

            <div class="ty-control-group">
                <label for="password2" class="ty-control-group__title cm-required cm-password hidden">{__("confirm_password")}</label>
                <input type="password" id="password2" name="user_data[password2]" size="32" maxlength="32" value="{if $runtime.mode == "update"}            {/if}"  placeholder="{__("confirm_password")}"class="ty-input-text cm-autocomplete-off" />
            </div>
        </div>
    {else}
        <div class="ty-control-group">
            <label for="email" class="ty-control-group__title cm-required cm-email cm-trim">{__("email")}</label>
            <input type="text" id="email" name="user_data[email]" size="32" maxlength="128" value="{$user_data.email}" class="ty-input-text cm-focus" />
        </div>

        <div class="ty-control-group">
            <label for="password1" class="ty-control-group__title cm-required cm-password">{__("password")}</label>
            <input type="password" id="password1" name="user_data[password1]" size="32" maxlength="32" value="{if $runtime.mode == "update"}            {/if}" class="ty-input-text cm-autocomplete-off" />
        </div>

        <div class="ty-control-group">
            <label for="password2" class="ty-control-group__title cm-required cm-password">{__("confirm_password")}</label>
            <input type="password" id="password2" name="user_data[password2]" size="32" maxlength="32" value="{if $runtime.mode == "update"}            {/if}" class="ty-input-text cm-autocomplete-off" />
        </div>
    {/if}
{/hook}
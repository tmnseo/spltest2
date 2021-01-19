{hook name="select_popup:notify_checkboxes"}
{if $auth.user_type == 'V' && $runtime.controller == 'orders' && $runtime.mode == 'manage'}
    <div class="hidden">
        {if $notify}
            <li class="divider"></li>
            <li><a><label for="{$prefix}_{$id}_notify">
                <input type="checkbox" name="__notify_user" id="{$prefix}_{$id}_notify" value="Y" {if $notify_customer_status == true} checked="checked" {/if} onclick="Tygh.$('input[name=__notify_user]').prop('checked', this.checked);" />
                {$notify_text|default:__("notify_customer")}</label></a>
            </li>
        {/if}
        {if $notify_department}
            <li><a><label for="{$prefix}_{$id}_notify_department">
                <input type="checkbox" name="__notify_department" id="{$prefix}_{$id}_notify_department" value="Y" {if $notify_department_status == true} checked="checked" {/if} onclick="Tygh.$('input[name=__notify_department]').prop('checked', this.checked);" />
                {__("notify_orders_department")}</label></a>
            </li>
        {/if}
        {if "MULTIVENDOR"|fn_allowed_for && $notify_vendor}
            <li><a><label for="{$prefix}_{$id}_notify_vendor">
                <input type="checkbox" name="__notify_vendor" id="{$prefix}_{$id}_notify_vendor" value="Y" {if $notify_vendor_status == true} checked="checked" {/if} onclick="Tygh.$('input[name=__notify_vendor]').prop('checked', this.checked);" />
                {__("notify_vendor")}</label></a>
            </li>
        {/if}
    </div>
{/if}             
{/hook}
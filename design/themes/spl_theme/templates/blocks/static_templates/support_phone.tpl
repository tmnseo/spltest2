{if $addons.cp_spl_theme.cp_support_phone}
{$cp_support_phone_href=$addons.cp_spl_theme.cp_support_phone|replace:' ':''}
<div class="cp-support-phone">
    <span class="cp-support-phone__label">{__("cp_support")}: </span>
    <a class="cp-support-phone__value" href="tel:{$cp_support_phone_href}">{$addons.cp_spl_theme.cp_support_phone}</a>
</div>
{/if}
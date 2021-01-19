{$but_title=$but_title|default:__("vendor_communication.ask_a_question")}
{$show_icon=$show_icon|default:true}
{if $auth.user_id}
    <a title="{$but_title}" class="{if "MULTIVENDOR"|fn_allowed_for}ty-vendor-communication__post-write{/if} cm-dialog-opener cm-dialog-auto-size" data-ca-target-id="new_thread_dialog_{$object_id}" rel="nofollow" data-ca-dialog-class="ty-vendor-communication-post__new-popup">
        {if $show_icon}<i class="ty-icon-chat"></i>{/if}
        {$but_title}
    </a>
{else}
    {assign var="return_current_url" value=$config.current_url|escape:url}

    <a title="{$but_title}" href="{"auth.login_form?return_url=`$return_current_url`"|fn_url}" data-ca-dialog-class="ty-vendor-communication-post__new-popup" data-ca-target-id="new_thread_login_form" class="cm-dialog-opener cm-dialog-auto-size {if "MULTIVENDOR"|fn_allowed_for}ty-vendor-communication__post-write{/if}" rel="nofollow">
        {if $show_icon}<i class="ty-icon-chat"></i>{/if}
        {$but_title}
    </a>

    {if $show_form}
        {include file="addons/vendor_communication/views/vendor_communication/components/login_form.tpl"}
    {/if}
{/if}
<div class="ty-vendor-communication-post__wrapper" data-vs-scroll-to=".vendor-communication-post-item:last" id="messages_list_{$thread_id}">
    {if $messages}
        {foreach from=$messages item=message}
            <div class="ty-vendor-communication-post__content vendor-communication-post-item {if $message.user_type == $auth.user_type}ty-vendor-communication-post__you {/if}ty-mb-l">
                {hook name="vendor_communication:items_list_row"}
                {* <div class="ty-vendor-communication-post__img">
                    {if $message.user_type == "V"}
                        <a href="{"companies.products?company_id=`$message.vendor_info.logos.theme.company_id`"|fn_url}">
                            {include file="common/image.tpl" images=$message.vendor_info.logos.theme.image image_width="60" image_height="60" class="ty-vendor-communication-post__logo"}
                        </a>
                    {/if}
                    {if $message.user_type == "A"}
                        <i class="ty-icon-user"></i>
                    {/if}
                </div> *}
                <div class="ty-vendor-communication-post__info">
                    <div class="ty-vendor-communication-post {cycle values=", ty-vendor-communication-post_even"}" id="post_{$message.message_id}">
                        <div class="ty-vendor-communication-post__info-header">
                            <div class="ty-vendor-communication-post__author">{if $message.user_type == "C"}{__("vendor_communication.you")}{else}{$message.firstname} {$message.lastname}{/if}</div>
                            <div class="ty-vendor-communication-post__date">{$message.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</div>
                        </div>
                        <div class="ty-vendor-communication-post__message">{$message.message|nl2br nofilter}</div>
                        <span class="ty-caret"> <span class="ty-caret-outer"></span> <span class="ty-caret-inner"></span></span>
                    </div>
                </div>
                {/hook}
            </div>
        {/foreach}
        <div class="ty-vendor-communication-post__bottom"></div>
    {else}
        <p class="ty-no-items">{__("vendor_communication.no_messages_found")}</p>
    {/if}
<!--messages_list_{$thread_id}--></div>

{include
    file="addons/vendor_communication/views/vendor_communication/components/new_message_form.tpl"
    object_id=$thread_id
}
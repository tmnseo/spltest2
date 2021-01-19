{if $cp_popup_order_completed}
    <div class="cm-notification-container notification-container{if $cp_popup_order_completed} notification-container_order-completed{/if}">
        {if !"AJAX_REQUEST"|defined}
            {foreach from=""|fn_get_notifications item="message" key="key"}
                {if $message.type == "I"}
                    <div class="ui-widget-overlay hidden-phone" data-ca-notification-key="{$key}"></div>
                    <div class="cm-notification-content cm-notification-content-extended notification-content_order-completed{if $message.message_state == "I"} cm-auto-hide{/if}"
                         data-ca-notification-key="{$key}">
                        {if $addons.cp_checkout_modifications.id_banner_popup}
                            {$banner=$addons.cp_checkout_modifications.id_banner_popup|fn_get_banner_data}
                        {/if}
                        <span class="cm-notification-close icon-spl-close hidden-phone"></span>
                        {if $banner}
                            <div class="notification-body_banner">
                                {include file="common/image.tpl" images=$banner.main_pair class="notification-body_banner__image" image_width="392" image_height="247"}
                            </div>
                        {/if}
                        <div class="notification-body">
                            <h2>{$message.title}</h2>
                            {$message.message nofilter}
                        </div>
                    </div>
                {elseif $message.type =="WW"}


                    {if $cp_show_notice}
                        <div class="cm-notification-content notification-content{if $message.message_state == "I"} cm-auto-hide{/if} {if $message.type == "N"}alert-success{elseif $message.type == "W"}alert-warning{else}alert-error{/if}"
                             data-ca-notification-key="{$key}">
                            <button type="button"
                                    class="close cm-notification-close {if $message.message_state == "S"} cm-notification-close-ajax{/if}"
                                    {if $message.message_state != "S"}data-dismiss="alert"{/if}>&times;</button>
                            <strong>{$message.title}</strong>
                            {$message.message nofilter}
                        </div>
                    {/if}

                {/if}
            {/foreach}
        {/if}
    </div>
{else}
    <div class="cm-notification-container notification-container">
        {if !"AJAX_REQUEST"|defined}
            {foreach from=""|fn_get_notifications item="message" key="key"}
                {if $message.type == "I"}
                    <div class="ui-widget-overlay" data-ca-notification-key="{$key}"></div>
                    <div class="cm-notification-content cm-notification-content-extended notification-content-extended{if $message.message_state == "I"} cm-auto-hide{/if}"
                         data-ca-notification-key="{$key}">
                        <h1>{$message.title}<span
                                    class="cm-notification-close {if $message.message_state == "S"} cm-notification-close-ajax{/if}"></span>
                        </h1>
                        <div class="notification-body-extended">
                            {$message.message nofilter}
                        </div>
                    </div>
                {elseif $message.type == "O"}
                    <div class="cm-notification-content notification-content alert-error"
                         data-ca-notification-key="{$key}">
                        <button type="button" class="close cm-notification-close"
                                {if $message.message_state != "S"}data-dismiss="alert"{/if}>&times;</button>
                        {$message.message nofilter}
                    </div>
                {else}
                    <div class="cm-notification-content notification-content{if $message.message_state == "I"} cm-auto-hide{/if} {if $message.type == "N"}alert-success{elseif $message.type == "W"}alert-warning{else}alert-error{/if}"
                         data-ca-notification-key="{$key}">
                        <button type="button"
                                class="close cm-notification-close {if $message.message_state == "S"} cm-notification-close-ajax{/if}"
                                {if $message.message_state != "S"}data-dismiss="alert"{/if}>&times;</button>
                        <strong>{$message.title}</strong>
                        {$message.message nofilter}
                    </div>
                {/if}
            {/foreach}
        {/if}
    </div>
{/if}
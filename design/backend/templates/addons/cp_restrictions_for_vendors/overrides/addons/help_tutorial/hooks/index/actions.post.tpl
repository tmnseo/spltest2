{if $auth.user_type == 'V'}
<div class="help-tutorial clearfix">
    <span class="help-tutorial-link cm-external-click {if $sidebar_content|trim != ""}{if $sidebar_position=="right"}pulled-left{elseif $sidebar_position=="left"}pulled-right{/if}{/if}" id="cp_help_tutorial_link" data-ca-scroll="main_column">
        <span class="help-tutorial-show"><a href="{$addons.cp_restrictions_for_vendors.support_href}" target="_blank"><i class="help-tutorial-icon icon-question-sign"></i>{__("help_tutorial.need_help")}</a></span>
    </span>
</div>
{/if}
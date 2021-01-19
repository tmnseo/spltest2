{script src="js/tygh/tabs.js"}
{script src="js/tygh/fileuploader_scripts.js"}

{capture name="mainbox"}
    <div class="cp-addons-manage" id="addons_list_reload">

        {include file="addons/cp_addons_manager/views/cp_addons_manager/components/correct_permissions.tpl"}
        
        {assign var="c_url" value=$config.current_url|escape:"url"}
        
        <div class="table-responsive-wrapper">
        {if $addons_list}
            <table class="table table-middle table-responsive">
                {if $extra.am_id && $addons_list[$extra.am_id]}
                    {$addon = $addons_list[$extra.am_id]}
                    <tr>
                        <td class="right" width="4%">
                            {include file="addons/cp_addons_manager/views/cp_addons_manager/components/list_tools.tpl" addon=$addons_list[$extra.am_id] c_url=$c_url is_am=true}
                        </td>
                        <td>
                            {include file="addons/cp_addons_manager/views/cp_addons_manager/components/list_info.tpl" addon=$addons_list[$extra.am_id]}
                        </td>
                        <td class="left cp-addon-right-wrap" width="40%">
                            {include file="addons/cp_addons_manager/views/cp_addons_manager/components/list_action.tpl" addon=$addons_list[$extra.am_id] without_subscr=true is_am=true extra=$extra}
                        </td>
                    </tr>
                {/if}
                {foreach from=$addons_list item="addon" key="key"}
                    {if $key == $extra.am_id}
                        {continue}
                    {/if}
                    <tr>
                        <td class="right" width="4%">
                            {include file="addons/cp_addons_manager/views/cp_addons_manager/components/list_tools.tpl" addon=$addon c_url=$c_url}
                        </td>
                        <td>
                            {include file="addons/cp_addons_manager/views/cp_addons_manager/components/list_info.tpl" addon=$addon}
                        </td>
                        <td class="left cp-addon-right-wrap" width="40%">
                            {include file="addons/cp_addons_manager/views/cp_addons_manager/components/list_action.tpl" addon=$addon}
                        </td>
                    </tr>
                {/foreach}
            </table>
        {else}
            <p class="no-items">{__("no_data")}</p>
        {/if}
        </div>
        {capture name="buttons"}
        {/capture}
        {capture name="adv_buttons"}
        {/capture}
        </form>
    <!--addons_list_reload--></div>
{/capture}

{capture name="title"}{__("cart_power")}: {__("cp_my_addons")}{/capture}

{include file="common/mainbox.tpl" title=$smarty.capture.title content=$smarty.capture.mainbox buttons=$smarty.capture.buttons adv_buttons=$smarty.capture.adv_buttons select_languages=false no_sidebar=true}

{** banners section **}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="add_serv_link_form" class="form-horizontal form-edit ">
    <fieldset>
        <div class="control-group">
            <label for="elm_cp_add_name" class="control-label cm-required">{__("shipping")}:</label>
            <div class="controls">
                <select name="service_data[module]" id="elm_cp_add_name">
                    {foreach from=$avail_services item="av_serv"}
                        <option value="{$av_serv}">{$av_serv}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="control-group">
            <label for="elm_track_link" class="control-label cm-required">{__("link")}{include file="common/tooltip.tpl" tooltip={__("cp_oc_track_info")} params="ty-subheader__tooltip"}:</label>
            <div class="controls">
                <input type="text" name="service_data[track_link]" id="elm_track_link" value="" placeholder="http://shipping_service.com/?trak_number=[TRACKING_NUMBER]" class="input-large" />
            </div>
        </div>
        <div class="buttons-container">
            <div class="controls">
                {include file="buttons/button.tpl" but_text=__("add") but_role="submit" but_name="dispatch[cp_service_links.add]" but_target_form="add_serv_link_form"}
            </div>
        </div>
    </fieldset>
</form>

<form action="{""|fn_url}" method="post" name="links_manage_form" class="{if ""|fn_check_form_permissions} cm-hide-inputs{/if}" id="links_manage_form" >  
    {if $links}
        <div class="table-responsive-wrapper">
            <table class="table table-middle table-responsive">
                <thead>
                    <tr> 
                        <th width="25%" class="nowrap left">{__("shipping")}</th>
                        <th width="60%">{__("link")}</th>
                        <th width="15%" class="mobile-hide center">&nbsp;</th>
                    </tr>
                </thead>

                {foreach from=$links item="link_data"}
                    <input type="hidden" name="services[{$link_data.module}][module]" value="{$link_data.module}" />
                    <tr>
                        <td class="left" data-th="{__("name")}">
                            {$link_data.module}
                        </td>
                        <td class="left" data-th="{__("link")}">
                            <input type="text" name="services[{$link_data.module}][track_link]" value="{$link_data.track_link}" style="width: 100%;" class="input-hidden"/>
                        </td>
                        <td class="right mobile-hide">
                            <div class="hidden-tools">
                            {capture name="tools_list"}
                                <li>{btn type="list" text=__("delete") class="cm-confirm" href="cp_service_links.delete?module=`$link_data.module`"}</li>
                            {/capture}
                            {dropdown content=$smarty.capture.tools_list}
                            </div>
                        </td>
                    </tr>
                {/foreach}
            </table>
        </div>
    {else}
        <p class="no-items">{__("no_data")}</p>
    {/if}

    {capture name="buttons"}
        {if $links}
            {include file="buttons/save.tpl" but_name="dispatch[cp_service_links.update]" but_role="submit-link" but_target_form="links_manage_form"}
        {/if}
    {/capture}
</form>
{/capture}

{include file="common/mainbox.tpl" title=__("cp_oc_service_links") content=$smarty.capture.mainbox buttons=$smarty.capture.buttons adv_buttons=$smarty.capture.adv_buttons}

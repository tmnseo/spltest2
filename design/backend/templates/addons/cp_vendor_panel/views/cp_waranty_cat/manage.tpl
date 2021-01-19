{capture name="mainbox"}
    <form action="{""|fn_url}" method="post" name="add_category_form" class="form-horizontal form-edit ">
        <input type="hidden" class="cm-no-hide-input" name="category_id" value="0" />
        <fieldset>
            <div class="control-group">
                <label for="elm_cp_add_name" class="control-label cm-required">{__("name")}:</label>
                <div class="controls">
                    <input type="text" name="category_data[category]" id="elm_cp_add_name" size="25" value="" class="input-short" />
                </div>
            </div>
            <div class="control-group">
                <label for="elm_cp_add_position" class="control-label">{__("position")}:</label>
                <div class="controls">
                    <input type="text" name="category_data[position]" id="elm_cp_add_position" size="25" value="0" class="input-small" />
                </div>
            </div>
            {if "ULTIMATE"|fn_allowed_for}
                {include file="views/companies/components/company_field.tpl"
                    name="category_data[company_id]"
                    id="elm_add_data_`$id`"
                }
            {/if}
            <div class="control-group">
                <label for="elm_cp_add_descr" class="control-label">{__("description")}:</label>
                <div class="controls cm-required">
                    <textarea id="elm_cp_add_descr" name="category_data[description]" cols="35" rows="8" class="cm-wysiwyg input-large"></textarea>
                </div>
            </div>
            {include file="common/select_status.tpl" input_name="category_data[status]" id="elm_add_data_status" obj=$category_data hidden=false}
            <div class="buttons-container">
                <div class="controls">
                    {include file="buttons/button.tpl" but_text=__("cp_vp_add_category") but_role="submit" but_name="dispatch[cp_waranty_cat.update]" but_target_form="add_category_form"}
                </div>
            </div>
        </fieldset>
    </form>

    <form action="{""|fn_url}" method="post" name="war_category_manage_form" class="{if ""|fn_check_form_permissions} cm-hide-inputs{/if}" id="war_category_manage_form" >

    {include file="common/pagination.tpl" save_current_page=true save_current_url=true}

    {$c_url=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
    {$c_icon="<i class=\"exicon-`$search.sort_order_rev`\"></i>"}
    {$c_dummy="<i class=\"exicon-dummy\"></i>"}

    {if $categories}
        <div class="table-responsive-wrapper">
            <table class="table table-middle table-responsive">
                <thead>
                    <tr>
                        <th width="1%" class="left mobile-hide">
                            {include file="common/check_items.tpl"}
                        </th>
                        <th width="5%">
                            <a class="cm-ajax" href="{"`$c_url`&sort_by=position&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">
                                {__("position")}{if $search.sort_by == "position"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}
                            </a>
                        </th>
                        <th width="25%" class="nowrap left">
                            <a class="cm-ajax" href="{"`$c_url`&sort_by=name&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">
                                {__("name")}{if $search.sort_by == "name"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}
                            </a>
                        </th>
                        <th width="50%">{__("description")}</th>
                        <th width="15%" class="mobile-hide center">&nbsp;</th>
                        <th width="10%" class="right">
                            <a class="cm-ajax" href="{"`$c_url`&sort_by=status&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">
                                {__("status")}{if $search.sort_by == "status"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}
                            </a>
                        </th>
                    </tr>
                </thead>

                {foreach from=$categories item="cat_data"}
                    {$additional_class="cm-no-hide-input"}
                    {$status_display=""}
                    <tr class="cm-row-status-{$cat_data.status|lower} {$additional_class}">
                        <td>
                            <input name="category_ids[]" type="checkbox" value="{$cat_data.category_id}" class="cm-item" />
                        </td>
                        <td class="left" data-th="{__("position")}">
                            <input type="text" name="categories[{$cat_data.category_id}][position]" value="{$cat_data.position}" class="input-small input-hidden"/>
                        </td>
                        <td class="left" data-th="{__("name")}">
                            <input type="text" name="categories[{$cat_data.category_id}][category]" size="40" value="{$cat_data.category}" class="input-short input-hidden"/>
                            {include file="views/companies/components/company_name.tpl" object=$cat_data}
                        </td>
                        <td class="left" data-th="{__("description")}">
                            <textarea name="categories[{$cat_data.category_id}][description]" cols="35" rows="3" class="cm-wysiwyg input-large">{$cat_data.description}</textarea>
                        </td>
                        <td class="right mobile-hide">
                            {btn type="list" href="cp_waranty_cat.delete?category_id=`$cat_data.category_id`" class="cm-confirm btn" icon="icon-trash"}
                        </td>
                        <td class="right" data-th="{__("status")}">
                            {include file="common/select_popup.tpl" popup_additional_class="dropleft" display=$status_display id=$cat_data.category_id status=$cat_data.status hidden=false object_id_name="category_id" table="cp_vp_warranty_categories"}
                        </td>
                    </tr>
                {/foreach}
            </table>
        </div>
    {else}
        <p class="no-items">{__("no_data")}</p>
    {/if}

    {include file="common/pagination.tpl"}

    {capture name="buttons"}
        {capture name="tools_list"}
            {if $categories}
                <li>{btn type="delete_selected" dispatch="dispatch[cp_waranty_cat.m_delete]" form="war_category_manage_form"}</li>
            {/if}
        {/capture}
        {dropdown content=$smarty.capture.tools_list}
        {if $categories}
            {include file="buttons/save.tpl" but_name="dispatch[cp_waranty_cat.m_update]" but_role="submit-link" but_target_form="war_category_manage_form"}
        {/if}
    {/capture}
    </form>
{capture name="sidebar"}
    {include file="addons/cp_vendor_panel/views/cp_waranty_cat/category_search.tpl" dispatch="cp_waranty_cat.manage" view_type="cats"}
{/capture}
{/capture}
{include file="common/mainbox.tpl" title=__("cp_vp_waranty_cat") content=$smarty.capture.mainbox tools=$smarty.capture.tools select_languages=true buttons=$smarty.capture.buttons adv_buttons=$smarty.capture.adv_buttons sidebar=$smarty.capture.sidebar}
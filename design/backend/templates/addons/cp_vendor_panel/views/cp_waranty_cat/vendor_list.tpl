
{assign var="redirect_url" value=$config.current_url|escape:url}

<div class="items-container" id="cp_vendor_warranties">
{include file="common/subheader.tpl" title=__("cp_vp_add_brand") target="#cp_vp_add_brand"}
<div id="cp_vp_add_brand" class="collapsed in">
    <form action="{""|fn_url}" method="post" name="add_warranty_categories" class="form-horizontal form-edit ">
        <input type="hidden" name="selected_section" value="cp_vp_brands_for_work" />
        <input type="hidden" name="company_id" value="{$smarty.request.company_id}" />
        <input type="hidden" name="redirect_url" value="{$config.current_url}" />
        <fieldset>
            <div class="control-group" id="cp_prod_feat_var_sel">
                <label class="control-label cm-required" for="cp_product_feature_variant">{__("brand")}</label>
                <div class="controls">
                    <div class="object-selector">
                        <select id="cp_product_feature_variant"
                                class="cp-group-selector cm-object-selector"
                                name="warranty_data[variant_id]"
                                data-ca-enable-images="true"
                                data-ca-image-width="30"
                                data-ca-image-height="30"
                                data-ca-load-via-ajax="true"
                                data-ca-placeholder="{__("search")}"
                                data-ca-close-on-select="true"
                                data-ca-enable-search="true"
                                data-ca-page-size="10"
                                data-ca-data-url="{"product_features.get_variants_list?include_empty=Y&feature_id=`$cp_brand_feature`&lang_code=`$descr_sl`"|fn_url nofilter}"
                                data-ca-placeholder="-{__("none")}-"
                                data-ca-allow-clear="true">
                                <option value="">-{__("none")}-</option>
                            {foreach from=$variants item="vars"}
                                <option value="{$vars.variant_id}">{$vars.variant}</option>
                            {/foreach}
                            <option value="">-{__("none")}-</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="table-responsive-wrapper">
                <table class="table table-middle table--relative table-responsive" width="100%">
                <thead class="cm-first-sibling">
                <tr>
                    <th width="5%">{__("position")}</th>
                    <th width="30%">{__("category")}</th>
                    <th width="35%">{__("cp_vp_term_of_warranty")}</th>
                    <th width="15%">&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                {math equation="x+1" x=0 assign="new_key"}
                <tr class="{cycle values="table-row , " reset=1}{$no_hide_input_if_shared_product}" id="box_add_war_category">
                    <td width="5%" data-th="{__("position")}">
                        <input type="text" name="warranty_data[categories][{$new_key}][position]" value="0" class="input-micro" />
                    </td>
                    <td width="50%" data-th="{__("category")}">
                        {include file="addons/cp_vendor_panel/pickers/categories/picker.tpl" data_id="add_location_category" input_name="warranty_data[categories][{$new_key}][category_id]" hide_link=true hide_delete_button=true display_input_id="elm_war_category_id"}
                    </td>
                    <td width="15%" data-th="{__("cp_vp_term_of_warranty")}">
                        <input type="text" name="warranty_data[categories][{$new_key}][warranty_term]" value="0" size="32" class="input-small" />
                    </td>
                    <td width="15%" class="right">
                        {include file="buttons/multiple_buttons.tpl" item_id="add_war_category"}
                    </td>
                </tr>
                </tbody>
                </table>
            </div>
            <div class="buttons-container left">
                {include file="buttons/button.tpl" but_text=__("cp_vp_add_brand") but_role="submit" but_name="dispatch[companies.add_warranty_cats]" but_target_form="add_warranty_categories"}
            </div>
        </fieldset>
    </form>
    <hr />
</div>
{include file="common/subheader.tpl" title=__("cp_vp_brands_for_work") target="#cp_vp_brands_cats"}
<div id="cp_vp_brands_cats" class="collapsed in">
    {include file="common/pagination.tpl" save_current_page=true save_current_url=true}
    {if $cp_vp_warranties}
        <form action="{""|fn_url}" method="post" name="manage_warranty_categories" class="form-horizontal form-edit ">
            <input type="hidden" name="selected_section" value="cp_vp_brands_for_work" />
            <input type="hidden" name="company_id" value="{$smarty.request.company_id}" />
            <input type="hidden" name="redirect_url" value="{$config.current_url}" />
            <div class="buttons-container right">
                {include file="buttons/button.tpl" but_text=__("cp_vp_save_brands") but_role="submit" but_name="dispatch[companies.update_war_brands]" but_target_form="manage_warranty_categories"}
            </div>
            <div class="table-responsive-wrapper">
                <table class="table table-middle table--relative table-objects table-responsive">
                    <thead class="cm-first-sibling">
                    </thead>
                    {foreach from=$cp_vp_warranties key="variant_id" item="war_cats"}
                        {$brand_name=$variant_id|fn_cp_vp_get_brand_name:$smarty.const.CART_LANGUAGE}
                        <tr>
                            <td colspan="3" class="cp-vp__brand_war">{__("brand")}: <strong>{$brand_name}</strong></td>
                            <td class="right mobile-hide">
                                {btn type="list" href="companies.cp_warranty_delete_brand?variant_id=`$variant_id`&company_id=`$smarty.request.company_id`" class="cm-confirm cm-post btn" icon="icon-trash"}
                                <!--div class="hidden-tools">
                                {capture name="tools_list"}
                                    <li>{btn type="list" text=__("cp_vp_delete_brand") class="cm-confirm cm-post" href="companies.cp_warranty_delete_brand?variant_id=`$variant_id`&company_id=`$smarty.request.company_id`"}</li>
                                {/capture}
                                {dropdown content=$smarty.capture.tools_list}
                                </div-->
                            </td>
                        </tr>
                        <tr>
                            <th width="5%">{__("position")}</th>
                            <th width="30%">{__("category")}</th>
                            <th width="35%">{__("cp_vp_term_of_warranty")}</th>
                            <th width="15%" class="mobile-hide center">&nbsp;</th>
                        </tr>
                        {foreach from=$war_cats item="war_data"}
                            <tr class="">
                                <td width="5%" data-th="{__("position")}">
                                    <input type="text" name="warranty_data[{$variant_id}][{$war_data.category_id}][position]" value="{$war_data.position}" class="input-micro" />
                                </td>
                                <td width="50%" data-th="{__("category")}">
                                    {include file="addons/cp_vendor_panel/pickers/categories/picker.tpl" data_id="location_category" input_name="warranty_data[{$variant_id}][{$war_data.category_id}][category_id]" item_ids="{$war_data.category_id}" hide_link=true hide_delete_button=true display_input_id="elm_war_category_id_`$war_data.category_id`"}
                                </td>
                                <td width="15%" data-th="{__("cp_vp_term_of_warranty")}">
                                    <input type="text" name="warranty_data[{$variant_id}][{$war_data.category_id}][warranty_term]" value="{$war_data.warranty_term}" size="32" class="input-small" />
                                </td>
                                <td class="right mobile-hide">
                                    {btn type="list" href="companies.cp_warranty_delete?category_id=`$war_data.category_id`&company_id=`$war_data.company_id`" class="cm-confirm cm-post btn" icon="icon-trash"}
                                    <!--div class="hidden-tools">
                                    {capture name="tools_list"}
                                        <li>{btn type="list" text=__("delete") class="cm-confirm cm-post" href="companies.cp_warranty_delete?category_id=`$war_data.category_id`&company_id=`$war_data.company_id`"}</li>
                                    {/capture}
                                    {dropdown content=$smarty.capture.tools_list}
                                    </div-->
                                </td>
                            </tr>
                        {/foreach}
                    {/foreach}
                </table>
            </div>
        </form>
    {else}
        <p>{__("no_data")}</p>
    {/if}
    {include file="common/pagination.tpl"}
</div>
<!--cp_vendor_warranties--></div>
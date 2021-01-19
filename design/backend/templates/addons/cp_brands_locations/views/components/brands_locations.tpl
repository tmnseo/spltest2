{assign var="redirect_url" value=$config.current_url|escape:url}

<div class="items-container" id="cp_brands_locations">
{include file="common/subheader.tpl" title=__("cp_brands_locations.brands_title") target="#cp_add_brand_locations"}
    <div id="cp_add_brand_locations" class="collapsed in">
        <form action="{""|fn_url}" method="post" name="cp_brand_locations" class="form-horizontal form-edit ">
            <input type="hidden" name="selected_section" value="cp_brands_locations" />
            <input type="hidden" name="company_id" value="{$smarty.request.company_id}" />
            <input type="hidden" name="brand_data[company_id]" value="{$smarty.request.company_id}" />
            <input type="hidden" name="redirect_url" value="{$config.current_url}" />
            <fieldset>
                <div class="control-group" id="cp_prod_feat_var_sel">
                    <label class="control-label cm-required" for="cp_brand_feature_variant">{__("brand")}</label>
                    <div class="controls">
                        <div class="object-selector">
                            <select id="cp_brand_feature_variant"
                                    class="cp-group-selector cm-object-selector"
                                    name="brand_data[brand_variant_id]"
                                    data-ca-enable-images="true"
                                    data-ca-image-width="30"
                                    data-ca-image-height="30"
                                    data-ca-load-via-ajax="true"
                                    data-ca-placeholder="{__("search")}"
                                    data-ca-close-on-select="true"
                                    data-ca-enable-search="true"
                                    data-ca-page-size="10"
                                    data-ca-data-url="{"product_features.get_variants_list?include_empty=Y&feature_id=`$cp_brand_feature`&lang_code=`$descr_sl`"|fn_url nofilter}"
                                    data-ca-allow-clear="true"
                                    onchange="cp_get_existed_destinations(this);">
                                    <option value="">-{__("none")}-</option>
                                {foreach from=$variants item="vars"}
                                    <option value="{$vars.variant_id}">{$vars.variant}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                </div>
                <div class="control-group " id="cp_locations_{$smarty.request.company_id}">
                    <label class="control-label">{__("store_locator.show_to")}:</label>
                    <div class="controls">
                        {foreach from=$destinations item=destination}
                            <label class="checkbox inline" for="destinations_{$destination.destination_id}">
                                <input
                                    type="checkbox"
                                    name="brand_data[destinations_ids][]"
                                    class="store-locator__destination"
                                    id="destinations_{$destination.destination_id}"
                                    {if $cp_selected_destinations && $destination.destination_id|in_array:$cp_selected_destinations}
                                        checked="checked"
                                    {/if}
                                    value="{$destination.destination_id}"
                                />{$destination.destination}
                            </label>
                        {/foreach}
                    </div>
                <!--cp_locations_{$smarty.request.company_id}--></div>
                <div class="buttons-container left">
                    {include file="buttons/button.tpl" but_text=__("cp_brands_locations.add_locations") but_role="submit" but_name="dispatch[companies.add_brand_locations]" but_target_form="cp_brand_locations"}
                </div>
            </fieldset>
        </form>
        <hr />
    </div>
    {include file="common/subheader.tpl" title=__("cp_brands_locations.brands_table_title") target="#cp_brands_locations_table"}
    <div id="cp_brands_locations_table" class="collapsed in">
    
    {if $cp_brands_locations}
        <table class="table table-middle table--relative table-objects table-responsive">
            <thead class="">
                <tr>
                    <th width="15%">{__("brand")}</th>
                    <th>{__("store_locator.show_to")}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$cp_brands_locations item="brand_data"}
                    <tr class="">
                        <td width="15%" data-th="{__("brand")}">
                            {$brand_data.brand_name}
                        </td>
                        <td data-th="{__("store_locator.show_to")}">
                            {foreach from=$destinations item=destination}
                                <label class="checkbox inline" for="read_destinations_{$destination.destination_id}">
                                    <input
                                        type="checkbox"
                                        name="brand_data[destinations_ids][]"
                                        class="store-locator__destination"
                                        id="read_destinations_{$destination.destination_id}"
                                        {if $brand_data.destinations_ids && $destination.destination_id|in_array:$brand_data.destinations_ids}
                                            checked="checked"
                                        {/if}
                                        value="{$destination.destination_id}"
                                        disabled=disabled
                                    />{$destination.destination}
                                </label>
                            {/foreach}
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    {else}
        <p>{__("no_data")}</p>
    {/if}
    
    </div>
<!--cp_brands_locations--></div>
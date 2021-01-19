{hook name="products:manage_table_body"}

    {if "ULTIMATE"|fn_allowed_for}
        {if $runtime.company_id && $product.is_shared_product == "Y" && $product.company_id != $runtime.company_id}
            {assign var="hide_inputs_if_shared_product" value="cm-hide-inputs"}
            {assign var="no_hide_input_if_shared_product" value="cm-no-hide-input"}
        {else}
            {assign var="hide_inputs_if_shared_product" value=""}
            {assign var="no_hide_input_if_shared_product" value=""}
        {/if}
        {if !$runtime.company_id && $product.is_shared_product == "Y"}
            {assign var="show_update_for_all" value=true}
        {else}
            {assign var="show_update_for_all" value=false}
        {/if}
    {/if}

        <tr class="cm-row-status-{$product.status|lower} cm-longtap-target {$hide_inputs_if_shared_product}"
            data-ca-longtap-action="setCheckBox"
            data-ca-longtap-target="input.cm-item"
            data-ca-id="{$product.product_id}"
            data-ca-category-ids="{$product.category_ids|to_json}"
        >
            {hook name="products:manage_body"}
            <td width="6%" class="left mobile-hide">
            <input type="checkbox" name="product_ids[]" value="{$product.product_id}" class="cm-item cm-item-status-{$product.status|lower} hide" /></td>
            {if $search.cid && $search.subcats != "Y"}
            <td class="{if $no_hide_input_if_shared_product}{$no_hide_input_if_shared_product}{/if}">
                <input type="text" name="products_data[{$product.product_id}][position]" size="3" value="{$product.position}" class="input-micro" /></td>
            {/if}
            <td class="products-list__image">
                {include 
                        file="common/image.tpl" 
                        image=$product.main_pair.icon|default:$product.main_pair.detailed 
                        image_id=$product.main_pair.image_id 
                        image_width=$settings.Thumbnails.product_admin_mini_icon_width 
                        image_height=$settings.Thumbnails.product_admin_mini_icon_height 
                        href="products.update?product_id=`$product.product_id`"|fn_url
                        image_css_class="products-list__image--img"
                        link_css_class="products-list__image--link"
                }
            </td>
            <td class="product-name-column" data-th="{__("name")}">
                <input type="hidden" name="products_data[{$product.product_id}][product]" value="{$product.product}" {if $no_hide_input_if_shared_product} class="{$no_hide_input_if_shared_product}"{/if} />
                <a class="row-status" title="{$product.product|strip_tags}" href="{"products.update?product_id=`$product.product_id`"|fn_url}">{$product.product|truncate:40 nofilter}</a>
                <div class="product-list__labels">
                    {hook name="products:product_additional_info"}
                        <div class="product-code">
                            <span class="product-code__label">{$product.product_code}</span>
                        </div>
                    {/hook}
                </div>
                {include file="views/companies/components/company_name.tpl" object=$product}
            </td>
            <td width="13%" class="{if $no_hide_input_if_shared_product}{$no_hide_input_if_shared_product}{/if}" data-th="{__("price")}">
                {include file="buttons/update_for_all.tpl" display=$show_update_for_all object_id="price_`$product.product_id`" name="update_all_vendors[price][`$product.product_id`]"}
                <input type="text" name="products_data[{$product.product_id}][price]" size="6" value="{$product.price|fn_format_price:$primary_currency:null:false}" class="input-mini input-hidden"/>
            </td>
            <td width="12%" class="mobile-hide" data-th="{__("list_price")}">
                {hook name="products:list_list_price"}
                    <input type="text" name="products_data[{$product.product_id}][list_price]" size="6" value="{$product.list_price|fn_format_price:$primary_currency:null:false}" class="input-mini input-hidden" />
                {/hook}
            </td>
            {if $search.order_ids}
            <td width="9%" data-th="{__("purchased_qty")}">{$product.purchased_qty}</td>
            <td width="9%" data-th="{__("subtotal_sum")}">{$product.purchased_subtotal}</td>
            {/if}
            <td width="9%" data-th="{__("quantity")}">
                {hook name="products:list_quantity"}
                    {if $product.tracking == "ProductTracking::TRACK_WITH_OPTIONS"|enum}
                        {include file="buttons/button.tpl" but_text=__("edit") but_href="product_options.inventory?product_id=`$product.product_id`" but_role="edit"}
                    {else}
                        <input type="text" name="products_data[{$product.product_id}][amount]" size="6" value="{$product.inventory_amount|default:$product.amount}" class="input-full input-hidden" />
                    {/if}
                {/hook}
            </td>
            {/hook}
            <td width="9%" class="nowrap mobile-hide">
                <div class="hidden-tools">
                    {capture name="tools_list"}
                        {hook name="products:list_extra_links"}
                            {if $auth.user_type != 'V'}
                                <li>{btn type="list" text=__("edit") href="products.update?product_id=`$product.product_id`"}</li>
                                {if !$hide_inputs_if_shared_product}
                                    <li>{btn type="list" text=__("delete") class="cm-confirm" href="products.delete?product_id=`$product.product_id`" method="POST"}</li>
                                {/if}
                            {/if}
                        {/hook}
                    {/capture}
                    {dropdown content=$smarty.capture.tools_list}
                </div>
            </td>
            <td width="9%" class="right nowrap" data-th="{__("status")}">

                {if !fn_cp_check_demo_mode($product.company_id)} 
                    {$non_editable_status = false}
                {else}
                    {$non_editable_status = true}
                {/if}

                {include file="views/products/components/status_on_manage.tpl"
                    popup_additional_class="dropleft"
                    id=$product.product_id
                    status=$product.status
                    hidden=true
                    object_id_name="product_id"
                    table="products"
                    non_editable_status=$non_editable_status
                }
            </td>
        </tr>
        {/hook}
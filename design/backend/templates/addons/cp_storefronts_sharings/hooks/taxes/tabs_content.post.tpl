{if fn_allowed_for("MULTIVENDOR:ULTIMATE")|| $is_sharing_enabled}
        <div class="hidden" id="content_storefronts">
            {$add_storefront_text = __("add_storefronts")}
            {if fn_allowed_for("ULTIMATE")}
                {$add_storefront_text = __("add_companies")}
            {/if}
            {include file="pickers/storefronts/picker.tpl"
                multiple=true
                input_name="tax_data[cp_storefront_ids]"
                item_ids=$cp_storefront_ids
                data_id="storefront_ids"
                but_meta="pull-right"
                no_item_text=__("cp_no_selected_storefronts")
                but_text=$add_storefront_text
                view_only=($is_sharing_enabled && $runtime.company_id)
            }
        <!--content_tab_storefronts_{$id}--></div>
{/if}
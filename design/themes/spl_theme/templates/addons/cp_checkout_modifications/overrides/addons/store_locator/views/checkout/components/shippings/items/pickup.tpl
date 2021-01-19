{$company_data = $store.company_id|fn_get_company_data}
 <label for="store_{$group_key}_{$shipping.shipping_id}_{$store.store_location_id}" 
        class="ty-one-store js-pickup-search-block-{$group_key} {if ($old_store_id == $store.store_location_id) || ($store_count == 1)}ty-sdek-office__selected{/if}"
>
    <input type="radio" 
            class="ty-one-store__radio ty-one-store__radio--{$group_key} cm-sl-pickup-select-store"
            name="select_store[{$group_key}][{$shipping.shipping_id}]"
            value="{$store.store_location_id}"
            {if ($old_store_id == $store.store_location_id) || ($store_count == 1)}
            checked="checked"
            {/if}
            id="store_{$group_key}_{$shipping.shipping_id}_{$store.store_location_id}"
            data-ca-pickup-select-store="true"
            data-ca-shipping-id="{$shipping.shipping_id}"
            data-ca-group-key="{$group_key}"
            data-ca-location-id="{$store.store_location_id}"
    />

    <div class="ty-sdek-store__label ty-one-store__label">
        <div class="ty-one-store__description">
            <span class="ty-one-store__vendor">
                <span class="ty-one-store__label_label">{__("vendor")}: </span>
                <span class="ty-one-store__label_value">{$company_data.company} </span>
            </span>
            <span class="ty-one-store__stock">
                <span class="ty-one-store__label_label">{__("stock")}: </span>
                <span class="ty-one-store__label_value">{$store.city},<br> {$store.pickup_address} </span>
            </span>
            {if $store.pickup_time}
            <span class="ty-one-store__worktime">
                <span class="ty-one-store__label_label">{__("working_hours")}: </span>
                <span class="ty-one-store__label_value">{$store.pickup_time} </span>
            </span>
            {/if}
            {if $store.pickup_phone}
            <span class="ty-one-store__phone">
                <span class="ty-one-store__label_label">{__("phone")}: </span>
                <span class="ty-one-store__label_value">{$store.pickup_phone}</span>
            </span>
            {/if}

            {if $store.pickup_rate && $store.pickup_rate > 0}
            <span class="ty-one-store__name-rate">
                <span class="ty-one-store__label_label">{__("cost")}: </span>
                <span class="ty-one-store__label_value">{include file="common/price.tpl" value=$store.pickup_rate}</span>
            </span>
            {/if}
            
        </div>
    </div>
</label>
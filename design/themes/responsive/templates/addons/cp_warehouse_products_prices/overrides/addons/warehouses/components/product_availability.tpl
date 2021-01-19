{*
int $in_stock_stores_count
int $available_stores_count
int $product_id
*}
{if $in_stock_stores_count || $available_stores_count}
    <div class="ty-warehouses-shipping__item">
        <div class="ty-warehouses-shipping__label">
            <span 
            >
                <i class="ty-icon-cart"></i>
                <span class="">
                    {if $in_stock_stores_count}
                        {__("warehouses.product_in_stock")}
                    {else}
                        {__("warehouses.product_available_if_ordered")}
                    {/if}
                </span>
            </span>
            <div class="ty-warehouses-shipping__value">
                {if $in_stock_stores_count}
                    {__("warehouses.in_n_stores", [
                        $in_stock_stores_count
                    ])}
                {else}
                    {__("warehouses.in_n_stores", [
                        $available_stores_count
                    ])}
                {/if}
            </div>
        </div>
    </div>
{/if}
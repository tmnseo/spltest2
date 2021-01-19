{if !empty($runtime.company_id)} 
    {$products = array($runtime.company_id)|fn_get_companies_active_products_count}

{/if}
{if !empty($general_stats.products) || !empty($products[$runtime.company_id])}
    <div class="dashboard-card">
        <div class="dashboard-card-title">{__("active_products")}</div>
        <div class="dashboard-card-content">
            {if $general_stats.products.total_products}
                <h3><a href="{"products.manage?status=A"|fn_url}">{$general_stats.products.total_products|number_format}</a></h3>
            {elseif $products[$runtime.company_id]}
                <h3><p>{$products[$runtime.company_id]}</p></h3>
            {/if}
        </div>
    </div>
{/if}
{if !empty($general_stats.products)}
    {if $settings.General.inventory_tracking == "Y"}
        <div class="dashboard-card">
            <div class="dashboard-card-title">{__("out_of_stock_products")}</div>
            <div class="dashboard-card-content">
                <h3><a href="{"products.manage?amount_from=&amount_to=0&tracking[0]={"ProductTracking::TRACK_WITHOUT_OPTIONS"|enum}&tracking[1]={"ProductTracking::TRACK_WITH_OPTIONS"|enum}"|fn_url}">{$general_stats.products.out_of_stock_products|number_format}</a></h3>
            </div>
        </div>
    {/if}
{/if}
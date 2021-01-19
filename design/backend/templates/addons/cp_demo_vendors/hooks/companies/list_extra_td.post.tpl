{if $auth.user_type == 'A'}
    <td class="row-status" data-th="{__("cp_demo")}">
        {include file="common/switcher.tpl"
            meta = "company-switch-storefront-status-button storefront__status"
            checked = $company.cp_is_demo == 'Y'
            extra_attrs = [
                "data-ca-submit-url" => 'companies.update_demo_status',
                "data-ca-storefront-id" => $company.company_id,
                "data-ca-opened-status" => 'Y',
                "data-ca-closed-status" => 'N',
                "data-ca-return-url" => $return_url
            ]
        } 
    </td>
{/if}
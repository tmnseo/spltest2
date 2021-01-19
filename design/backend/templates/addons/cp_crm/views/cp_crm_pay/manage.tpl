{capture name="mainbox"}

    <form action="{""|fn_url}" method="post" name="states_form" class="{if $runtime.company_id} cm-hide-inputs{/if}">
        <input type="hidden" name="country_code" value="{$search.country}" />

        {include file="common/pagination.tpl" save_current_page=true save_current_url=true}

        {if $payments}
            <div class="table-responsive-wrapper">
                <table width="100%" class="table table-middle table--relative table-responsive">
                    <thead>
                    <tr>
                        <th width="1%" class="mobile-hide">{include file="common/check_items.tpl"}</th>
                        <th width="5%">{__("cp_crm_payment_order_date")}</th>
                        <th width="5%">{__("cp_crm_payment_order_number")}</th>

                        <th width="45%">{__("cp_crm_payment_purpose")}</th>
                        <th width="10%">{__("cp_crm_customer_name")}</th>
                        <th width="10%">{__("cp_crm_customer_tin")}</th>
                        <th width="10%">{__("cp_crm_bank_bic")}</th>
                        <th width="10%">{__("cp_crm_amount_total")}</th>
                        <th width="5%">{__("cp_crm_amount_vat")}</th>

                        <th width="5%">&nbsp;</th>
                        <th class="right" width="10%">{__("status")}</th>
                    </tr>
                    </thead>
                    {foreach from=$payments item=payment}
                        <tr class="cm-row-status-{$payment.status|lower}">
                            <td class="mobile-hide">
                                <input type="checkbox" name="payment_ids[]" value="{$payment.payment_id}" class="cm-item" /></td>

                            <td data-th="{__("cp_crm_payment_order_date")}">
                                <input disabled="disabled" type="text" name="payment_data[{$payment.payment_id}][payment_order_date]" size="55" value="{$payment.payment_order_date}" class=""/>
                            </td>

                            <td data-th="{__("cp_crm_payment_order_number")}">
                                <input disabled="disabled" type="text" name="payment_data[{$payment.payment_id}][payment_order_number]" size="55" value="{$payment.payment_order_number}" class=""/>
                            </td>


                            <td data-th="{__("cp_crm_payment_purpose")}">
                                {$payment.payment_purpose}
                            </td>
                            <td data-th="{__("cp_crm_customer_name")}">
                                {$payment.customer_name}
                            </td>
                            <td data-th="{__("cp_crm_customer_tin")}">
                                {$payment.customer_tin}
                            </td>
                            <td data-th="{__("cp_crm_bank_bic")}">
                                {$payment.bank_bic}
                            </td>
                            <td data-th="{__("cp_crm_amount_total")}">
                                {$payment.amount_total}
                            </td>
                            <td data-th="{__("cp_crm_amount_vat")}">
                                {$payment.amount_vat}
                            </td>

                            <td class="nowrap" data-th="{__("tools")}">
                                {capture name="tools_list"}
                                    <li>{btn type="list" class="cm-confirm" text=__("delete") href="cp_crm_pay.delete?payment_id=`$payment.payment_id`" method="POST"}</li>
                                {/capture}
                                <div class="hidden-tools">
                                    {dropdown content=$smarty.capture.tools_list}
                                </div>
                            </td>
                            <td class="right" data-th="{__("status")}">
                                {*$has_permission = fn_check_permissions("tools", "update_status", "admin", "GET", ["table" => "states"])*}
                                {include file="common/select_popup.tpl" id=$payment.payment_id status=$payment.status hidden="" object_id_name="payment_id" table=$table_name non_editable=false}
                            </td>
                        </tr>
                    {/foreach}
                </table>
            </div>
        {else}
            <p class="no-items">{__("no_data")}</p>
        {/if}

        {include file="common/pagination.tpl"}

    </form>

    {capture name="tools"}

    {/capture}

    {capture name="buttons"}
        {capture name="tools_list"}
            {if $cities}
                <li>{btn type="delete_selected" dispatch="dispatch[cp_crm_pay.m_delete]" form="states_form"}</li>
            {/if}

        {/capture}
        {dropdown content=$smarty.capture.tools_list}

        {if $payments}
            {include file="buttons/save.tpl" but_name="dispatch[cp_crm_pay.m_update]" but_role="action" but_target_form="states_form" but_meta="cm-submit"}
        {/if}
    {/capture}



{/capture}
{include file="common/mainbox.tpl" title=__("cp_crm_title") content=$smarty.capture.mainbox adv_buttons=$smarty.capture.adv_buttons buttons=$smarty.capture.buttons sidebar=$smarty.capture.sidebar select_languages=true}
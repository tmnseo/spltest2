{capture name="mainbox"}
    {include file="common/pagination.tpl"}
    {if $invoices}
    <form>
    <div id="invoices_table">
        <input type="hidden" name="redirect_url" value="{$config.current_url}" />
        <table width="100%" class="table table-middle">
            <thead>
            <tr>
                <th class="center" width="10%">{__("cp_invoices_for_accounting.order_id")}</th>
                <th class="center" width="15%">{__("cp_invoices_for_accounting.cp_payment_order_number")}</th>
                <th class="center" width="5%">{__("cp_invoices_for_accounting.order_status")}</th>
            </tr>
            </thead>

            {foreach from=$invoices item=invoice}
                <tbody>
                <tr>
                    <td class="center">{$invoice.order_id}</td>
                    <td class="center">{$invoice.cp_payment_order_number}</td>
                    <td class="center">{$invoice.status_descr}</td>
                </tr>
                </tbody>
            {/foreach}
        </table>
    <!--invoices_table--></div>
    {else}
        <p class="no-items">{__("no_data")}</p>
    {/if}

    {include file="common/pagination.tpl"}
    {capture name="buttons"}
        <a class="cm-post btn" href="{"cp_invoices_for_accounting.go"|fn_url}" >{__("cp_invoices_for_accounting.download_invoices")}</a>
    {/capture}
{/capture}
{include file="common/mainbox.tpl" title=__("cp_invoices_for_accounting") content=$smarty.capture.mainbox buttons=$smarty.capture.buttons}
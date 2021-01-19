<?php
/*****************************************************************************
*                                                        © 2013 Cart-Power   *
*           __   ______           __        ____                             *
*          / /  / ____/___ ______/ /_      / __ \____ _      _____  _____    *
*      __ / /  / /   / __ `/ ___/ __/_____/ /_/ / __ \ | /| / / _ \/ ___/    *
*     / // /  / /___/ /_/ / /  / /_/_____/ ____/ /_/ / |/ |/ /  __/ /        *
*    /_//_/   \____/\__,_/_/   \__/     /_/    \____/|__/|__/\___/_/         *
*                                                                            *
*                                                                            *
* -------------------------------------------------------------------------- *
* This is commercial software, only users who have purchased a valid license *
* and  accept to the terms of the License Agreement can install and use this *
* program.                                                                   *
* -------------------------------------------------------------------------- *
* website: https://store.cart-power.com                                      *
* email:   sales@cart-power.com                                              *
******************************************************************************/

use Tygh\Registry;
use Tygh\Addons\CpInvoicesForAccounting\InvoiceManager\InvoiceManager;
use Tygh\Addons\CpInvoicesForAccounting\FileManager\FileManager;
use Tygh\Addons\CpInvoicesForAccounting\ServiceProvider;
use Tygh\Addons\CpStatusesRules\OrderStatuses\OrderStatuses;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'go') {

        $cron_pass = ServiceProvider::cronPass();
        return [CONTROLLER_STATUS_REDIRECT, 'cp_invoices_for_accounting.download_invoices&cron_pass='.$cron_pass.'&is_test=Y'];
    }

    return;

}

if ($mode == 'manage') {

    $params = $_REQUEST;
    $params['cp_statuses_after_paid'] = OrderStatuses::getStatusesAfterPaid();
    list($invoices, $search) = InvoiceManager::getUnloadedInvoices($params, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);
    
    Tygh::$app['view']->assign('invoices', $invoices);
    Tygh::$app['view']->assign('search', $search);

}
if ($mode == 'download_invoices') {

    $cron_pass = ServiceProvider::cronPass();

    if (!empty($_REQUEST['cron_pass']) && $cron_pass == $_REQUEST['cron_pass']) {
        
        $params['cp_statuses_after_paid'] = OrderStatuses::getStatusesAfterPaid();
        $params['get_all'] = true;

        list($invoices,) = InvoiceManager::getUnloadedInvoices($params, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);

        if (!empty($invoices)) {

            $file_manager = new FileManager();

            $zoho = new Zoho();
            $contact = $zoho->createContact("ADMIN", Registry::get('settings.Company.company_site_administrator'));

            foreach ($invoices as $invoice_data) {

                $filepath = $file_manager->saveFile($invoice_data['order_id']);   
                InvoiceManager::markAsUploaded($invoice_data['order_id']);
            }
        }
        
    }

    if (!empty($_REQUEST['is_test']) && $_REQUEST['is_test'] == 'Y') {
        return [CONTROLLER_STATUS_REDIRECT, 'cp_invoices_for_accounting.manage'];  
    }

    exit;
}
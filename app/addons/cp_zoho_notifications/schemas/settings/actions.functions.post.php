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

use Tygh\Addons\CpZohoNotifications\Service;
use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_settings_variants_addons_cp_zoho_notifications_order_status_processed()
{
    return Service::getOrderStatusesForSettings();
}
function fn_settings_variants_addons_cp_zoho_notifications_order_status_confirmed()
{
    return Service::getOrderStatusesForSettings();
}
function fn_settings_variants_addons_cp_zoho_notifications_order_status_paid()
{   
    return Service::getOrderStatusesForSettings();   
}
function fn_settings_variants_addons_cp_zoho_notifications_order_status_paid_after_cancellation()
{
    return Service::getOrderStatusesForSettings(); 
}
function fn_settings_variants_addons_cp_zoho_notifications_order_status_completed()
{
    return Service::getOrderStatusesForSettings();
}
function fn_settings_variants_addons_cp_zoho_notifications_order_status_received()
{
    return Service::getOrderStatusesForSettings();
}
function fn_cp_zoho_notifications_cron_run_info()
{
    $admin_ind = Registry::get('config.admin_index');
    $__params = Registry::get('addons.cp_zoho_notifications');
    if (!empty($__params) && !empty($__params['cron_pass'])) {
        $cron_pass = $__params['cron_pass'];
    } else {
        $cron_pass = '';
    }
    $hint = '<b>' . __("cp_zoho_notifications.cron_info") . ':</b><br /><code>php ' . Registry::get('config.dir.root') .'/' . $admin_ind . ' --dispatch=cp_zoho_notifications.time_to_check --cron_pass=' . $cron_pass . '</code>';
    
    return $hint;
}

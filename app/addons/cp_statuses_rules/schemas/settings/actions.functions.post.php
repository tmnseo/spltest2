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

function fn_settings_variants_addons_cp_statuses_rules_order_status_placed()
{
    return Service::getOrderStatusesForSettings();
}
function fn_settings_variants_addons_cp_statuses_rules_order_status_cancel()
{
    return Service::getOrderStatusesForSettings();
}
function fn_settings_variants_addons_cp_statuses_rules_order_status_paid()
{   
    return Service::getOrderStatusesForSettings();   
}
function fn_settings_variants_addons_cp_statuses_rules_order_status_paid_after_cancellation()
{
    return Service::getOrderStatusesForSettings(); 
}
function fn_settings_variants_addons_cp_statuses_rules_order_status_confirmed()
{
    return Service::getOrderStatusesForSettings();
}
function fn_settings_variants_addons_cp_statuses_rules_order_status_refund()
{
    return Service::getOrderStatusesForSettings();
}
function fn_settings_variants_addons_cp_statuses_rules_order_status_cancel_with_refund()
{
    return Service::getOrderStatusesForSettings();
}
function fn_settings_variants_addons_cp_statuses_rules_order_status_completed()
{
    return Service::getOrderStatusesForSettings();
}
function fn_settings_variants_addons_cp_statuses_rules_order_status_received()
{
    return Service::getOrderStatusesForSettings();
}
function fn_settings_variants_addons_cp_statuses_rules_order_status_shipped()
{
    return Service::getOrderStatusesForSettings();
}
function fn_settings_variants_addons_cp_statuses_rules_order_status_waiting_for_payment()
{
    return Service::getOrderStatusesForSettings();
}
function fn_settings_variants_addons_cp_statuses_rules_order_status_vendor_return()
{
    return Service::getOrderStatusesForSettings();
}
function fn_settings_variants_addons_cp_statuses_rules_order_status_finished()
{
    return Service::getOrderStatusesForSettings();
}
function fn_cp_statuses_rules_cron_run_info()
{
    $admin_ind = Registry::get('config.admin_index');
    $__params = Registry::get('addons.cp_statuses_rules');
    if (!empty($__params) && !empty($__params['cron_pass'])) {
        $cron_pass = $__params['cron_pass'];
    } else {
        $cron_pass = '';
    }
    $hint = '<b>' . __("cp_statuses_rules.cron_info") . ':</b><br /><code>php ' . Registry::get('config.dir.root') .'/' . $admin_ind . ' --dispatch=cp_statuses_rules.check_statuses --cron_pass=' . $cron_pass . '</code>';
    
    return $hint;
}
function fn_cp_statuses_rules_cron_unpaid_orders_run_info()
{
    $admin_ind = Registry::get('config.admin_index');
    $__params = Registry::get('addons.cp_statuses_rules');
    if (!empty($__params) && !empty($__params['cron_pass'])) {
        $cron_pass = $__params['cron_pass'];
    } else {
        $cron_pass = '';
    }
    $hint = '<b>' . __("cp_statuses_rules.order_unpaid_cron_info") . ':</b><br /><code>php ' . Registry::get('config.dir.root') .'/' . $admin_ind . ' --dispatch=cp_statuses_rules.check_unpaid_orders --cron_pass=' . $cron_pass . '</code>';
    
    return $hint;
}
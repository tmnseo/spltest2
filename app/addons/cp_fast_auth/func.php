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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

/* HOOKS */
function fn_cp_fast_auth_change_order_status($status_to, $status_from, &$order_info, $force_notification, $order_statuses, $place_order)
{
    $auth_statuses = Registry::get("addons.cp_fast_auth.order_statuses");
    if (!empty($auth_statuses)) {
        $auth_statuses_keys = array_keys($auth_statuses);
        if (in_array($status_to, $auth_statuses_keys)) {
            $order_info['personal_link'] = fn_cp_generate_auth_link($order_info);
        }
    }
    /* for email templates */
    if (!empty($order_info['cp_payment_order_date'])) {

        $order_info['cp_payment_order_date'] = fn_date_format($order_info['cp_payment_order_date'], Registry::get('settings.Appearance.date_format'));
    }
    
    if (!empty($order_info['shipment_ids'])) {
        $shipment_id = current($order_info['shipment_ids']);
        $tracking_number = db_get_field("SELECT tracking_number FROM ?:shipments WHERE shipment_id = ?i", $shipment_id);
        if (!empty($tracking_number)) {
            $order_info['tracking_number'] = $tracking_number;
        }
    }
    /* for email templates */
}
/* HOOKS */
function fn_cp_generate_auth_link($order_info, $orders_href = "orders.search")
{   
    if (!empty($order_info['user_id'])) {
        $secret_key = md5(uniqid(""));

        $s_data = array(
            'user_id' => $order_info['user_id'],
            'secret_key' => $secret_key,
            'timestamp' => time()
        );

        $result = db_query("REPLACE INTO ?:cp_fast_auth_secret_keys ?e", $s_data);

        if (!empty($result)) {
            $orders_href .= "?cp_s_key=" . $secret_key;
        } 
    }

    return fn_url($orders_href,'C');
}
function fn_cp_auth_user($secret_key)
{   
    $result = false;
    $auth_info = db_get_row("SELECT * FROM ?:cp_fast_auth_secret_keys WHERE secret_key = ?s", $secret_key);

    if (!empty($auth_info['timestamp'])) {
        $result = fn_cp_check_secret_key_time($auth_info['timestamp'], $secret_key);
    }
    if (!empty($auth_info['user_id']) && $result === true) {
        $result = true;
    }

    if ($result === true) {

        fn_cp_delete_auth_tokens(array('secret_key' => $secret_key));
        fn_login_user($auth_info['user_id'], true);

    }elseif ($result === false) {
        fn_set_notification('W', __('warning'), __('cp_fast_auth.can_not_auth_user'));
        fn_redirect(fn_url());
    }
}
function fn_cp_check_secret_key_time($timestamp, $secret_key)
{
    $secret_key_lifetime = Registry::get('addons.cp_fast_auth.key_lifetime');

    if (($timestamp + ($secret_key_lifetime * 60)) < time()) {
        fn_cp_delete_auth_tokens(array('secret_key' => $secret_key));
        return false;
    }else {
        return true;
    }
}
function fn_cp_delete_auth_tokens($params)
{
    if (!empty($params['secret_key'])) {
        db_query("DELETE FROM ?:cp_fast_auth_secret_keys WHERE secret_key = ?s", $params['secret_key']);
    }
    if (!empty($params['clear_old'])) {
        $secret_key_lifetime = Registry::get('addons.cp_fast_auth.key_lifetime');

        db_query("DELETE FROM ?:cp_fast_auth_secret_keys WHERE `timestamp` < ?i", (time() - ($secret_key_lifetime * 60)));
    }
}
function fn_cp_fast_auth_cron_run_info()
{
    $admin_ind = Registry::get('config.admin_index');
    $__params = Registry::get('addons.cp_fast_auth');
    if (!empty($__params) && !empty($__params['cron_pass'])) {
        $cron_pass = $__params['cron_pass'];
    } else {
        $cron_pass = '';
    }
    $hint = '<b>' . __("cp_fast_auth.cron_info_text") . ':</b><br /><code>php ' . Registry::get('config.dir.root') .'/' . $admin_ind . ' --dispatch=cp_fast_auth.cron_clear_tokens --cron_pass=' . $cron_pass . '</code>';

    return $hint;
}
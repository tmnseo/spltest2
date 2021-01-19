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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if ($mode == 'update_confirm_status') {

        $params = array_merge([
            'storefront_id' => 0,              // order_id
            'status'        => 'N',            // cp_confirm_status
            'return_url'    => 'orders.manage',
        ], $_REQUEST);
        $order_id = $params['storefront_id'];
        $cp_confirm_status = $params['status'];
        /*gMelnikov modifs*/
        $check_vendor_order_number = db_get_field("SELECT cp_vendor_order_number FROM ?:orders WHERE order_id = ?i", $order_id);
        if (!$check_vendor_order_number){
            fn_set_notification('W', __('warning'), __('cp_warning_vendor_order_number'));
            $cp_confirm_status = 'N';
        }
        if ($cp_confirm_status == 'Y') {
            $cp_confirm_date = time();    
        }
        /*gMelnikov modifs*/
		$condition = fn_get_company_condition('?:orders.company_id');
        $result = db_query("UPDATE ?:orders SET cp_confirm_status = ?s, cp_confirm_date = ?i WHERE order_id = ?i $condition", $cp_confirm_status, $cp_confirm_date, $order_id);

        if ($cp_confirm_status == 'Y' && $result) {
            $addon_settings = Registry::get('addons.cp_confirm_order');

            if (!empty($addon_settings['order_status'])) {
                $notification_rules = array();

                if ($addon_settings['notify_user'] == 'N') {
                    $notification_rules['notify_user'] = false;
                } else {
                    $notification_rules['notify_user'] = true;                
                }

                if ($addon_settings['notify_vendor'] == 'N') {
                    $notification_rules['notify_vendor'] = false;
                } else {
                    $notification_rules['notify_vendor'] = true;                
                }

                if ($addon_settings['notify_department'] == 'N') {
                    $notification_rules['notify_department'] = false;
                } else {
                    $notification_rules['notify_department'] = true;                
                }

                if (fn_change_order_status($order_id, $addon_settings['order_status'], '', fn_get_notification_rules($notification_rules)) && defined('AJAX_REQUEST')) {
                    Tygh::$app['ajax']->assign('order_id', $order_id);
                    Tygh::$app['ajax']->assign('update_confirm_status', strtolower($addon_settings['order_status']));
                }
            }
        }

        if (defined('AJAX_REQUEST')) {
            Tygh::$app['ajax']->assign('result', $result);

            return [CONTROLLER_STATUS_OK, urldecode($params['return_url'])];
        }
	}

	return;
}

if ($mode == 'manage' || $mode == 'details') {
    Tygh::$app['view']->assign('cp_confirm_status', strtolower(Registry::get('addons.cp_confirm_order.order_status')));
}
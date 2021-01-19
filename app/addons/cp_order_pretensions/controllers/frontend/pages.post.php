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
    if ($mode == 'send_form') {

        $params['notify_department'] = $params['notify_user'] = $params['notify_vendor'] = 'Y';

        $page_id = !empty($_REQUEST['page_id']) ? $_REQUEST['page_id'] : null;
        $order_id = !empty($_REQUEST['order_id']) ? $_REQUEST['order_id'] : null;
        $pretension_page_id = Registry::get('addons.cp_order_pretensions.pretension_page_id');
        $pretension_order_status = Registry::get('addons.cp_order_pretensions.order_status');

        
        if (!empty($page_id) && !empty($order_id) && !empty($pretension_page_id) && $page_id == $pretension_page_id && !empty($pretension_order_status)) {

            fn_cp_create_zoho_request($_REQUEST);

            fn_change_order_status($order_id, $pretension_order_status, '', fn_get_notification_rules($params));

            return array(CONTROLLER_STATUS_REDIRECT, 'orders.search');
        }
    }
    return;
    
}

if ($mode == 'view') {

    $page_id = !empty($_REQUEST['page_id']) ? $_REQUEST['page_id'] : null;
    $order_id = !empty($_REQUEST['order_id']) ? $_REQUEST['order_id'] : null;
    $pretension_page_id = Registry::get('addons.cp_order_pretensions.pretension_page_id');
    $products = array();
    $order = array();
    $order_amount = 0;

    if (!empty($page_id) && !empty($order_id) && !empty($pretension_page_id) && $page_id == $pretension_page_id) {
        $order_info = fn_get_order_info($order_id);
        
        if (!empty($order_info)) {
            $order = array(
                'order_id' => $order_info['order_id'],
                'total' =>  $order_info['total'],
                //'currency' => db_get_field("SELECT  description FROM ?:currency_descriptions WHERE currency_code = ?s AND lang_code = ?s",$order_info['secondary_currency'], CART_LANGUAGE)
                'currency' => $order_info['secondary_currency']
            );
        }
        if (!empty($order_info['product_groups'])) {
            $product_group = current($order_info['product_groups']); //only one vedor for each order
            $order['company_name'] = $product_group['name'];
        }

        if (!empty($order_info['products'])) {
            foreach ($order_info['products'] as $cart_id => $product_data) {
                $order_amount += $product_data['amount'];
                $products[$product_data['product_id']] = array(
                    'product_id' => $product_data['product_id'],
                    'product' => $product_data['product'],
                    'price' => $product_data['price'],
                    'amount' => $product_data['amount']
                );
            }

            $order['amount'] = $order_amount;
        }
        
    }
    
    Tygh::$app['view']->assign([
        'products' => $products,
        'order_info' => $order
    ]);
}
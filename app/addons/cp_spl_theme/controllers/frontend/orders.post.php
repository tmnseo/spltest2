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
    return;
}

if ($mode == 'search') {

    $orders = Tygh::$app['view']->getTemplateVars('orders');

    if (!empty($orders)) {
        foreach ($orders as &$order_data) {
            $order_data['extra'] = db_get_field("SELECT extra FROM ?:order_details WHERE order_id = ?i",$order_data['order_id']);
            if (!empty($order_data['extra'])) {
                $extra = unserialize($order_data['extra']);
                if (!empty($extra['cp_recipient_data'])) {
                    $order_data['cp_recipient_data'] = $extra['cp_recipient_data'];
                }
            }
        }
        unset($order_data);
        Tygh::$app['view']->assign('orders', $orders);
    }

}
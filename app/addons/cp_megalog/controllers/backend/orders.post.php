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

    if ($mode == 'update_details') {
        $types = fn_get_schema('cp_ml', 'types');
        if (!empty($types) && !empty($types['orders']) && !empty($types['orders']['update_details'])) {
            $req_data = array(
                'order_id' => $_REQUEST['order_id']
            );
            $put_data = array(
                'controller' => 'orders',
                'mode' => 'update_details',
                'method' => 'post',
                'timestamp' => time(),
                'user_id' => !empty(Tygh::$app['session']['auth']['user_id']) ? Tygh::$app['session']['auth']['user_id'] : 0,
                'object_id' => $_REQUEST['order_id'],
                'request' => json_encode($req_data)
            );
            fn_cp_megalog_ml_add_log($put_data);
        }
    }
}

if ($mode == 'details') {

    if (!empty($_REQUEST['order_id'])) {
        $params = array(
            'object_id' => $_REQUEST['order_id'],
            'controller' => 'orders',
        );
        list($logs, $search) = fn_cp_megalog_ml_get_logs($params, 0, DESCR_SL);
        Tygh::$app['view']->assign('cp_ml_logs', $logs);
        
        Registry::set('navigation.tabs.cp_ml_logs', array(
            'title' => __('cp_ml_mega_logs'),
            'js' => true
        ));
    }
}
if ($mode == 'cp_update_order_logs') {
    if (defined('AJAX_REQUEST')) {
        $params = array(
            'object_id' => $_REQUEST['order_id'],
            'controller' => 'orders',
        );
        list($logs, $search) = fn_cp_megalog_ml_get_logs($params, 0, DESCR_SL);
        
        Tygh::$app['view']->assign('logs', $logs);
        Tygh::$app['view']->display('addons/cp_megalog/components/order_logs.tpl');
        exit;
    }
} elseif ($mode == 'cp_ml_update_status') {
    Tygh::$app['view']->assign('params', $_REQUEST);
}
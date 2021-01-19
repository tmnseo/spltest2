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
use Tygh\Addons\CpStatusesRules\ServiceProvider; 

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode == 'cp_oc_cancel_order') {
        if (!empty($_REQUEST['order_id'])) {
            $params = array(
                'order_id' => $_REQUEST['order_id']
            );

            /*gMelnikov*/
            if (Registry::get('addons.cp_statuses_rules.status') == 'A') {
            
                $params['status'] = ServiceProvider::statusCancel();
            }
            /*gMelnikov*/

            fn_cp_oc_cancel_order_by_customer($params, $auth);
            return array(CONTROLLER_STATUS_OK, 'orders.search');
        }
    }
}

if ($mode == 'search') {
    Registry::set('cp_oc_get_canceling', true);
    $cancel_statuses = fn_cp_oc_get_orders_statuses_for_cancel();
    Registry::set('cp_oc_get_canceling', false);
    
    $is_cancel_status = fn_cp_oc_get_order_status_to_cancel();
    if (!empty($is_cancel_status)) {
        Tygh::$app['view']->assign('is_cancel_status', $is_cancel_status);
        Tygh::$app['view']->assign('cp_allowed_cancel', $cancel_statuses);
    }
    $order_statuses = fn_get_statuses(STATUSES_ORDER, array(), false, false, DESCR_SL);
    
    Tygh::$app['view']->assign('cp_order_statuses', $order_statuses);

} elseif ($mode == 'cp_oc_get_details') {
    if (!empty($_REQUEST['order_id'])) {
    
        Registry::set('cp_oc_is_get_details', true);
        $order_info = fn_get_order_info($_REQUEST['order_id']);

        if(!empty($order_info['products'])){
            foreach ($order_info['products'] as $key => $product) {

                if(!empty($product['extra']['warehouse_id'])){
                    $url =  fn_url("products.view?product_id=".$product['product_id'].'&warehouse_id='.$product['extra']['warehouse_id']);
                }
                else{
                    $url =  fn_url("products.view?product_id=".$product['product_id']);
                }
                $order_info['products'][$key]['manuf_code'] = fn_cp_np_getproduct_manuf_art($product['product_id']);

                $order_info['products'][$key]['cp_url']  = $url;
            }
        }

        Registry::set('cp_oc_is_get_details', false);
        
        Tygh::$app['view']->assign('back_order_id', $_REQUEST['order_id']);
        Tygh::$app['view']->assign('cust_order_info', $order_info);
        
        Registry::get('view')->display('addons/cp_orders_list_details/components/order_products.tpl');
    }
    exit;
}
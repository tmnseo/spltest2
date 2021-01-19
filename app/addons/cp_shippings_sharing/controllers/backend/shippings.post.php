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

    if ($mode == 'update_vendor_shippings') {

        $params = $_REQUEST;
        /* 
            $params['storefront_id'] - shipping_id
            $params['status'] - shipping_status
        */
        if (!empty($params['storefront_id']) && !empty($params['status'])) {

            $company_id = Registry::ifGet('runtime.company_id', null);

            if (empty($company_id)) {
                return [CONTROLLER_STATUS_OK, urldecode("shippings.manage")];
            }

            $result = fn_cp_change_shipping_used_status($params, $company_id);
                        
        }
        if (defined('AJAX_REQUEST')) {
            Tygh::$app['ajax']->assign('result', $result);

            return [CONTROLLER_STATUS_OK, urldecode("shippings.manage")];
        }
    }
    return ;
}
if ($mode == 'manage') {

    if (isset($auth['user_type']) && $auth['user_type'] === 'V') {

        $company_id = Registry::ifGet('runtime.company_id', null);

        if (empty($company_id)) {
            return ;
        }

        $shippings = Tygh::$app['view']->getTemplateVars('shippings');
        
        if (!empty($shippings)) {

            $used_shippings = fn_cp_get_used_shippings($company_id);
            if (!empty($used_shippings)) {
                foreach ($shippings as $shipping_id => &$shipping_data) {
                    if (in_array($shipping_id, $used_shippings)) {
                        $shipping_data['cp_use_this_shipping'] = 'Y';    
                    }    
                }
            }

            Tygh::$app['view']->assign('shippings', $shippings);
        }
    }
}elseif ($mode == 'update') {
    if (isset($auth['user_type']) && $auth['user_type'] === 'V') {

        $tabs = Registry::get('navigation.tabs'); 

        /*this tab contains
        information about setting up the 
        payment method, so it will be 
        hidden from the vendor*/

        if (!empty($tabs['configure'])) { 
            unset($tabs['configure']);
            Registry::set('navigation.tabs', $tabs);
        }
    }
}
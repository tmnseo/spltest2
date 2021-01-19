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
if ($mode == 'checkout') {
    $product_groups = Tygh::$app['view']->getTemplateVars('product_groups');
    $cart = Tygh::$app['view']->getTemplateVars('cart');
    $carts = Tygh::$app['view']->getTemplateVars('carts');

    if (isset($product_groups)) {

        foreach ($product_groups as $group_key => &$group) {

            $company_id = $group['company_id'];

            if (empty($company_id)) {
                return ;
            }
            if (!empty($group['shippings'])) {

                foreach ($group['shippings'] as $shipping_id => $shipping_data) {
                    
                    $used_shippings = fn_cp_get_used_shippings($company_id);

                    if (!empty($used_shippings) && !in_array($shipping_id, $used_shippings)) {
                        unset($group['shippings'][$shipping_id]);
                    }
                }

                if (!in_array($cart['chosen_shipping'][$group_key], array_keys($group['shippings'])) && !empty($group['shippings'])) {

                    fn_checkout_update_shipping($cart, array_keys($group['shippings']));

                    $location_hash = fn_checkout_get_location_hash($cart['user_data'] ?: []);
                    $is_location_changed = isset($cart['location_hash']) && $cart['location_hash'] !== $location_hash;
                    $shipping_calculation_type = fn_checkout_get_shippping_calculation_type($cart, $is_location_changed);

                    fn_calculate_cart_content($cart, $auth, $shipping_calculation_type, true, 'F');

                    if (!empty($carts)) {
                        foreach ($carts as $vendor_key => &$vendor_cart) {
                            fn_checkout_update_shipping($vendor_cart, array_keys($group['shippings']));
                            fn_calculate_cart_content($vendor_cart, $auth, $shipping_calculation_type, true, 'F');
                        }
                    }
                }
            }
        }
        
        Tygh::$app['view']->assign('carts', $carts);
        Tygh::$app['view']->assign('product_groups', $product_groups);
        Tygh::$app['view']->assign('cart', $cart);
    }
  
}
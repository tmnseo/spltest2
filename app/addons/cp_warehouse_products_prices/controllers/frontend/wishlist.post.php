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
   return ;
}
if ($mode == 'view') {
    $products = Tygh::$app['view']->getTemplateVars("products");
    if (!empty($products)) {
        foreach ($products as $wishlist_key => &$product_data) {
            if (!empty($product_data['product_id']) && !empty($product_data['extra']['warehouse_id'])) {
                $product_data['price'] = $product_data['base_price'] = fn_cp_warehouse_products_prices_get_product_price($product_data['product_id'], $product_data['extra']['warehouse_id']);
            }
        }
        Tygh::$app['view']->assign("products", $products);
    }
}
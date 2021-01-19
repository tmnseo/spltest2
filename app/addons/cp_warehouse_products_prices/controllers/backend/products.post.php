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
   if ($mode == 'update') {
      $product_id = isset($_REQUEST['product_id']) ? $_REQUEST['product_id'] : null;
      if (isset($_REQUEST['product_data']['cp_warehouse_products_prices'])) {
         foreach ($_REQUEST['product_data']['cp_warehouse_products_prices'] as $warehouse_id => $price) {
            fn_cp_update_warehouse_product_price($product_id, $warehouse_id, $price);
         }
      }
   }
}
if ($mode == 'update') {
   $product_id = isset($_REQUEST['product_id']) ? $_REQUEST['product_id'] : null;
   $warehouse_prices = fn_cp_get_warehouse_products_prices($product_id);

   Tygh::$app['view']->assign('warehouse_prices',$warehouse_prices);
}
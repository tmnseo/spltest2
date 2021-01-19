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
   $product_id = isset($_REQUEST['product_id']) ? $_REQUEST['product_id'] : null;
   $warehouse_prices = fn_cp_get_warehouse_products_prices($product_id);
   
   Tygh::$app['view']->assign('warehouse_prices',$warehouse_prices);
   if (isset($_REQUEST['warehouse_id']) && !empty($_REQUEST['warehouse_id'])) {
      $warehouses_amount = db_get_field("SELECT amount FROM ?:warehouses_products_amount WHERE product_id = ?i AND warehouse_id = ?i",$product_id, $_REQUEST['warehouse_id']);
      $cp_store_data = fn_get_store_location($_REQUEST['warehouse_id']);
      
      Tygh::$app['view']->assign('cp_store_data', $cp_store_data);
      Tygh::$app['view']->assign('cp_warehouses_amount',$warehouses_amount);
      Tygh::$app['view']->assign('cp_warehouse_id', $_REQUEST['warehouse_id']);
   } else {
      $items = fn_cp_warehouses_blocks_get_availability_in_stores();
      Tygh::$app['view']->assign('items', $items);
   }
}
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
   
}
elseif ($mode == 'search') {
   
   $original_products = Registry::get('view')->getTemplateVars('original_products');
   $original_search = Registry::get('view')->getTemplateVars('original_search');

   $analog_products = Registry::get('view')->getTemplateVars('analog_products');
   $analog_search = Registry::get('view')->getTemplateVars('analog_search');
   $count = 0;

   if (isset($original_products) && !empty($original_products)) {

      fn_cp_filter_warehouses_by_status($original_products);

      $count += fn_cp_get_product_count_with_warehouses($original_products);
      
      if ($count == 0) {
         $original_search['total_items'] = $count;
         Tygh::$app['view']->assign('original_search',$original_search);
      }
      Tygh::$app['view']->assign('original_products',$original_products);

   }
   if (isset($analog_products) && !empty($analog_products)) {

      fn_cp_filter_warehouses_by_status($analog_products);

      $count += fn_cp_get_product_count_with_warehouses($analog_products);

      if ($count == 0) {
         $analog_search['total_items'] = $count;
         Tygh::$app['view']->assign('analog_search',$analog_search);
      }

      Tygh::$app['view']->assign('analog_products',$analog_products);

   }

   Tygh::$app['view']->assign('total_search_product_count',$count);
}
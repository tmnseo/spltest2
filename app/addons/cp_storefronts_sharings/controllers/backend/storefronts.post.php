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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   if ($mode == 'update') {
      fn_cp_update_primary_currency($_REQUEST);
   }
}
if ($mode == 'update') {

   if (!isset($_REQUEST['storefront_id'])) {
      return ;
   }

   $primary_currency_id = fn_cp_get_primary_currency($_REQUEST['storefront_id']);
   
   if (isset($primary_currency_id) && !empty($primary_currency_id)) {
      $currencies = Tygh::$app['view']->getTemplateVars('all_currencies');
      if (isset($currencies) && !empty($currencies)) {
         foreach ($currencies as &$currency_data) {
            if ($currency_data['currency_id'] == $primary_currency_id) {
               $currency_data['cp_is_primary'] = 'Y';
            } else {
               $currency_data['cp_is_primary'] = 'N';
            }
         }
      }
      Tygh::$app['view']->assign('all_currencies',$currencies);
   }
}
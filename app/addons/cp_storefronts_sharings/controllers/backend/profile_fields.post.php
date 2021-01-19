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
      fn_cp_update_storefront_fields($_REQUEST['field_data']);
   }
}
if ($mode == 'update') {
   if (isset($_REQUEST['field_id']) && !empty($_REQUEST['field_id'])) {
      $selected_storefront_ids = fn_cp_get_selected_storefronts($_REQUEST['field_id'], 'field_id', '?:cp_storefronts_profile_fields');
   }
   if (!empty($selected_storefront_ids)) {
      Tygh::$app['view']->assign('cp_storefront_ids',$selected_storefront_ids);
   }
}

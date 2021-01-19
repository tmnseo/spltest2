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

if (!defined('BOOTSTRAP')) { die('Access denied'); }
   
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   if ($mode == 'update' || $mode == 'add') {
      /*this functionality is added because storefront piker(this one)  
      has a constant name and returns the name of the storefront*/
      if (!empty($_REQUEST['storefront_name'])) {
         $storefront_id = db_get_field("SELECT storefront_id FROM ?:storefronts WHERE name = ?s", $_REQUEST['storefront_name']);
         if (!empty($storefront_id)) {
            $_REQUEST['user_data']['storefront_id'] = $storefront_id;
         }
      }
   }
}

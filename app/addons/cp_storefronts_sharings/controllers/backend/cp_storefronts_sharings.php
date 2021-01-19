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
//CRON 
if ($mode == 'remove_old_tokens') {

   $cron_password = Registry::get('addons.cp_storefronts_sharings.cron_password');
  
   if (!empty($cron_password) && (!isset($_REQUEST['cron_password']) || $cron_password != $_REQUEST['cron_password'])) {
       die(__('access_denied'));
   }

   $deadlain_time = time() - CP_TOKEN_ALIVE_TIME ;
   db_query("DELETE FROM ?:cp_storefront_redirect_tokens WHERE start_time < ?i", $deadlain_time);
}
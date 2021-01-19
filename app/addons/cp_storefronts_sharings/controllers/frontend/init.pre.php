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

if (isset($_REQUEST['user_storefront_token']) && !empty($_REQUEST['user_storefront_token'])) {
   $user_token = $_REQUEST['user_storefront_token'];
   $cp_user_data = db_get_row("SELECT user_id, start_time FROM ?:cp_storefront_redirect_tokens WHERE user_token = ?s",$user_token);
   $time_check = false;

   if (!empty($cp_user_data['start_time']))
   {  
      if ($cp_user_data['start_time'] + CP_TOKEN_ALIVE_TIME > time()) {
         $time_check = true;
      }

   }
   if (!empty($cp_user_data['user_id']) && $time_check == true) {
      fn_login_user($cp_user_data['user_id'], true);
      db_query("DELETE FROM ?:cp_storefront_redirect_tokens WHERE user_token = ?s",$user_token);
   }
}
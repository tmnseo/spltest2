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
use Tygh\Enum\YesNo;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

/*HOOKS*/
function fn_cp_storefronts_sharings_storefront_repository_find(
   $params,
   $items_per_page,
   $fields,
   &$join,
   &$conditions,
   $group_by,
   $having,
   $order_by,
   $limit)
{
   $auth = Tygh::$app['session']['auth'];
   if (!empty($auth['user_type']) && $auth['user_type'] == 'V' && Registry::get('runtime.mode') == 'manage' && Registry::get('runtime.controller') == 'taxes') {
      $join[] .= db_quote(' LEFT JOIN ?:storefronts_companies ON storefronts.storefront_id = ?:storefronts_companies.storefront_id');
      $conditions[] .= db_quote(' AND ?:storefronts_companies.company_id = ?i OR ?:storefronts_companies.company_id is NULL', $auth['company_id']);
   }

}
function fn_cp_storefronts_sharings_init_currency_post($params, $area, &$primary_currency, &$secondary_currency)
{
   if ($area == 'C') {
      
      $storefront_id = fn_cp_get_storefront_id();
      $primary_currency_id = fn_cp_get_primary_currency($storefront_id);

      if (isset($primary_currency_id) && !empty($primary_currency_id)) {
         $_params['currency_id'] = $primary_currency_id;
         $currency = fn_get_currencies_list($_params, $area, CART_LANGUAGE);
         foreach ($currency as $currency_code => $currency_data) {
            if ($currency_code != $primary_currency) {
               $primary_currency = $currency_code;
               $secondary_currency = $currency_code;
               //fn_set_session_data('secondary_currency' . $area, $secondary_currency, COOKIE_ALIVE_TIME);
            }
         }   
      }
   }
}
function fn_cp_storefronts_sharings_get_products_post(&$products, $params, $lang_code)
{
   if (AREA == 'C') {
      foreach ($products as $product_id => &$product_data) {
         fn_cp_update_product_taxes($product_data);
      }
   }
}
function fn_cp_storefronts_sharings_get_product_data_post(&$product_data, $auth, $preview, $lang_code)
{
   if (AREA == 'C') {
      fn_cp_update_product_taxes($product_data);
   }
}
function fn_cp_storefronts_sharings_get_cart_product_data_post($hash, $product, $skip_promotion, $cart, $auth, $promotion_amount, &$_pdata, $lang_code)
{
   if (AREA == 'C') {
      fn_cp_update_product_taxes($_pdata);
   }
}
function fn_cp_storefronts_sharings_get_shipping_taxes_post($shipping_id, $shipping_rates, $cart, &$taxes)
{
   if (AREA == 'C') {
      fn_cp_change_vendor_taxes($cart, $taxes);
   }
}
function fn_cp_storefronts_sharings_get_payment_taxes_post($payment_id, $cart, &$taxes)
{
   if (AREA == 'C') {
      fn_cp_change_vendor_taxes($cart, $taxes);
   }
}
function fn_cp_storefronts_sharings_update_tax_post($tax_data, $tax_id, $lang_code) {
   
   if (isset($tax_data['cp_storefront_ids']) && !empty($tax_data['cp_storefront_ids'])) {
      $selected_storefront_ids = explode(',', $tax_data['cp_storefront_ids']);
   } else {
      db_query('DELETE FROM ?:cp_storefronts_taxes WHERE tax_id = ?i',$tax_id);
   }

   if (!empty($selected_storefront_ids) && !empty($tax_id)) {
      fn_cp_update_storefronts_taxes($selected_storefront_ids, $tax_id);
   }
}
function fn_cp_storefronts_sharings_get_profile_fields_post($location, $_auth, $lang_code, $params, &$profile_fields, $sections)
{
   if (AREA == 'C') {
      $storefront_id = fn_cp_get_storefront_id();
      $availibility_fields_ids = db_get_fields("SELECT field_id FROM ?:cp_storefronts_profile_fields WHERE storefront_id = ?i ", $storefront_id);
      
      foreach ($profile_fields as $field_type => &$fields_data) {
         fn_cp_check_availibility_on_storefront($fields_data, $availibility_fields_ids);
      }
   }
}
function fn_cp_storefronts_sharings_update_user_pre($user_id, &$user_data, $auth, $ship_to_another, $notify_user)
{
   $storefront_id = !empty($user_data['storefront_id']) ? $user_data['storefront_id'] : fn_cp_get_storefront_id();
   if (!empty($storefront_id)) {
      if ((AREA == 'C' && !empty($user_data['user_type']) && $auth['user_type'] == 'C') || (AREA == 'A' && !empty($user_data['user_type']) && $user_data['user_type'] == 'C')) {
         $user_data['storefront_id'] = $storefront_id;
      }   
   }
}
function fn_cp_storefronts_sharings_cp_auth_routines_post($status, $user_data, $user_login, $password, $salt, $request){
   if (AREA == 'C' && !empty($user_data['user_type']) && $user_data['user_type'] == 'C' && !empty($user_data['storefront_id'])) {
      if ($status == true && !empty($user_data) && !empty($password) && fn_generate_salted_password($password, $salt) == $user_data['password']) {
         $redirect_url = empty($request['return_url']) ? '' : $request['return_url'];
         $storefront_id = fn_cp_get_storefront_id();
         if ($user_data['storefront_id'] != $storefront_id && !empty($user_data['user_id'])) {
            $user_token = md5(uniqid(""));
            $start_time = time();
            $data = array(
               'user_id' => $user_data['user_id'],
               'user_token' => $user_token,
               'start_time' => $start_time
            );
            db_query("INSERT INTO ?:cp_storefront_redirect_tokens ?e",$data);
            
            if (defined('AJAX_REQUEST')) {
               Tygh::$app['ajax']->assign('force_redirection', fn_url($redirect_url."?storefront_id=".$user_data['storefront_id']."&user_storefront_token=".$user_token));
               exit;
            }
            fn_redirect(fn_url($redirect_url."?storefront_id=".$user_data['storefront_id']."&user_storefront_token=".$user_token), true);   
         }
      }
   }
}
/*HOOKS*/
function fn_cp_get_storefront_id()
{
   $storefront = Tygh::$app['storefront'];
   $storefront_id = isset($storefront->storefront_id) ? $storefront->storefront_id : NULL;

   return $storefront_id;
}
/*Currencies*/
function fn_cp_get_primary_currency($storefront_id)
{
   $primary_currency_id = db_get_field("SELECT currency_id FROM ?:storefronts_currencies WHERE storefront_id = ?i AND cp_is_primary = ?s",$storefront_id, 'Y');

   return $primary_currency_id;
}
function fn_cp_update_primary_currency($params)
{
   $primary_currency_id = isset($params['cp_is_primary_currency']) ? $params['cp_is_primary_currency'] : NULL;
   $check = false;
   if ($primary_currency_id != NULL) {
      foreach ($params['storefront_data']['currency_ids'] as $currency_id) {
         if ($currency_id == $primary_currency_id) {
            $check = true;
         }
      }
   }
   if ($primary_currency_id != NULL && isset($params['storefront_data']['storefront_id']) && !empty($params['storefront_data']['storefront_id']) && $check == true) {
      db_query("UPDATE ?:storefronts_currencies SET cp_is_primary = ?s WHERE storefront_id = ?i AND currency_id = ?i",'Y',$params['storefront_data']['storefront_id'], $primary_currency_id);
   }
}
/*Taxes*/
function fn_cp_update_storefronts_taxes($selected_storefront_ids, $tax_id)
{
   foreach ($selected_storefront_ids as $storefront_id) {
      $data = array(
         'storefront_id' => $storefront_id,
         'tax_id' => $tax_id);
      db_replace_into('cp_storefronts_taxes',$data);
   }
   db_query('DELETE FROM ?:cp_storefronts_taxes WHERE tax_id = ?i AND storefront_id NOT IN (?n)',$tax_id, $selected_storefront_ids);   
}
function fn_cp_get_selected_storefronts($key_value, $key_name, $table_name)
{
   $storefronts = db_get_fields("SELECT storefront_id FROM $table_name WHERE $key_name = ?i", $key_value);
   return $storefronts;
}
function fn_cp_filter_taxes($storefront_id, $vendor_id, &$taxes, $tax_id)
{
   $taxes[$tax_id]['cp_is_primary_tax'] = db_get_field("SELECT is_primary FROM ?:cp_vendor_taxes WHERE storefront_id = ?i AND vendor_id = ?i AND tax_id = ?i",$storefront_id, $vendor_id, $tax_id); 

   $availibility_taxes = db_get_fields("SELECT tax_id FROM ?:cp_storefronts_taxes WHERE storefront_id = ?i", $storefront_id);
   if (!in_array($tax_id, $availibility_taxes)) {
      unset($taxes[$tax_id]);
   }
   

}
function fn_cp_update_vendor_primary_tax($storefront_id, $vendor_id, $tax_id)
{
   $data = array(
      'storefront_id' => $storefront_id,
      'vendor_id' => $vendor_id,
      'tax_id' => $tax_id,
      'is_primary' => 'Y');
   db_replace_into('cp_vendor_taxes', $data);
   db_query('UPDATE ?:cp_vendor_taxes SET is_primary = ?s WHERE storefront_id = ?i AND vendor_id = ?i AND tax_id != ?i','N' ,$storefront_id, $vendor_id, $tax_id); 
}
function fn_cp_update_product_taxes(&$data)
{
   $storefront_id = fn_cp_get_storefront_id();
   $vendor_id = isset($data['company_id']) ? $data['company_id'] : NULL;
   $storefront_vendor_tax_id = fn_cp_get_storefront_vendor_tax_id($storefront_id, $vendor_id);

   if (!empty($storefront_vendor_tax_id)) {
      unset($data['tax_ids']);
      $data['tax_ids'] = $storefront_vendor_tax_id; 
   }else {
      $_storefront_taxes = db_get_fields("SELECT tax_id FROM ?:cp_storefronts_taxes WHERE storefront_id = ?i",$storefront_id);
      if (isset($_storefront_taxes) && count($_storefront_taxes) == 1) {
         unset($data['tax_ids']);
         $data['tax_ids'] = $_storefront_taxes[0];
      }
   }
}
function fn_cp_get_storefront_vendor_tax_id($storefront_id, $vendor_id)
{
   $_id = db_get_field("SELECT tax_id FROM ?:cp_vendor_taxes WHERE storefront_id = ?i AND vendor_id = ?i AND is_primary = ?s",$storefront_id, $vendor_id, 'Y');
   return $_id;
}
/*Profile Fields*/
function fn_cp_update_storefront_fields($data)
{
   $field_id = $data['field_id'];
   if (isset($data['cp_storefront_ids']) && !empty($data['cp_storefront_ids'])) {
      $selected_storefront_ids = explode(',', $data['cp_storefront_ids']);
      if (!empty($selected_storefront_ids) && !empty($field_id)) {
         foreach ($selected_storefront_ids as $storefront_id) {
            $data = array(
            'storefront_id' => $storefront_id,
            'field_id' => $field_id);
            db_replace_into('cp_storefronts_profile_fields', $data);
         }
         db_query('DELETE FROM ?:cp_storefronts_profile_fields WHERE field_id = ?i AND storefront_id NOT IN (?n)',$field_id, $selected_storefront_ids);
      }      
   } else {
      db_query('DELETE FROM ?:cp_storefronts_profile_fields WHERE field_id = ?i',$field_id);
   }
}
function fn_cp_check_availibility_on_storefront(&$data, $availibility_fields_ids)
{
   foreach ($data as $field_id => &$field_data) {
      $check_storefront_sharing = db_get_field("SELECT COUNT(*) FROM ?:cp_storefronts_profile_fields WHERE field_id = ?i",$field_id);
      if (!in_array($field_id, $availibility_fields_ids) && $check_storefront_sharing != 0) {
         unset($data[$field_id]);
      }
   }
}
function fn_cp_change_vendor_taxes($cart, &$taxes)
{
   $storefront_id = fn_cp_get_storefront_id();
   foreach ($cart['products'] as $cart_id => $product) {
      $vendor_id = !empty($product['company_id']) ? $product['company_id'] : NULL;
      $taxes = array();
      $storefront_vendor_tax_id = fn_cp_get_storefront_vendor_tax_id($storefront_id, $vendor_id);
      if (!empty($storefront_vendor_tax_id)) {
         $taxes = db_get_hash_array("SELECT tax_id, address_type, priority, price_includes_tax, regnumber FROM ?:taxes WHERE tax_id = ?i AND status = 'A' ORDER BY priority", 'tax_id', $storefront_vendor_tax_id);
      } else {
         $_storefront_taxes = db_get_fields("SELECT tax_id FROM ?:cp_storefronts_taxes WHERE storefront_id = ?i",$storefront_id);
         if (isset($_storefront_taxes) && count($_storefront_taxes) == 1) {
            $taxes = db_get_hash_array("SELECT tax_id, address_type, priority, price_includes_tax, regnumber FROM ?:taxes WHERE tax_id = ?i AND status = 'A' ORDER BY priority", 'tax_id', $_storefront_taxes[0]);
         }
      }
   }
}
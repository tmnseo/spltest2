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
use Tygh\Http;
use Tygh\Addons\CpWorkingCalendar\Analizator\DayAnalizator;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

/*HOOKS*/
function fn_cp_admin_checkout_modifications_get_order_info(&$order, $additional_data)
{
   if (AREA != 'C') {
      foreach ($order['products'] as &$product) {
         fn_cp_get_manufacturer_data($product, $product['product_id']);
      }
   }
   /*Replaces id of custom fields with names*/
   fn_cp_profile_field_normalization($order);

   /*Adds information to display on the map*/
   fn_cp_get_shipping_address($order);
}
function fn_cp_admin_checkout_modifications_change_order_status_post($order_id, $status_to, $status_from, $force_notification, $place_order, $order_info, $edp_data) 
{
   if ($status_to == 'P' || $status_to == 'U' || $status_to == 'L') {
      if (isset($order_info['shipping'])) {
         foreach ($order_info['shipping'] as $shipping) {
            if (isset($shipping['service_code']) && $shipping['service_code'] == 'pickup') {
               $number_working_days = 1;
            } elseif (isset($shipping['service_delivery_time'])) {
               $numbers = str_split(preg_replace('/[^0-9]/', '', $shipping['service_delivery_time']));
               $number_working_days = max($numbers) + 1; // one day for all shippings
            }   
         }
      }
      
      $data['cp_planned_time_issuing_order'] = fn_cp_admin_checkout_modifications_get_planned_date($number_working_days, $order_info['company_id']);

      /* NOTIFICATION FOR VENDOR */
      if (Registry::get('addons.cp_additional_email_templates.status') == 'A') {
         
         if (!empty($order_info['company_id'])) {

            $event_dispatcher = Tygh::$app['event.dispatcher'];
            $formatter = Tygh::$app['formatter'];

            $request_data['email'] = db_get_field("SELECT email FROM ?:companies WHERE company_id = ?i", $order_info['company_id']);
            $request_data['issuing_time'] = $formatter->asDatetime($data['cp_planned_time_issuing_order'], "%d/%m/%y");
            $request_data['order_id'] = $order_info['order_id'];
            
            $event_dispatcher->dispatch('cp_additional_email_templates.new_planned_time_issuing_order', [
               'request_data' => $request_data
            ]);
         }
         
      }
      /* NOTIFICATION FOR VENDOR */
      db_query('UPDATE ?:orders SET ?u WHERE order_id = ?i', $data, $order_id);
   }
   if ($status_to == 'U') {
      list($shipments,) = fn_get_shipments_info(array('order_id' => $order_info['order_id'], 'advanced_info' => true));
      if (!empty($shipments)) {
         foreach ($shipments as $shipment) {
            if (!empty($shipment['tracking_number']) && !empty($shipment['shipment_id'])) {
               db_query('UPDATE ?:shipments SET tracking_number = "" WHERE shipment_id = ?i', $shipment['shipment_id']);
            }
         }
      }
   }
}
function fn_cp_admin_checkout_modifications_change_order_status(&$status_to, $status_from, $order_info, $force_notification, $order_statuses, $place_order)
{
   if ($status_to == 'G') {
      
      foreach ($order_info['shipping'] as $shipping) {
         if ($shipping['service_code'] == 'pickup') {    
            return;
         }
      }
      list($shipments,) = fn_get_shipments_info(array('order_id' => $order_info['order_id'], 'advanced_info' => true));
      
      $check_track = false;
      if (isset($shipments) && !empty($shipments)) {
         foreach ($shipments as $shipment) {
            if (isset($shipment['tracking_number']) && !empty($shipment['tracking_number'])) {
               $check_track = true;
            }
         }
      }

      if ($check_track == false) {
         $status_to = $status_from;
         fn_set_notification('W', __('warning'), __('cp_warning_track_number'));
      }
   }elseif ($status_to == 'J') {
      if (empty($order_info['cp_vendor_order_number'])) {
         $status_to = $status_from;
         fn_set_notification('W', __('warning'), __('cp_warning_vendor_order_number'));   
      }
   }
}
function fn_cp_admin_checkout_modifications_gather_additional_products_data_post($product_ids, $params, $products, $auth, $lang_code)
{
   foreach ($products as $key => &$product) {
      fn_cp_get_manufacturer_data($product, $product['product_id']);
   }

}
/*HOOKS*/
function fn_cp_admin_checkout_modifications_cp_change_custom_fields($params, $order_id)
{
   /*if (!empty($params['supplier_order_id'])) {
      db_query("UPDATE ?:orders SET cp_vendor_order_number = ?s WHERE order_id = $order_id",$params['supplier_order_id'],$order_id);
   }
   if (!empty($params['charge_id'])) {
      db_query("UPDATE ?:orders SET cp_payment_order_number = ?s WHERE order_id = $order_id",$params['charge_id'],$order_id);
   }
   if (!empty($params['charge_date'])) {
      db_query("UPDATE ?:orders SET cp_payment_order_date = ?s WHERE order_id = $order_id",$params['charge_date'],$order_id);
   }
   if (!empty($params['payment_amount'])) {
      db_query("UPDATE ?:orders SET cp_payment_amount = ?s WHERE order_id = $order_id",$params['payment_amount'],$order_id);
   }*/
   if (!empty($params)) {
      $data = array();
      foreach ($params as $key => $value) {
         if ($key == 'supplier_order_id') {
            $data['cp_vendor_order_number'] = $value;
         }elseif ($key == 'charge_id') {
            $data['cp_payment_order_number'] = $value;
         }elseif ($key == 'charge_date') {
            $data['cp_payment_order_date'] = $value;
         }elseif ($key == 'payment_amount') {
            $data['cp_payment_amount'] = $value;
         }
      }
      if (!empty($data)) {
         db_query("UPDATE ?:orders SET ?u WHERE order_id = ?i",$data, $order_id);
      }
   }
}
/*HOOKS*/
function fn_cp_get_manufacturer_data(&$product, $product_id)
{
   $manufacturer_code_id = Registry::get('addons.cp_change_inv.manufacturer_code_id');
   $manufacturer_id = Registry::get('addons.cp_change_inv.manufacturer_id');

   $product['manufacturer_code'] = db_get_field("SELECT vd.variant FROM ?:product_features_values as v LEFT JOIN ?:product_feature_variant_descriptions as vd ON v.variant_id = vd.variant_id WHERE v.product_id = ?i AND v.feature_id = ?i AND vd.lang_code = ?s", $product_id, $manufacturer_code_id, CART_LANGUAGE);
   $product['manufacturer'] = db_get_field("SELECT vd.variant FROM ?:product_features_values as v LEFT JOIN ?:product_feature_variant_descriptions as vd ON v.variant_id = vd.variant_id WHERE v.product_id = ?i AND v.feature_id = ?i AND vd.lang_code = ?s", $product_id, $manufacturer_id, CART_LANGUAGE);
}
/**
 * Returns date + N working days
 *
 * @param int $number_working_days The number of working days to be added to the date
 * @param int $date Date to add
 * @return int timestamp date + N working days
 */
function fn_cp_admin_checkout_modifications_get_planned_date($number_working_days, $company_id, $date = 0)
{
   if (empty($date)) {
      $date = strtotime('now');
   }


   $added_days = 0;
   $i = 0;

   /* We need it?*/

   /*$first_day_analizator = new DayAnalizator($company_id, $date);
   $first_working_day = $first_day_analizator->isWorkDay();
   if ($first_working_day != 1) {
      $number_working_days ++;
   }*/
   
   while ($added_days <= $number_working_days) {
      //$checked_day = date('Ymd',strtotime("+ $i days"));
      //$is_working_day = fn_cp_get_working_day($checked_day);

      $i++;

      $day_analizator = new DayAnalizator($company_id, $date + ($i * SECONDS_IN_DAY));
      $is_working_day = $day_analizator->isWorkDay(false);

      if ($is_working_day == 1) {
         $added_days++;

      }
   }

   $date = strtotime("+$i day", $date);

   return $date;
}
function fn_cp_get_working_day($day)
{
   $url = "https://isdayoff.ru/$day";

   $response = Http::post($url, $day, array('timeout' => 5));

   return $response;
}
function fn_cp_get_shipping_address(&$order)
{
   foreach ($order['shipping'] as $shipping_data) {
      if (isset($shipping_data['store_data'])) {
         $order['cp_address_data']['s_country_descr'] = isset($shipping_data['store_data']['country']) ? $shipping_data['store_data']['country'] : 'Russia';
         $order['cp_address_data']['s_city'] = isset($shipping_data['store_data']['city']) ? $shipping_data['store_data']['city'] : '';
         $order['cp_address_data']['s_address'] = isset($shipping_data['store_data']['pickup_address']) ? $shipping_data['store_data']['pickup_address'] : '';
      } elseif (isset($shipping_data['office_data'])) {
         $order['cp_address_data']['s_country_descr'] = isset($order['s_country']) ? $order['s_country'] : 'Russia';
         $order['cp_address_data']['s_city'] = isset($order['s_city']) ? $order['s_city'] : '';
         $order['cp_address_data']['s_address'] = isset($shipping_data['office_data']['address']) ? $shipping_data['office_data']['address'] : '';
      } elseif (isset($shipping_data['cp_is_door_delivery']) && $shipping_data['cp_is_door_delivery'] == 'Y') {
         $order['cp_address_data']['s_country_descr'] = isset($order['s_country']) ? $order['s_country'] : 'Russia';
         $order['cp_address_data']['s_city'] = isset($order['s_city']) ? $order['s_city'] : '';
         $order['cp_address_data']['s_address'] = isset($order['s_address']) ? $order['s_address'] : '';
      }
   }

}
function fn_cp_profile_field_normalization(&$order) 
{
   //$user_info = fn_get_user_info($order['user_id'], true);
   $profile_fields = fn_get_profile_fields();
   if (isset($order['fields'])) {
      foreach ($order['fields'] as $field_id => $field) {
         fn_cp_search_all_sections($field_id, $profile_fields, $field, $order);
      }
   }
   
}
function fn_cp_search_all_sections($field_id, $profile_fields, $field, &$order)
{
   foreach ($profile_fields as $section => $section_data) {
      foreach ($section_data as $section_data_field_id => $field_data) {
         if ($field_id == $section_data_field_id) {
            $order['cp_address_data'][$section][$field_data['field_name']] = $field;
         }
      }
   }
}
function fn_cp_place_order_manually(&$cart, $params, $customer_auth, $action, $issuer_id, $force_notification)
{
    // Clean up saved shipping rates
    unset(Tygh::$app['session']['shipping_rates']);

    // update totals and etc.
    fn_update_cart_by_data($cart, $params, $customer_auth);

    if (!empty($params['shipping_ids'])) {
        fn_checkout_update_shipping($cart, $params['shipping_ids']);
    }
  
    $cart['calculate_shipping'] = false;
    // recalculate cart content after update
    list($cart_products, $product_groups) = fn_calculate_cart_content($cart, $customer_auth, false);

    $cart['notes'] = !empty($params['customer_notes']) ? $params['customer_notes'] : '';
    $cart['payment_info'] = !empty($params['payment_info']) ? $params['payment_info'] : array();
    
    list($order_id, $process_payment) = fn_place_order($cart, $customer_auth, $action, $issuer_id);

    return [$order_id, $action];
}
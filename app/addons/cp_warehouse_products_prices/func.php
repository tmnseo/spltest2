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
function fn_cp_warehouse_products_prices_add_product_to_cart_get_price($product_data, &$cart, $auth, $update, $_id, $data, $product_id, $amount, &$price, $zero_price_action, $allow_add)
{

  if (AREA == 'C') {

    $_price = $price;
    if (!empty($product_data)) {
      
       foreach ($product_data as $key => $product) {

          $warehouse_id = isset($product['extra']['warehouse_id']) ? $product['extra']['warehouse_id'] : null;

          if ($warehouse_id != null && (!($update) || (!empty($_id) && $key == $_id))) {
              $price = fn_cp_warehouse_products_prices_get_product_price($product['product_id'], $warehouse_id);

              /*$price_data['price'] = $price;
              $price_data['base_price'] = $price;
              $price_data['product_id'] = $product['product_id'];
              if (AREA == 'C') {
                fn_promotion_apply('catalog', $price_data, $auth);
              }

              $price = $price_data['price'];*/
              
             if ($price == null) {
                $price = $_price;
             }    
          }
          
       }   
    }
    
    if (isset($cart['products'][$_id]['price'])) { 
      $cart['products'][$_id]['price'] = $price;
    }
  }
}
function fn_cp_warehouse_products_prices_get_cart_product_data($product_id, &$_pdata, $product, $auth, $cart, $hash)
{
  //if (AREA == 'C') {
    if (isset($product['price'])) {

      if (!empty($product['extra']['warehouse_id'])) {
        $_pdata['base_price'] = $_pdata['price'] = fn_cp_warehouse_products_prices_get_product_price($product_id, $product['extra']['warehouse_id']);
      }
    }
  //}
}
function fn_cp_warehouse_products_prices_get_cart_product_data_pre($hash, $product, &$skip_promotion, $cart, $auth, $promotion_amount, $lang_code)
{
  //$skip_promotion = true;
}
function fn_cp_warehouse_products_prices_get_products_post(&$products, $params, $lang_code)
{
   if (AREA == 'C') {
      foreach ($products as $product_id => &$product_data) {
         fn_cp_add_warehouses_extra_data($product_id, $product_data);

         fn_set_hook('add_warehouses_extra_data_post',$products, $product_id, $product_data, $params, $lang_code);
         if (!empty($params['block_data']['type']) && $params['block_data']['type'] == 'products') {
            $params_for_best_warehouse = [
               'product_id' => $product_data['product_id']
            ];

            list(, $best_wh_id) = fn_cp_np_get_mosts_products($params_for_best_warehouse, true, true);
            $product_data['best_warehouse_id'] = $best_wh_id;
            
         }
        
      }
      
   }
}
function fn_cp_warehouse_products_prices_update_company($company_data, $company_id, $lang_code, $action)
{
  if ($action == 'add') {
    $store_location_data = array(
      'store_type' => 'W',
      'name' => __("cp_default_warehouse" ,["[company_name]" => $company_data['company']]),
      'company_id' => $company_id,
      'country' => $company_data['country'],
      'state' => $company_data['state'],
      'city' => $company_data['city'],
      'status' => 'A');

    fn_update_store_location($store_location_data, $_REQUEST['store_location_id'], DESCR_SL);
  }
}
function fn_cp_warehouse_products_prices_get_product_filter_fields(&$filters) {
  $filters['G'] = array(
    'db_field' => 'price',
    'table' => 'warehouses_products_amount',
    'description' => 'cp_cf_warehouse_price',
    'condition_type' => 'D',
    'slider' => true,
    'prefix' => (Registry::get('currencies.' . CART_SECONDARY_CURRENCY . '.after') == 'Y' ? '' : Registry::get('currencies.' . CART_SECONDARY_CURRENCY . '.symbol')),
    'suffix' => (Registry::get('currencies.' . CART_SECONDARY_CURRENCY . '.after') != 'Y' ? '' : Registry::get('currencies.' . CART_SECONDARY_CURRENCY . '.symbol')),
    'conditions' => function($db_field, $join, $condition) {
      return array($db_field, $join, $condition);
    },
  );
}
function fn_cp_warehouse_products_prices_generate_filter_field_params(&$params, $filters, $selected_filters, $filter_fields, $filter, $structure)
{
  if (AREA == 'C' && isset($filter['field_type']) && $filter['field_type'] == 'G') {
      $params['is_cp_price_filter'] = 'Y';
  } 
}
function fn_cp_warehouse_products_prices_get_products (&$params, &$fields, $sortings, &$condition, &$join, $sorting, $group_by, $lang_code, $having) {
  
  if (AREA == 'C' && !empty($params['is_cp_price_filter']) && $params['is_cp_price_filter'] == 'Y') {

      if (Registry::get('runtime.controller') == 'companies' && Registry::get('runtime.mode') == 'products') {
         $params['only_for_counting'] = true; //  for isWhetherToNeedIncludeChildVariations (crash pagination)
      }
    

    $is_details_page = Registry::get('cp_np_is_product_details');
    
    if (empty($params['cp_np_type']) && empty($is_details_page)) {
      $join .= db_quote(" LEFT JOIN ?:warehouses_products_amount as cp_wh_am ON cp_wh_am.product_id = products.product_id");
    }

    if (isset($params['price_from']) && fn_is_numeric($params['price_from'])) {
        $condition = str_replace("AND prices.price >=", "AND cp_wh_am.price >=", $condition);
    }

    if (isset($params['price_to']) && fn_is_numeric($params['price_to'])) {
        $condition = str_replace("prices.price <=", "cp_wh_am.price <=", $condition);
    }
  }
   if (empty($fields['company_id'])) {
      $fields['company_id'] = "products.company_id";   
   }
}
function fn_cp_warehouse_products_prices_add_warehouses_extra_data_post(&$products, $product_id, &$product_data, $params, $lang_code)
{
  if (AREA == 'C' && !empty($params['is_cp_price_filter']) && $params['is_cp_price_filter'] == 'Y') {

    foreach ($product_data['extra_warehouse_data'] as $warehouse_id => $warehouses_data) {
      if (isset($params['price_from']) && fn_is_numeric($params['price_from']) && isset($params['price_to']) && fn_is_numeric($params['price_to'])) {
        if ($warehouses_data['price'] < $params['price_from'] || $warehouses_data['price'] > $params['price_to'])
        {
          unset($product_data['extra_warehouse_data'][$warehouse_id]);
        }
      }
    }
  }
}
function fn_cp_warehouse_products_prices_cp_before_display_wishlist_notifications(&$added_products, $wishlist, $auth)
{
  foreach ($added_products as $wishlist_key => $product_data) {
    if (!empty($product_data['extra']['warehouse_id']) && !empty($product_data['product_id'])) {
      $added_products[$wishlist_key]['display_price'] = fn_cp_warehouse_products_prices_get_product_price($product_data['product_id'], $product_data['extra']['warehouse_id']);
    }
  }
}
/*HOOKS*/
function fn_cp_update_warehouse_product_price($product_id, $warehouse_id, $price)
{
   $data = array( 'product_id' => $product_id,
                  'warehouse_id' => $warehouse_id,
                  'price' => abs($price));
   db_replace_into('warehouses_products_amount',$data);
}
function fn_cp_get_warehouse_products_prices($product_id)
{
   $cp_prices = array();
   $prices = db_get_array("SELECT warehouse_id, price FROM ?:warehouses_products_amount WHERE product_id = ?i", $product_id);

   foreach ($prices as $key => $price_data) {
      if ((!empty($price_data['price']) && $price_data['price'] != 0) || AREA != 'C') {

        $price_data['base_price'] = $price_data['price'];
        $price_data['product_id'] = $product_id;
        if (AREA == 'C') {
          fn_promotion_apply('catalog', $price_data, $auth);
        }

        $cp_prices[$price_data['warehouse_id']]['price'] = $price_data['price'];
        $cp_prices[$price_data['warehouse_id']]['base_price'] = $price_data['base_price'];
      } else {
        $cp_prices[$price_data['warehouse_id']] = fn_get_product_price($product_id, 1, Tygh::$app['session']['auth']);
      }
   }
   
   return $cp_prices;   
}
function fn_cp_warehouse_products_prices_get_product_price($product_id, $warehouse_id)
{
   $price = db_get_field("SELECT price FROM ?:warehouses_products_amount WHERE product_id = ?i AND warehouse_id = ?i", $product_id, $warehouse_id);

   return !empty($price) ? (float)$price : null;
}
function fn_cp_add_warehouses_extra_data($product_id, &$product_data)
{   
    if ((Registry::get('runtime.controller') == 'companies' && Registry::get('runtime.mode') == 'products') || (stripos($product_id, '-') !== false)) {
        list($product_id, $product_warehouse_id) = explode('-', $product_id);
    }
    if (Registry::get('addons.cp_matrix_destinations.status') == 'A' && !empty(Tygh::$app['session']['cp_user_has_defined_city'])) {
        $has_geo_city = true;
        $warehouses_data = db_get_array("SELECT ?:warehouses_products_amount.*, ?:cp_matrix_data.time_average FROM ?:warehouses_products_amount 
            LEFT JOIN ?:cp_matrix_data ON ?:cp_matrix_data.city_from_id = ?:warehouses_products_amount.city_id
            WHERE ?:warehouses_products_amount.product_id = ?i AND ?:cp_matrix_data.city_to_id = ?i", $product_id, Tygh::$app['session']['cp_user_has_defined_city']);
    } else {
        $has_geo_city = false;
        $warehouses_data = db_get_array("SELECT ?:warehouses_products_amount.* FROM ?:warehouses_products_amount WHERE product_id = ?i",$product_id);
    }
    
   if (isset($warehouses_data)) {
      foreach ($warehouses_data as $data) {
        
        if (!empty($data['price'])) {
          
          if (!empty($product_warehouse_id) && $data['warehouse_id'] != $product_warehouse_id) {
            continue;
          }

          $warehouse_city = db_get_field("SELECT city FROM ?:store_location_descriptions WHERE store_location_id = ?i AND lang_code = ?s",$data['warehouse_id'], CART_LANGUAGE);
          $product_data['extra_warehouse_data'][$data['warehouse_id']] = $data;

          $product_data['extra_warehouse_data'][$data['warehouse_id']]['warehouse_city'] = $warehouse_city;
          $product_data['extra_warehouse_data'][$data['warehouse_id']]['base_price'] = $data['price'];
          $product_data['extra_warehouse_data'][$data['warehouse_id']]['modifiers_price'] = 0;
          $product_data['extra_warehouse_data'][$data['warehouse_id']]['product_id'] = $product_id;

          if (!empty($has_geo_city)) {
            $product_data['extra_warehouse_data'][$data['warehouse_id']]['time_average'] = $data['time_average'];
          }
          /* Сделано для того чтобы в блоке смогла примениться промоакция продавца
            Для того чтобы понять это вы можете заглянуть в app/addons/cp_direct_payments/func.php
            Хук get_promotions
            Без данного участка кода не применяются промоакции для цен на складах тк 
            применение вызывается после того как будет определен runtime.cp_direct_payments.cart.vendor_id
          */
          $_cur_vend_id = Registry::get('runtime.cp_direct_payments.cart.vendor_id');
          
         Registry::set('runtime.cp_direct_payments.cart.vendor_id', $product_data['company_id']);
          
          
          fn_promotion_apply('catalog', $product_data['extra_warehouse_data'][$data['warehouse_id']], $auth);
          
          if (!empty($_cur_vend_id)) {
            Registry::set('runtime.cp_direct_payments.cart.vendor_id', $_cur_vend_id);
          }
        }   
      }
   }
   
}
function fn_cp_get_product_count_with_warehouses($products)
{
   $count = 0;
   foreach ($products as $product_id => $product_data) {
      if (isset($product_data['extra_warehouse_data'])) {
         foreach ($product_data['extra_warehouse_data'] as $warehouse_id => $warehouse_data) {
            if (!empty($warehouse_data['amount'])) {
               $count ++;
            }
         }
      }else {
         if (!empty($product_data['amount'])) {
            $count++;
         }
      }
   }
   return $count;
}
function fn_cp_warehouses_exim_get_price($product_id, $warehouse_id)
{
    if (!(isset($product_id) && isset($warehouse_id))) {
        return 0;
    }
    
    $price = fn_cp_warehouse_products_prices_get_product_price($product_id, $warehouse_id);

    $export_data = $price;
    return $export_data;
}
function fn_cp_warehouses_exim_aggregate_prices(array $item, array $aggregated_data)
{
  
    foreach ($aggregated_data['values'] as $key => $value) {
        unset($aggregated_data['values'][$key]);
        
        if (fn_string_not_empty($value)) {
            list(, $key) = explode('cp_warehouses_price_', $key);
            list($key, ) = explode('_P', $key);
            $aggregated_data['values'][$key] = $value;
        }
    }
    
    return $aggregated_data['values'];
}
function fn_cp_warehouses_exim_set_product_prices($product_id, $warehouse_price_data, $reset_inventory, $price_delimiter)
{ 
  $symbols_in_price = [' ', ',', '-', '.'];

  if (!empty($product_id) && !empty($warehouse_price_data)) {
    foreach ($warehouse_price_data as $warehouse_id => $product_price) {

      $product_price = str_replace(array_diff($symbols_in_price, [$price_delimiter]), '', $product_price);
      
      fn_cp_update_warehouse_product_price($product_id, $warehouse_id, fn_exim_import_price($product_price, $price_delimiter));
    }
  }
}
function fn_cp_check_wishlist($product_id, $warehouse_id)
{
  $wishlist = Tygh::$app['session']['wishlist']['products'];
  if (!empty($wishlist)) {
    foreach ($wishlist as $key => $wishlist_data) {
      
      if (!empty($wishlist_data['product_id']) 
          && $wishlist_data['product_id'] == $product_id
          && !empty($wishlist_data['extra']['warehouse_id'])
          && $wishlist_data['extra']['warehouse_id'] == $warehouse_id) {
        return $key;
      }
    }
  }
  return false;
}

<?php

use Tygh\Registry;
use Tygh\Tygh;
use Tygh\Enum\ProfileFieldSections;

defined('BOOTSTRAP') or die('Access denied');
$cart = &Tygh::$app['session']['cart'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	return;
}
if ($mode === 'checkout') {

	$s_profile_fields = fn_get_profile_fields('S');
	foreach ($s_profile_fields['S'] as $field_id => &$field) {
		if ($field['profile_show'] != 'Y' && $field['field_name'] != 's_address') {
			unset($s_profile_fields['S'][$field_id]);
		}
      if ($field['profile_required'] == 'Y') {
         $field['checkout_required'] = 'Y';
      }
	}
	Registry::get('view')->assign('s_profile_fields', $s_profile_fields);

   foreach ($cart['products'] as $key => $product) {
      
      if (isset($product['extra']['warehouse_id'])) {
         $warehouse_id = $product['extra']['warehouse_id'];
      }
   }
   if (isset($warehouse_id)) {
      $warehouse_address = db_get_field("SELECT CONCAT(city, ', ', pickup_address)  FROM ?:store_location_descriptions WHERE store_location_id = ?i AND lang_code = ?s",$warehouse_id, CART_LANGUAGE);
      Registry::get('view')->assign('warehouse_address', $warehouse_address);
   }

   $manufacturer_code_id = Registry::get('addons.cp_change_inv.manufacturer_code_id');
   $manufacturer_id = Registry::get('addons.cp_change_inv.manufacturer_id');

   $cart_products = Tygh::$app['view']->getTemplateVars('cart_products');

   foreach ($cart_products as &$product) {
      $product['manufacturer_code'] = db_get_field("SELECT vd.variant FROM ?:product_features_values as v LEFT JOIN ?:product_feature_variant_descriptions as vd ON v.variant_id = vd.variant_id WHERE v.product_id = ?i AND v.feature_id = ?i AND vd.lang_code = ?s", $product['product_id'], $manufacturer_code_id, CART_LANGUAGE);
      $product['manufacturer'] = db_get_field("SELECT vd.variant FROM ?:product_features_values as v LEFT JOIN ?:product_feature_variant_descriptions as vd ON v.variant_id = vd.variant_id WHERE v.product_id = ?i AND v.feature_id = ?i AND vd.lang_code = ?s", $product['product_id'], $manufacturer_id, CART_LANGUAGE);
   }
   
   Tygh::$app['view']->assign('cart_products', $cart_products);

   $product_groups = Tygh::$app['view']->getTemplateVars('product_groups');
   
   if (isset($product_groups)) {
      foreach ($product_groups as &$group) {
         foreach ($group['shippings'] as $key => &$shipping_data) {
            if ($shipping_data['service_code'] == 'pickup') {
               fn_cp_warehouse_selection($shipping_data, $warehouse_id);
               if (count($shipping_data['data']['stores']) == 0) {
                  unset($group['shippings'][$key]);
               }
            }
         }
      }
      Tygh::$app['view']->assign('product_groups', $product_groups);
   }

   /*gMelnikov 14.09.2020*/
   $carts = Tygh::$app['view']->getTemplateVars('carts');
   if (!empty($carts)) {
      $current_cart_vendor_id = !empty($_REQUEST['vendor_id']) ? $_REQUEST['vendor_id'] : null;
      if ($current_cart_vendor_id == null) {
         $cp_carts = $carts;
         reset($cp_carts);
         $current_cart_vendor_id = key($cp_carts);
      }

      $cp_completed_orders = !empty(Tygh::$app['session']['cp_completed_orders']) ? Tygh::$app['session']['cp_completed_orders'] : null;
      $cp_is_place_all_orders = Tygh::$app['session']['is_place_all_orders'];

      if ($cp_is_place_all_orders) {

         $i = !empty($cp_completed_orders) ? count($cp_completed_orders) + 1 : 1;
         
         foreach ($carts as $vendor_id => &$cart) {
            $cart['checkout_order_number'] = $i;
            
            if ($vendor_id == $current_cart_vendor_id) {
               $cart['order_active'] = true;
            }
            $i ++;
         }
         
         Tygh::$app['view']->assign('cp_current_checkout_order_number', $carts[$current_cart_vendor_id]['checkout_order_number']);
         Tygh::$app['view']->assign('carts', $carts);
         Tygh::$app['view']->assign('cp_popup_order_completed', true);
         
         if (!empty($cp_completed_orders)) {
            foreach ($cp_completed_orders as $order_id => $checkout_order_number) {
               $full_data_complected_orders[$order_id] = fn_get_order_info($order_id);
               $full_data_complected_orders[$order_id]['cp_checkout_order_number'] = $checkout_order_number;
            }
            
            Tygh::$app['view']->assign('cp_completed_orders', $full_data_complected_orders);
         }
      }else {

         foreach ($carts as $vendor_id => $tmp_cart) {
            if ($vendor_id == $current_cart_vendor_id) {
               $current_vendor_cart[$vendor_id] = $tmp_cart;
            }
         }
         
         if (!empty($current_vendor_cart)) {
            Tygh::$app['view']->assign('carts', $current_vendor_cart);
         }
      }
   }
   /*gMelnikov 14.09.2020*/

}elseif ($mode == 'complete') {
   $carts = Tygh::$app['view']->getTemplateVars('carts');
   if (empty($carts)) {
      unset(Tygh::$app['session']['cp_completed_orders']);
   }
}
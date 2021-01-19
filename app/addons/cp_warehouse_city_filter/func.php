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
function fn_cp_warehouse_city_filter_get_product_filter_fields(&$filters) {
    $filters['W'] = array(
      'db_field' => 'warehouse_id',
      'table' => 'warehouses_products_amount',
      'description' => 'cp_cf_warehouse_city',
      'condition_type' => 'F',
      'variant_name_field' => '?:store_location_descriptions.city',
      'conditions' => function($db_field, $join, $condition) {
        $join .= db_quote(" LEFT JOIN ?:store_location_descriptions ON ?:warehouses_products_amount.warehouse_id = ?:store_location_descriptions.store_location_id AND ?:store_location_descriptions.lang_code = ?s", CART_LANGUAGE
        );
        return array($db_field, $join, $condition);
      },
    );
}
function fn_cp_warehouse_city_filter_generate_filter_field_params(&$params, $filters, $selected_filters, $filter_fields, $filter, $structure)
{
  if (AREA == 'C' && isset($filter['field_type']) && $filter['field_type'] = 'W') {
    
    if (isset($params['filter_params']) && isset($params['filter_params']['warehouse_id']) && !empty($params['filter_params']['warehouse_id'])) {
      $params['cp_warehouse_filter_ids'] = $params['filter_params']['warehouse_id'];
    }
  } 
}
function fn_cp_warehouse_city_filter_get_products ($params, $fields, $sortings, &$condition, &$join, $sorting, $group_by, $lang_code, $having) {
  if (AREA == 'C' && !empty($params['cp_warehouse_filter_ids'])) {
    $cities = db_get_fields("SELECT DISTINCT city FROM ?:store_location_descriptions WHERE store_location_id IN (?n) AND lang_code = ?s", $params['cp_warehouse_filter_ids'], CART_LANGUAGE);
    if (!empty($cities)) {
        $join .= db_quote(" INNER JOIN ?:store_location_descriptions ON cp_wh_am.warehouse_id = ?:store_location_descriptions.store_location_id AND ?:store_location_descriptions.lang_code = ?s", CART_LANGUAGE
        ); 
        $condition .= db_quote(' AND ?:store_location_descriptions.city IN (?a)', $cities);
    }
  }
}
function fn_cp_warehouse_city_filter_add_warehouses_extra_data_post(&$products, $product_id, &$product_data, $params, $lang_code)
{
    if (AREA == 'C' && !empty($params['cp_warehouse_filter_ids']) && !empty($product_data['extra_warehouse_data'])) {
      
      $cities = db_get_fields("SELECT DISTINCT city FROM ?:store_location_descriptions WHERE store_location_id IN (?n) AND lang_code = ?s", $params['cp_warehouse_filter_ids'], CART_LANGUAGE);

      foreach ($product_data['extra_warehouse_data'] as $warehouse_id => $warehouses_data) {
        
        $warehouse_city = db_get_field("SELECT city FROM ?:store_location_descriptions WHERE store_location_id = ?i AND lang_code = ?s", $warehouse_id, CART_LANGUAGE);
        
        if (!in_array($warehouse_city, $cities)) {
          unset($product_data['extra_warehouse_data'][$warehouse_id]);
        } 
      }
      if (empty($product_data['extra_warehouse_data'])) {
        unset($products[$product_id]);
      }
    }
}
function fn_cp_warehouse_city_filter_get_filters_products_count_post($params, $lang_code, &$filters, $selected_filters) {
  if (AREA == 'C' && !empty($filters)) {
      foreach($filters as &$filter_data) {
          if (!empty($filter_data['field_type']) && $filter_data['field_type'] == 'W' && !empty($filter_data['variants'])) {
              $exists = array();
              if (!empty($filter_data['selected_variants'])) {
                  foreach($filter_data['selected_variants'] as $sv_key => $sv_val) {
                      if (!in_array($sv_val['variant'], $exists)) {
                          $exists[] = $sv_val['variant'];
                      }
                  }
              }
              foreach($filter_data['variants'] as $v_key => $v_val) {
                  if (!empty($v_val['variant'])) {
                      if (!in_array($v_val['variant'], $exists)) {
                          $exists[] = $v_val['variant'];
                      } else {
                          unset($filter_data['variants'][$v_key]);
                      }
                  } else {
                      unset($filter_data['variants'][$v_key]);
                  }
              }
          }
      }
  }
}
/*HOOKS*/
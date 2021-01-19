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
function fn_cp_warehouse_amount_filter_get_product_filter_fields(&$filters) {
    $filters['Z'] = array(
      'db_field' => 'warehouse_id',
      'table' => 'warehouses_products_amount',
      'description' => 'cp_cf_warehouse_amount',
      'condition_type' => 'A',
      'variant_name_field' => '?:store_location_descriptions.name',
      'conditions' => function($db_field, $join, $condition) {
        $join .= db_quote(" LEFT JOIN ?:store_location_descriptions ON ?:warehouses_products_amount.warehouse_id = ?:store_location_descriptions.store_location_id AND ?:store_location_descriptions.lang_code = ?s", CART_LANGUAGE
        );
        return array($db_field, $join, $condition);
      }
    );
}
function fn_cp_warehouse_amount_filter_get_current_filters_post($params, &$filters, $selected_filters, $area, $lang_code, $variant_values, $range_values, &$field_variant_values, $field_range_values)
{
  if (!empty($filters)) {
    foreach ($filters as $filter_id => &$filter) {
      if (!empty($filter['field_type']) && $filter['field_type'] == 'Z') {
        $field_variant_values[$filter['filter_id']]['variants'] = [];
        $filter['show_empty_filter'] = true;
      }
    }
  }
}
function fn_cp_warehouse_amount_filter_generate_filter_field_params(&$params, $filters, $selected_filters, $filter_fields, $filter, $structure)
{ 
  if (AREA == 'C' && isset($filter['field_type']) && $filter['field_type'] == 'Z') {
    if (!empty($selected_filters[$filter['filter_id']])) {
      $params['cp_warehouse_amount'] = current($selected_filters[$filter['filter_id']]);
    }
  }
}
function fn_cp_warehouse_amount_filter_get_products ($params, $fields, $sortings, &$condition, $join, $sorting, $group_by, $lang_code, $having) 
{ 

  if (AREA == 'C' && !empty($params['cp_warehouse_amount'])) {
    $condition = str_replace("cp_wh_am.amount > 0", db_quote("cp_wh_am.amount >= ?i", (int) $params['cp_warehouse_amount']), $condition);
  }
}
function fn_cp_warehouse_amount_filter_add_warehouses_extra_data_post(&$products, $product_id, &$product_data, $params, $lang_code)
{ 
    if (AREA == 'C' && !empty($params['cp_warehouse_amount']) && !empty($product_data['extra_warehouse_data'])) {
      foreach ($product_data['extra_warehouse_data'] as $warehouse_id => $warehouses_data) {
        if ($warehouses_data['amount'] < $params['cp_warehouse_amount']) {
          unset($product_data['extra_warehouse_data'][$warehouse_id]);
        } 
      }
    }
}
function fn_cp_warehouse_amount_filter_get_filters_products_count_post($params, $lang_code, &$filters, $selected_filters)
{
    foreach ($filters as &$filter) {
      if ($filter['field_type'] == 'Z') {
        if (!empty($selected_filters[$filter['filter_id']])) {
          $filter['warehouse_amount'] = current($selected_filters[$filter['filter_id']]);
          if (!empty($filter['warehouse_amount'])) {
            $filter['selected_range'] = true;
          }
        }
      }  
    }
    unset($filter);
}
/*HOOKS*/
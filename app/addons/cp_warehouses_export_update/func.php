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

/*HOOKS*/
function fn_cp_warehouses_export_update_get_store_locations_before_select($params, $fields, $joins, &$conditions, $sortings, $items_per_page, $lang_code)
{
  $auth = Tygh::$app['session']['auth'];

  if (!empty($auth['company_id']) && (Registry::get('runtime.controller') == 'exim' && Registry::get('runtime.mode') == 'export' || Registry::get('runtime.controller') == 'destinations') && !empty($auth['user_type']) && $auth['user_type'] == 'V') {
    $conditions['company_id'] = db_quote('?:store_locations.company_id IN (?n)', $auth['company_id']);
  }
}
function fn_cp_warehouses_export_update_cp_quantity_list_before_update_qty($product_id, &$quantity_list, $reset_inventory)
{

  if (!empty($product_id)) {
    $company_id = db_get_field("SELECT company_id FROM ?:products WHERE product_id = ?i",$product_id);
    if (!empty($company_id)) {
      $store_location_ids = db_get_fields("SELECT store_location_id FROM ?:store_locations WHERE company_id = ?i",$company_id);
    }
    if (!empty($store_location_ids)) {
      foreach ($quantity_list as $warehouse_id => $data) {
        if (!in_array($warehouse_id, $store_location_ids)) {
          unset($quantity_list[$warehouse_id]); 
        }
      }
    }
  }
}
/*HOOKS*/
function fn_cp_warehouses_exim_get_list($params)
{   
    $preset_id = !empty($params['preset_id']) ? $params['preset_id'] : null;
    $company_id = null;

    if (!empty($preset_id)) {

      $presets_manager = Tygh::$app['addons.advanced_import.presets.manager'];

      list($presets,) = $presets_manager->find(false, array('ip.preset_id' => $preset_id), false);

      $company_id = !empty($presets[$preset_id]['company_id']) ? $presets[$preset_id]['company_id'] : null;
    }
  
    /** @var Tygh\Addons\Warehouses\Manager $manager */
    $manager = Tygh::$app['addons.warehouses.manager'];
    $warehouses = $manager->getWarehouses($company_id); // why don't use this parametr in warehouses add-on??

    foreach ($warehouses as &$warehouse) {
        $warehouse['show_description'] = true;
        $warehouse['description'] = $warehouse['name'];
        $warehouse['show_name'] = false;
    }
    unset($warehouse);

    return $warehouses;
}


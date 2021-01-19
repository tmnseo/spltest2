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
    return;
}

if ($mode == 'get_fields') {

    $relations = Tygh::$app['view']->getTemplateVars('relations');
    
    $keys = array('warehouses', 'cp_warehouses_price');

    foreach ($relations as $relation_key => $relation) {
        if (in_array($relation_key, $keys)) {
            
            
            if ($relation_key == 'cp_warehouses_price') {
                $warehouses = fn_cp_warehouses_exim_get_list($_REQUEST);
                foreach ($warehouses as $warehouse_id => $warehouse) {
                    $warehouse['show_description'] = true;
                    $warehouse['description'] = __('cp_warehouse_product_prices.price')." ".$warehouse['name'];
                    $warehouse['show_name'] = false;

                    $cp_warehouses[$warehouse_id."_P"] = $warehouse;
                }
                
                unset($warehouse);

                $relations[$relation_key]['fields'] = $cp_warehouses;
                

            }elseif ($relation_key == 'warehouses') {
                $warehouses = fn_cp_warehouses_exim_get_list($_REQUEST);

                foreach ($warehouses as $warehouse_id => &$warehouse) {
                    $warehouse['show_description'] = true;
                    $warehouse['description'] = __('cp_warehouse_product_prices.qty')." ".$warehouse['name'];
                    $warehouse['show_name'] = false;
                }
                unset($warehouse);

                $relations[$relation_key]['fields'] = $warehouses;

            }
            Tygh::$app['view']->assign('relations', $relations);
        }
    }
}
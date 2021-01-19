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

use Tygh\Addons\AdvancedImport\Readers\Xml;
use Tygh\Enum\Addons\AdvancedImport\ImportStrategies;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

require_once('app/addons/warehouses/schemas/exim/products.functions.php'); 

foreach (fn_warehouses_exim_get_list() as $warehouse_name => $warehouse) {
    $field = sprintf("%s (Warehouse price)", $warehouse['name']);
    
    $schema['export_fields'][$field] = [
        'process_get' => ['fn_cp_warehouses_exim_get_price', '#key', $warehouse['store_location_id']],
        'export_only' => true,
        'linked'      => false,
        'warehouse_id'  => $warehouse['store_location_id']
    ];
}

return $schema;
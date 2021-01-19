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

use Tygh\Addons\CpVendorWarehouses\Warehouses\Warehouse;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == 'view_map') {
    
    if (!empty($_REQUEST['warehouse_id'])) {

        $warehouses_data = [];
        $warehouse_id = $_REQUEST['warehouse_id'];

        $warehouse = new Warehouse($warehouse_id);

        list($longitude, $latitude) = explode(',', $warehouse->getCoordinates());
        
        $warehouses_data = [
            'warehouse_id' => $warehouse_id,
            'country'      => $warehouse->country,
            'city'         => $warehouse->city,
            'address'      => $warehouse->address,
            'phone'        => $warehouse->phone,
            'worktime'     => $warehouse->worktime,
            'latitude'     => $latitude,
            'longitude'    => $longitude
        ];
        
        Tygh::$app['view']->assign('warehouse_data', $warehouses_data);
        Tygh::$app['view']->display('addons/cp_vendor_warehouses/views/warehouses/map.tpl');
        exit;
    } 
}
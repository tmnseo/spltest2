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

namespace Tygh\Addons\CpVendorWarehouses\Warehouses;

use Tygh\Addons\CpVendorWarehouses\Warehouses\Warehouse;

class WarehousesManager
{   

    public static function getDataForTemplate($company_id)
    {
        $vendor_warehouses_data = [];
        $vendor_warehouses_ids = db_get_fields("SELECT store_location_id FROM ?:store_locations WHERE company_id = ?i AND status <> 'D' ORDER BY position" , $company_id);
        
        foreach ($vendor_warehouses_ids as $warehouse_id) {
            $warehouse = new Warehouse($warehouse_id);

            list($longitude, $latitude) = explode(',', $warehouse->getCoordinates());
            
            $vendor_warehouses_data[] = [
                'warehouse_id' => $warehouse_id,
                'description'  => $warehouse->getDescription(),
                'image_href'   => $warehouse->getMapImage(),
                'country'      => $warehouse->country,
                'city'         => $warehouse->city,
                'address'      => $warehouse->address,
                'latitude'     => $latitude,
                'longitude'    => $longitude
            ];
            
        }

        return $vendor_warehouses_data;
    }
}
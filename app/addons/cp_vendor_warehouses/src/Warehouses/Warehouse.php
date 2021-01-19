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

use Tygh\Addons\CpVendorWarehouses\Api\StaticApi;

class Warehouse {
        
    private $id = 0;

    public $city = "";
    public $country = "";
    public $address = "";
    public $phone = "";
    public $worktime = "";
        
    function __construct($warehouse_id)
    {
        $this->id = $warehouse_id;
        $warehouse_data = db_get_row("SELECT country, city, pickup_address, pickup_phone, pickup_time FROM ?:store_locations as sl LEFT JOIN ?:store_location_descriptions as sld ON sl.store_location_id = sld.store_location_id WHERE sl.store_location_id = ?i AND lang_code = ?s", $warehouse_id, CART_LANGUAGE);
        
        $this->country = $warehouse_data['country'];
        $this->city = $warehouse_data['city'];
        $this->address = $warehouse_data['pickup_address'];
        $this->phone = $warehouse_data['pickup_phone'];
        $this->worktime = $warehouse_data['pickup_time'];    
    }

    public function getDescription()
    {
        return db_get_field("SELECT CONCAT(city, ', ', pickup_address) as description FROM ?:store_location_descriptions WHERE store_location_id = ?i AND lang_code = ?s", $this->id, CART_LANGUAGE);
    }
    public function getMapImage()
    {
        return StaticApi::getMapHref($this);
    }
    public function getCoordinates()
    {
        return db_get_field("SELECT CONCAT(longitude, ',', latitude) as coordinates FROM ?:store_locations WHERE store_location_id = ?i", $this->id);
    }
}
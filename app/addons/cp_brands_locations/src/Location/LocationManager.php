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

namespace Tygh\Addons\CpBrandsLocations\Location;

use Tygh\Addons\CpBrandsLocations\Service;
use Tygh;

class LocationManager
{
    public static function addBrandLocations($data)
    {   
        if (empty($data['company_id']) || empty($data['brand_variant_id'])) {
            return false;
        }

        db_query("DELETE FROM ?:cp_brands_locations WHERE brand_variant_id = ?i AND company_id = ?i", $data['brand_variant_id'], $data['company_id']);

        if (!empty($data['destinations_ids'])) {
            foreach ($data['destinations_ids'] as $destination_id) {    
                $data['destination_id'] = $destination_id;
                db_query("INSERT INTO ?:cp_brands_locations ?e", $data);    
            }
        }
        
    }

    public static function getVendorBrandsLocations($params, $items_per_page, $lang_code = CART_LANGUAGE)
    {   
        $default_params = array(
            'page' => 1,
            'items_per_page' => $items_per_page
        );

        $params = array_merge($default_params, $params);

        $conditions = '1';

        $limit = '';

        if (!empty($params['items_per_page'])) {

            $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:cp_brands_locations WHERE ?p GROUP BY brand_variant_id", $conditions);
            $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
        }

        $brands_locations = db_get_array("SELECT * FROM ?:cp_brands_locations WHERE company_id = ?i ", $params['company_id']);
        
        $brands_locations_for_template = [];
        
        foreach ($brands_locations as $bl_data) {
            if (!empty($brands_locations_for_template[$bl_data['brand_variant_id']])) {
                $brands_locations_for_template[$bl_data['brand_variant_id']]['destinations_ids'][] = $bl_data['destination_id'];
            }else {
                $brands_locations_for_template[$bl_data['brand_variant_id']] = [
                    'brand_variant_id' => $bl_data['brand_variant_id'],
                    'brand_name' => Service::getVariantName($bl_data['brand_variant_id'], $lang_code),
                    'destinations_ids' => [
                        $bl_data['destination_id']
                    ]
                ];
            }
        }
        
        return array($brands_locations_for_template, $params);
    }
    public static function getCustomerCurrentDestinationId($city_id) 
    {   
        
        $location = self::getCurrentLocation($city_id);
        $destination_id = fn_get_available_destination($location);

        if (!$destination_id) {
            /** @var \Tygh\Location\Manager $manager */
            $manager = Tygh::$app['location'];
            $destination_id = $manager->getDestinationId();

        }

        return $destination_id;
    }
    public static function getCurrentLocation($city_id)
    {
        $location_data = db_get_row("
            SELECT country_code as country ,state_code as state, city FROM ?:rus_cities as rc 
            LEFT JOIN ?:rus_city_descriptions as rcd ON rcd.city_id = rc.city_id AND lang_code = ?s
            WHERE rc.city_id = ?i", DESCR_SL, $city_id);
        
        $location = [
            'country' => !empty($location_data['country']) ? $location_data['country'] : '',
            'state' => !empty($location_data['state']) ? $location_data['state'] : '',
            'city' => !empty($location_data['city']) ? $location_data['city'] : '',
        ];
        
        return $location;
    }
    public static function getVendorDestinationsForBrand($company_id, $brand_variant_id)
    {
        return db_get_fields("SELECT destination_id FROM ?:cp_brands_locations WHERE company_id = ?i AND brand_variant_id = ?i", $company_id, $brand_variant_id);
    }
}
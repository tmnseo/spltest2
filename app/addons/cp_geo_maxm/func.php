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
use Tygh\Addons\CpMatrixDestinations\Geo\Geo;
use Tygh\Addons\CpMatrixDestinations\ServiceProvider;

if (!defined('BOOTSTRAP')) { die('Access denied'); }



function fn_cp_cities_find_cities($params, $lang_code = CART_LANGUAGE, $items_per_page = 10)
{
    $condition = array();
    $prefix = explode(',', __('addons.rus_cities.city_prefix'));

    if (empty($params['q'])) {
        return array();
    }

    $params['q'] = str_replace($prefix, '', $params['q']);
    $search = trim($params['q']) . '%';

    if (!empty($params['check_country'])) {
        $condition['country_code'] = db_quote('AND c.code = ?s', $params['check_country']);
    }

    if (!empty($params['check_state'])) {
        $condition['states_code'] = db_quote('AND s.code = ?s', $params['check_state']);
    }

    $fields = array(
        db_quote('DISTINCT cd.country'),
        db_quote('c.code AS country_code'),
        db_quote('sd.state'),
        db_quote('s.code AS state_code'),
        db_quote('rcd.city'),
        db_quote('rc.city_id'),
        db_quote('rc.zipcode'),
    );

    $join = array(
        db_quote('LEFT JOIN ?:rus_cities           AS rc ON rc.city_id = rcd.city_id'),
        db_quote('LEFT JOIN ?:countries            AS c  ON rc.country_code = c.code'),
        db_quote('LEFT JOIN ?:country_descriptions AS cd ON c.code = cd.code AND cd.lang_code = ?s', $lang_code),
        db_quote('LEFT JOIN ?:states               AS s  ON rc.state_code = s.code AND c.code = s.country_code'),
        db_quote('LEFT JOIN ?:state_descriptions   AS sd ON s.state_id = sd.state_id AND sd.lang_code = ?s', $lang_code),
    );

    $condition['countries_status'] = db_quote('AND c.status = ?s', 'A');
    $condition['states_status'] = db_quote('AND s.status = ?s', 'A');
    $condition['cities_status'] = db_quote('AND rc.status = ?s', 'A');
    $condition['search'] = db_quote('AND (rcd.city LIKE ?l OR sd.state LIKE ?l)', $search, $search);
    $condition['city_lang'] = db_quote('AND rcd.lang_code = ?s', $lang_code);

    /**
     * Executes before fetching cities from the database,
     * allow to modify SQL query.
     *
     * @param array    $params         City search parameters
     * @param string   $lang_code      Two-letter language code
     * @param int      $items_per_page Amount of cities to fetch
     * @param string   $search         City search criterion
     * @param string[] $fields         Fields to fetch from the database
     * @param string[] $join           JOIN part of SQL query
     * @param string[] $condition      Filter conditions
     */

    $fields = implode(',', $fields);
    $join = implode(' ', $join);
    $condition = implode(' ', $condition);

    $cities = db_get_array(
        'SELECT ?p'
        . ' FROM ?:rus_city_descriptions AS rcd'
        . ' ?p'
        . ' WHERE 1=1'
        . ' ?p'
        . ' ORDER BY rcd.city LIKE ?l DESC, sd.state LIKE ?l DESC, rcd.city ASC, sd.state ASC'
        . ' LIMIT ?i',
        $fields,
        $join,
        $condition,
        $search,
        $search,
        $items_per_page
    );

    return $cities;
}


function fn_cp_geo_maxm_get_customer_stored_geolocation(){

    $location = array();
    $reader = ServiceProvider::getGeo();
    $location = Geo::getCustomerLocation();
    return $location;
}


function fn_cp_geo_maxm_is_customer_location_detected(){

    $check = Geo::geoIsDeteted();
    return $check;
}


function fn_cp_geo_maxm_define_autocomplete(){
    
    $autocomplite = array();
        $autocomplite = 
        [
            'url'                  => fn_url('city.autocomplete_city'),
            'city_param'           => 'q',
            'country_param'        => 'check_country',
            'items_per_page_param' => 'items_per_page',
            'items_per_page'       => 50,
        ];
    
   return $autocomplite;
}
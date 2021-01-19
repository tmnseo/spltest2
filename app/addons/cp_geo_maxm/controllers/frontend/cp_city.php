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
use Tygh\Addons\CpMatrixDestinations\Model\CityHelper;

defined('BOOTSTRAP') or die('Access denied');

if ($mode == 'autocomplete_city') {
    $params = $_REQUEST;
     $items_per_page = isset($_REQUEST['items_per_page']) ? $_REQUEST['items_per_page'] : 10;
        $cities = fn_cp_cities_find_cities($params, CART_LANGUAGE, $items_per_page);
        $list_cities = fn_rus_cities_format_to_autocomplete($cities);
         $list_cities = json_encode($list_cities);
        Registry::get('ajax')->assign('autocomplete', $list_cities);
        exit();
}

if($mode == 'setup_location'){
    if(isset($_REQUEST['cp_city_id'])){

        Geo::setupCustomerLocation($_REQUEST['cp_city_id']);
        fn_set_cookie('cp_location_confirmed', !empty($_REQUEST['status']) ? $_REQUEST['status'] : true, SESSION_ALIVE_TIME);
    }

    $return_url = !empty($_REQUEST['return_url']) ? $_REQUEST['return_url'] : '';
    if(!empty($_REQUEST['cmp'])){
        $return_url = 'cp_city.service_shipping';
    }

    return array(CONTROLLER_STATUS_OK, $return_url);

}


if($mode =='service_shipping'){
    $cities = CityHelper::getAllEdostCitiesByAlph();
    $variants = array();
    if (!empty($cities)) {
        foreach ($cities as $variant) {
            $variants[fn_substr($variant['city'], 0, 1)][] = $variant;
        }
    }
    ksort($variants);
    
    
    
    Tygh::$app['view']->assign('variants', $variants);
}

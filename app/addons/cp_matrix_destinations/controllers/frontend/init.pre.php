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
use Tygh\Addons\CpMatrixDestinations\ServiceProvider;
use Tygh\Addons\CpMatrixDestinations\GeoIp2;

use Tygh\Addons\CpMatrixDestinations\Geo\Geo;


//require_once(Registry::get('config.dir.addons'). 'cp_matrix_destinations/src/GeoIp2/CpGeoFactory.php');

if (!defined('BOOTSTRAP')) { die('Access denied'); }

$reader = ServiceProvider::getGeo();

//SPECIAL TEST MODE
//
if(!empty($_REQUEST['cp_reset_city_session_data']) && $_REQUEST['cp_reset_city_session_data'] =='yes'){
    Tygh::$app['session']['cp_user_has_defined_city_action_was'] = false;
    Tygh::$app['session']['cp_user_has_defined_city'] = false;
    Tygh::$app['session']['cp_user_has_defined_city_name'] ='';
    Tygh::$app['session']['cp_user_geo_state_code'] ='';
    Tygh::$app['session']['cp_user_geo_country_code'] = '';
    Tygh::$app['session']['cp_user_has_defined_city_id_global'] = 0;
    Tygh::$app['session']['cp_user_not_edost_was_show'] =false;
    
    fn_set_cookie('cp_location_confirmed', '', -1);
    if(isset($_COOKIE['cp_location_confirmed'])) {
        unset($_COOKIE['cp_location_confirmed']);
    }
}


if(!empty($_REQUEST['cp_reset_city_session_to_ip'])){
    $_SERVER['HTTP_X_REAL_IP'] =  $_SERVER['REMOTE_ADDR'] =  $_REQUEST['cp_reset_city_session_to_ip'];
}

if(!empty($_REQUEST['cp_print_geo_data']) && $_REQUEST['cp_print_geo_data'] =='yes'){
    fn_print_r($_SERVER['HTTP_X_REAL_IP']);
    fn_print_r('cp_user_has_defined_city',Tygh::$app['session']['cp_user_has_defined_city']);
    $cp_location_confirmed  = fn_get_cookie('cp_location_confirmed');
    fn_print_r('cp_location_confirmed',$cp_location_confirmed);
    fn_print_r('cp_is_geo_confirmed',Tygh::$app['session']['cp_is_geo_confirmed']);
}


$defined_city = Tygh::$app['session']['cp_user_has_defined_city_action_was'];

if($defined_city){
    return true;
}


$ip = isset($_SERVER['HTTP_X_REAL_IP'])?$_SERVER['HTTP_X_REAL_IP']:$_SERVER['REMOTE_ADDR'];
// $arr = array("185.103.24.207");
// $ip='185.103.24.207';
// $count = count($arr);
// $ip = $arr[0];

try {
    $record = $reader->city($ip);
} catch (\GeoIp2\Exception\AddressNotFoundException $e) {
    $record ='';
}


if(!is_object($record)){
    $city_name ='';
    $county_code='';
}
else{
    $city_name =$record->city->names['ru'];
    $county_code =$record->country->isoCode;
}


$iso_code = $record->mostSpecificSubdivision->isoCode;
$result_city_exist_id = Geo::checkGeoCityByMatrix($city_name,$iso_code);

$is_edost = Geo::$isCityExistEdost;
$in_matrix_table = Geo::$inMatrixTable;
if($is_edost && !$in_matrix_table && $city_name){
    fn_set_notification('N', __('notice'), __('cp_geo_maxm_correct_edost'));
    $result_city_exist_id = Geo::getCurrentCityId();
}


if($county_code !='RU'){
    $result_city_exist_id = Geo::getDefaultCityId();
}
else {


    if (!$is_edost && $city_name) {
        $url = fn_url("cp_city.service_shipping");
        fn_set_notification('W', __('warning'), __('cp_geo_maxm_define_but_not_edost') . '<a target="_blank" href="' . $url . '">' . __('cp_geo_maxm_define_but_not_edost_link') . '</a>');
    }

}

//  fn_print_die($result_city_exist_id);


if(!empty($_REQUEST['cp_matrix_set_city_id_session'])){
    $result_city_exist_id = $_REQUEST['cp_matrix_set_city_id_session'];
}


if(!empty($_REQUEST['cp_print_geo_data']) && $_REQUEST['cp_print_geo_data'] =='yes'){

    fn_print_r('after detecting');
    fn_print_r($_SERVER['HTTP_X_REAL_IP']);
    fn_print_r($result_city_exist_id);
    fn_print_r(Tygh::$app['session']['cp_user_has_defined_city']);
    fn_print_r($iso_code);
    fn_print_die($city_name);
}


if(!$city_name){
    $result_city_exist_id = Geo::getDefaultCityId();
}

if(!$result_city_exist_id){
    Tygh::$app['session']['cp_user_has_defined_city'] = false;
}
else{
    Tygh::$app['session']['cp_user_has_defined_city'] = $result_city_exist_id;
}


Geo::$show_not_in_edost_notice = false;
Geo::setupCustomerLocation($result_city_exist_id);

Tygh::$app['session']['cp_user_has_defined_city_action_was'] = true;
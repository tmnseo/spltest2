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

namespace Tygh\Addons\CpMatrixDestinations\Geo;
use Tygh\Addons\CpMatrixDestinations\Model\CityControllerFabrica;
use Tygh\Addons\CpMatrixDestinations\ServiceProvider;
use Tygh\Addons\CpMatrixDestinations\Model\AbstractCityModel;
use Tygh\Addons\CpMatrixDestinations\City\City;
use Tygh\Addons\CpMatrixDestinations\Model\CityHelper;
use Tygh\Addons\CpMatrixDestinations\Matrix\Matrix;
use Tygh\Addons\CpMatrixDestinations\Stores\Stores;
use Tygh;


/**
 * Class FeaturePurposes
 *
 * @package Tygh\Addons\ProductVariations\Product
 */
class Geo
{
    public static $isCityExist = true;
    public static $isCityExistEdost = true;
    public static $lang_code ='ru';
    public static $inMatrixTable = false;
    public static $default_city_id = 12050;
    public static $current_city_id = 0;
    public static $show_not_in_edost_notice = true;
    
    public static function getCurrentCityId(){
        return self::$current_city_id;
    }

    public static function getDefaultCityId(){
        return self::$default_city_id;
    }

    public static function geoIsDeteted(){

        return Tygh::$app['session']['cp_user_has_defined_city'];
    }

    public static function setupCustomerLocation($city_id){
        Tygh::$app['session']['cp_user_has_defined_city_action_was'] = false;
        Tygh::$app['session']['cp_user_has_defined_city'] = false;
        Tygh::$app['session']['cp_user_geo_city_name'] ='';
        Tygh::$app['session']['cp_user_geo_state_code'] ='';
        Tygh::$app['session']['cp_user_geo_country_code'] = '';

        $reader = ServiceProvider::getGeo();

        $city_data = fn_rus_city_get_city_data(array($city_id));

        $city_data = current($city_data);

        if(empty($city_data)){
            return false;
        }


        Tygh::$app['session']['cp_user_has_defined_city_id_global']   = $city_id;


        //INNER JOIN ?:rus_edost_cities_link as recl ON recl.city_id = rcd.city_id
        $city_name = db_get_field("SELECT rcd.city
                                FROM  ?:rus_city_descriptions as rcd 
                                LEFT JOIN ?:rus_cities as rc ON rc.city_id = rcd.city_id
                                
                                WHERE rcd.lang_code =?s  and  rcd.city_id =?i",CART_LANGUAGE,$city_id);
        if(empty($city_name)){
            return false;
        }

        //$result_city_exist_id = Geo::checkGeoCityByMatrix($city_name,$city_data['state_code']);
        $result_city_exist_id = Geo::checkGeoCity($city_name,$city_data['state_code']);
        if(!$result_city_exist_id){
            Tygh::$app['session']['cp_user_has_defined_city'] = false;
            $url = fn_url("cp_city.service_shipping");
            if(self::$show_not_in_edost_notice) {
                fn_set_notification('W', __('warning'), __('cp_geo_maxm_define_but_not_edost'));
            }
            //fn_set_notification('W', __('warning'), __('cp_geo_maxm_define_but_not_edost').'<a target="_blank" href="'.$url.'">'.__('cp_geo_maxm_define_but_not_edost_link').'</a>');
        }
        else{
            Tygh::$app['session']['cp_user_has_defined_city'] = $result_city_exist_id;
        }


        $is_geo_confirmed = false;
        if(Tygh\Registry::get('addons.cp_geo_maps_ext.status') == "A"){
            $is_geo_confirmed = fn_cp_geo_is_geolocation_confirmed();
            Tygh::$app['session']['cp_is_geo_confirmed'] = $is_geo_confirmed;
        }
        Tygh::$app['session']['cp_user_geo_state_code'] = $city_data['state_code'];
        Tygh::$app['session']['cp_user_geo_city_name'] = Tygh::$app['session']['cp_user_has_defined_city_name'] = $city_name;
        Tygh::$app['session']['cp_user_geo_country_code'] = $city_data['country_code'];
        Tygh::$app['session']['cp_user_has_defined_city_action_was'] = true;
    }

    public static function getCustomerLocation(){


        $location= array();

        if(isset(Tygh::$app['session']['cp_user_geo_state_code'])){
            $state_code = Tygh::$app['session']['cp_user_geo_state_code'];
        }
        else{
            $state_code ='';
        }

        if(isset(Tygh::$app['session']['cp_user_geo_city_name'])){
            $city_name = Tygh::$app['session']['cp_user_geo_city_name'];
        }
        else{
            $city_name ='';
        }

        $city_id =0;
        if(isset(Tygh::$app['session']['cp_user_has_defined_city'])){
            $city_id  = Tygh::$app['session']['cp_user_has_defined_city'];
        }

        $country_code ='';
        if(isset(Tygh::$app['session']['cp_user_geo_country_code'])){
            $country_code = Tygh::$app['session']['cp_user_geo_country_code'];
        }


       // $confirmed = Tygh::$app['session']['cp_is_geo_confirmed'];
        if(Tygh\Registry::get('addons.cp_geo_maps_ext.status') == "A") {
            $confirmed = $is_geo_confirmed = fn_cp_geo_is_geolocation_confirmed();
        }
        else{
            $confirmed = false;
        }

        $location['s_country'] = $country_code;
        $location['s_state'] = $state_code;
        $location['s_city'] =  $location['city']= $city_name;
        $location['cp_city_id'] = $city_id;
        $location['cp_location_confirmed'] =$confirmed;

        // fn_print_die($location);

        return $location;
    }


    public static function convertPrecityToCity($params){

        $City_model = ServiceProvider::getCity();
        $PreCity_model = ServiceProvider::getPreCity();
        $city_table = $PreCity_model->getCityTable();
        $city_table= str_replace("?:","",$city_table);

        if(!empty($params['table']) && $params['table'] ==$city_table && !empty($params['id']) && !empty($params['status']) && $params['status'] =="A" ){

            $city_data = array();
            $city_data['city_name'] = $PreCity_model->getCityName($params['id']);
            $City_model->updateCity($city_data, $city_id = 0, self::$lang_code);
            $PreCity_model->deleteCity($params['id']);
        }
    }


    public static function checkGeoCityByMatrix($city_name,$iso_code=''){
        $city_rus_id = self::checkGeoCity($city_name,$iso_code);
        if(!$city_rus_id){
            self::$isCityExistEdost = false;
            return false;
        }

        self::$current_city_id = $city_rus_id;
        $res = db_get_field("SELECT city_to_id FROM ?:cp_matrix_data WHERE city_to_id =?i",$city_rus_id);
        if($res){
            self::$inMatrixTable = true;
            return $res;
        }
        return self::$inMatrixTable;
    }

    //нужно протестировать
    public static function checkGeoCity($city_name,$iso_code=''){

        $city_rus_id = CityHelper::checkCityForEdostCode($city_name, self::$lang_code,$iso_code);



        $city_data = array();
        $city_data['city_name'] = $city_name;
        $city_data['state_code'] =$iso_code;

        if (!$city_rus_id) {
            self::$isCityExistEdost = false;
            $City_model = ServiceProvider::getPreCity();
            $city_data['status'] ='D';
            CityControllerFabrica::CityPostUpdate($City_model,$city_data,0,self::$lang_code);
            return self::$isCityExistEdost;
        }
        else{

            $City_model = ServiceProvider::getCity();
            $city_exist_id = $City_model->isCityExist($city_name);


            //проверить еслть ли в матрице и если нет то сразу добавить
            $matrix_model = ServiceProvider::getMatrix();
            $check = $matrix_model->checkCityIdMatrixTable($city_rus_id);

            // fn_print_die($check);
            //засунуть город в тариф.
            if(!$check) {
                $Stores = new Stores(self::$lang_code);
                $stores_matrix = $Stores->getstoresForCalculate(true, 0);
                $cities_calc_from_admin = array(array('city_name'=>$city_name,'city_id'=>$city_rus_id));

                $matrix_model->cretaMatrixRecordByCities($stores_matrix, $cities_calc_from_admin);

            }

            if(!$city_exist_id){
                self::$isCityExist = false;
            }

            if(!self::$isCityExist){
                $City_model = ServiceProvider::getCity();
                CityControllerFabrica::CityPostUpdate($City_model,$city_data,0,self::$lang_code);


            }


        }

        if($city_rus_id){
            return $city_rus_id;
        }
    }
}
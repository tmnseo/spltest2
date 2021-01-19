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

namespace Tygh\Addons\CpMatrixDestinations\Matrix;
use Tygh\Addons\CpMatrixDestinations\City\City;
use Tygh\Addons\CpMatrixDestinations\Log\Log;

use Tygh\Addons\CpMatrixDestinations\Settings\Settings;
use Tygh\Addons\CpMatrixDestinations\Stores\Stores;
use Tygh\Addons\CpMatrixDestinations\Edost\Edost;

use Tygh\Http;
use Tygh\Languages\Languages;

/**
 * Class FeaturePurposes
 *
 * @package Tygh\Addons\ProductVariations\Product
 */
class Matrix
{

    protected $CitiesForCalculate = array();
    protected $StoresForCalculate = array();
    protected $uniqueCities = array();
    protected $matrixTariffs  = array();

    public static $edostApiUrl ='http://api.edost.ru/api2.php';
    public static $edostApiUrlReserv ='http://edost.net/api2.php';


    public  $langCode = 'ru';


    public function __construct($lang_code='ru')
    {
        $this->langCode = $lang_code;
    }



    public function addRecordToMatrix($city_name,$city_id){

    }

    public function removeRecordFromMatrix($city_name,$city_id){

    }


    public function getMatrixCities(){

    }


    public function getuniqueCities(){

        return $this->uniqueCities;
    }


    public function getCitiesForCalculate(){

        return $this->CitiesForCalculate;
    }

    public function getStoresForCalculate(){

        return $this->StoresForCalculate;
    }



    public function updateRecordToMatrix($city_data,$city_from_id,$city_to_id){

        $res =  db_query("UPDATE ?:cp_matrix_data SET ?u WHERE city_from_id =?i and city_to_id = ?i", $city_data, $city_from_id, $city_to_id);

        return $res;
    }

    //смотрим на все тарифы по одному напрвлание и берем максимально срещнее значение.



    public function checkCityIdMatrixTable($city_id){
        $check = db_get_row("SELECT * FROM ?:cp_matrix_data WHERE  city_to_id = ?i",$city_id);

        return $check;

    }

    public function updateMatrixTableByTariffs(){


        $addon_settings = Settings::getCpMatrixSettings();

        list($matrix_data,$search) = $this->getMatrixTable(array('timestamp'=>14),0,$this->langCode);
        // $matrixTariffs = $this->matrixTariffs();
        if(!empty($matrix_data)) {
            foreach ($matrix_data as $matrix_item) {

                $current_tariffs = array();

                $time_from =0;
                $time_to =0;
                $time_average =0;

                //20049
                //12050
                //$matrix_item['city_from_id'] = 20049;
                //$matrix_item['city_to_id'] = 12050;

                $res = db_get_array("SELECT * FROM ?:cp_matrix_data_tariffs  WHERE  city_from_id =?i and city_to_id = ?i",$matrix_item['city_from_id'],$matrix_item['city_to_id']);

                if(empty($res)){
                    continue;
                }

                $convert = count($res);

                foreach ($res as $re) {
                    $time_from+=$re['time_from'];
                    $time_to+=$re['time_to'];
                    $time_average+=$re['time_average'];
                }

                $time_from = round($time_from/$convert);
                $time_to = round($time_to/$convert);
                $time_average = round($time_average/$convert);

                $city_data = array();
                $city_data['time_from'] =$time_from;
                $city_data['time_to'] =$time_to;
                $city_data['time_average'] =$time_average;
                $city_data['last_time_update'] =  time();
                $this->updateRecordToMatrix($city_data,$matrix_item['city_from_id'],$matrix_item['city_to_id']);
            }
        }
    }

    public function getAverageDeliveryTimebyTariffs(){

    }





    public function buildMatrixTarriffs($mode='live'){

        if($mode =='test'){
            return $this;
        }
        //TO DO ДОБАВИТЬ ВОЗМОЖНОСТЬ ПОЛУЧИТЬ ДАННЫЕ ТОЛЬКО ЗА ОПРЕДЕЛЕННЫЕ ДНИ

        $addon_settings = Settings::getCpMatrixSettings();
        if(empty($addon_settings['edost_matrix_last_update'])){
            $addon_settings['edost_matrix_last_update'] =14;
        }


        Log::addLogRecord(array('time'=>time(),'status'=>'E','data'=>'start_edost_send','type'=>"I",'parent_log_id'=>0));


        list($matrix_data,$search) = $this->getMatrixTable(array('timestamp'=>$addon_settings['edost_matrix_last_update']),0,$this->langCode);


        if(!empty($matrix_data)) {
            foreach ($matrix_data as $matrix_item) {

                $code_from = db_get_field("SELECT edost_code FROM ?:rus_edost_cities_link WHERE city_id =?i",$matrix_item['city_from_id']);
                $code_to = db_get_field("SELECT edost_code FROM ?:rus_edost_cities_link WHERE city_id =?i",$matrix_item['city_to_id']);

                if(empty($code_to) or empty($code_from)){
                    continue;
                }
                $post = array(
                    'id' => $addon_settings['edost_id'],
                    'p' => $addon_settings['edost_password'],
                    'from_city' =>$code_from,
                    'to_city' => $code_to,
                    'weight' =>$addon_settings['edost_weight'],
                    'zip' => '',
                    'strah' => '0'
                );

                $response = Http::post(Matrix::$edostApiUrl,$post);

                Settings::incrementEdostCounter('cp_edost_counter');

                $parsed_response = Edost::_getRates($response);

                if(!empty($parsed_response) && is_array($parsed_response)) {
                    foreach ($parsed_response as $tariff_data){
                        if(empty($tariff_data['id']) or empty($tariff_data['company'])){
                            continue;
                        }

                        $day_matrix = array();
                        $time_from =0;
                        $time_to =0;
                        $time_average =0;
                        $day  =$tariff_data['day'];

                        $tariff_data['day']= str_replace(" ","",$tariff_data['day']);
                        $tariff_data['day']= str_replace("дня","",$tariff_data['day']);
                        $tariff_data['day']= str_replace("дней","",$tariff_data['day']);
                        $tariff_data['day']= str_replace("день","",$tariff_data['day']);

                        $tariff_data['day'] = explode("-",$tariff_data['day']);


                        if(empty($tariff_data['day'])){
                            continue;
                        }

                        if(count($tariff_data['day']) > 1){

                            $time_from = $tariff_data['day'][0];
                            $time_to = $tariff_data['day'][1];
                            $time_average = round(($time_from+$time_to)/2);
                        }
                        else if(count($tariff_data['day']) == 1){
                            $time_from =$time_to = $time_average = $tariff_data['day'][0];
                        }



                        $data_matrix_tariff = array(    'city_from_id'=>$matrix_item['city_from_id'],
                            'city_to_id'=> $matrix_item['city_to_id'],
                            'time_from'=>$time_from,
                            'time_to'=>$time_to,
                            'time_average'=>$time_average,
                            'edost_service_id'=>$tariff_data['id'],
                            'day'=>$day,
                            'price'=>$tariff_data['price']);

                        $this->matrixTariffs[] = $data_matrix_tariff;
                        db_query("REPLACE INTO ?:cp_matrix_data_tariffs ?e", $data_matrix_tariff);

                    }
                }
            }

            Log::addLogRecord(array('time'=>time(),'status'=>'E','data'=>'end_edost_send','type'=>"I",'parent_log_id'=>0));

        }

        return $this;
    }

    public function saveMatrixTarrif($data){
        if(empty($data)){
            $data = $this->matrixTariffs;
        }

        if(empty($data)){
            return false;
        }
    }

    public static function sortUniqueCityIds($matrix_data){

        if(empty($matrix_data)){
            return array();
        }

        $ids = array();
        foreach ($matrix_data as $item) {
            $ids[$item['city_from_id']] =$item['city_from_id'];
            $ids[$item['city_to_id']] =$item['city_to_id'];
        }

        return $ids;
    }


    public function getMatrixTableStateCodes($unique_ids){


        $state_codes = db_get_array("SELECT rc.city_id,rc.state_code FROM ?:rus_cities as rc  WHERE rc.city_id in (?n)",$unique_ids);

        if(empty($state_codes)){
            return array();
        }

        $state_code_ids_data = array();
        foreach ($state_codes as $state_code) {
            $state_code_ids_data[$state_code['city_id']] = $state_code['state_code'];
        }

        return $state_code_ids_data;


        //$state_codes_from = db_get_array("SELECT rc.state_code,rc.city_id,cmd.city_from_id FROM ?:rus_cities as rc LEFT JOIN ?:cp_matrix_data as cmd ON cmd.city_from_id = rc.city_id",'cmd.city_from_id');
        //$state_codes_to   = db_get_array("SELECT rc.state_code,rc.city_id,cmd.city_to_id FROM ?:rus_cities as rc LEFT JOIN ?:cp_matrix_data as cmd ON cmd.city_to_id = rc.city_id",'cmd.city_to_id');

        //return array($state_codes_from,$state_codes_to);

    }

    public function getMatrixTable($params,$items_per_page,$lang_code){

        $default_params = [
            'page'           => 1,
            'items_per_page' => $items_per_page,
        ];

        $params = array_merge($default_params, $params);

        $joins =  array();

        $fields = ['a.city_from_id', 'a.city_to_id', 'a.city_from', 'a.city_to', 'a.time_from', 'a.time_to', 'a.time_average', 'a.last_time_update'];

        $condition = 'WHERE 1=1';



        if (!empty($params['timestamp'])) {

            $params['timestamp'] = $params['timestamp']*24*60*60;
            $condition .= db_quote(' AND a.last_time_update <= ?i', time() - $params['timestamp']);
        }


        /*
        if (!empty($params['q'])) {
            $condition .= db_quote(' AND b.state LIKE ?l', '%' . $params['q'] . '%');
        }
        if (!empty($params['country_code'])) {
            $condition .= db_quote(' AND a.country_code = ?s', $params['country_code']);
        }

        */

        $sorting = 'ORDER BY  a.last_time_update';
        $limit = $group = '';
        if (!empty($params['items_per_page'])) {
            $params['total_items'] = db_get_field('SELECT count(*) FROM ?:cp_matrix_data as a ?p', $condition);
            $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
        }

        /**
         * Prepare params for getting states SQL query
         *
         * @param array  $params         Params list
         * @param int    $items_per_page States per page
         * @param string $lang_code      Language code
         * @param array  $fields         Fields list
         * @param array  $joins          Joins list
         * @param string $condition      Conditions query
         * @param string $group          Group condition
         * @param string $sorting        Sorting condition
         * @param string $limit          Limit condition
         */

        $matrix_data = db_get_array(
            'SELECT ' . implode(', ', $fields) . ' FROM ?:cp_matrix_data as a ?p ?p ?p ?p ?p',
            implode(' ', $joins), $condition, $group, $sorting, $limit
        );

        /**
         * Actions after states list was prepared
         *
         * @param array  $params         Params list
         * @param int    $items_per_page States per page
         * @param string $lang_code      Language code
         * @param array  $states         List of selected states
         */
        fn_set_hook('get_cp_matrix_post', $params,  $items_per_page, $lang_code, $matrix_data);

        return array($matrix_data, $params);




    }


    public function recalculateMatrixTable(City $city, Stores $stores){

        //$CitiesForCalculate = $Matrix->getCitiesForCalculate();
        //$rus_cities_added_admin  = $city->getRusCitiesAddedByadmin();
        //достаем все уникальные добавленные города из админки. смотрим чего нет в табоице матрицы и берем список



        $this->findCitiesFromAdminForCalculate($city);



        //достаем уникальные ПВЗ И МАГАЗИНЫ ПО ГОРОДАМ.  смотрим чего нет в табоице матрицы и берем список.
        //$this->findStoresForCalculate($stores,0);

        $cities_calc_from_admin = $this->getCitiesForCalculate();

        //$stores_for_calc = $this->getStoresForCalculate();
        //ВСЕ ЗНАЧЕНИЯ ГОРОДОВ И ПВЗ ДЛЯ МАТРИЦА ПРОСЧЕТА.
        // $cities_matrix = $city->getRusCitiesAddedByadmin();
        $stores_matrix = $stores->getstoresForCalculate(true,0);


        //ДОБАВЛЯЕМ ТОЛЬКО ИЗ ПВЗ ДО ГОРОДОВ.
        $this->cretaMatrixRecordByCities($stores_matrix,$cities_calc_from_admin);
        $this->clearMatrixTable($stores_matrix);




        //ГОРОДА УНИКИ НУЖНО ДОБАВИТЬ К ПВЗ
        // $this->cretaMatrixRecordByCities($cities_calc_from_admin,$stores_matrix);
        //ПВЗ УНИКИ НУЖНО ДОБАВИТЬ К ГОРОДАМ ИЗ СПИСКА.
        //  $this->cretaMatrixRecordByCities($stores_for_calc,$cities_matrix);
        //СМОТРИМ ЧЕГО НЕТ В МАТРИЦЕ И СОЗДАЕМ УНИКАЛЬНЫЙ СПИСОК
        // $this->uniqueCitiesFromStoresAndAdmin();
        //$uniqueCities = $this->getuniqueCities();
        //fn_print_die($uniqueCities);
        //ПОТОМ ДЕЛАЕМ КРЕСТ НА КРЕСТ МЕЖДУ ДВУМЯ МАССИВАМ И ЗАПИСЫВАЕМ ВСЕ В ТАБЛИЦУ ДОСТАВКИ
    }

    /**
     * @param $cities_from  список городов СКЛАДОВ И МАГАЗИНОВ
     * @param $cities_to   список городоа со страницы городов в амдин панели, которые предварительно прошли проверку на наличие в таблице матрицы.
     */

    public static function getCityIds(){

    }

    public function clearMatrixTable($cities_from){

        //проверим магазины в списке ОТ
        //проверить города в списке К
        $City = new City($this->langCode);
        $Cities_to = $City->getRusCitiesAddedByadmin();

        $cities_from_ids = array();
        $cities_to_ids = array();

        foreach ($cities_from as $city_from){

            $cities_from_ids[$city_from['city_id']] = $city_from['city_id'];
        }


        foreach ($Cities_to as $city_to){

            $cities_to_ids[$city_to['city_id']] = $city_to['city_id'];
        }

        $delete_from_matrix_city_from = db_query("DELETE FROM ?:cp_matrix_data WHERE city_from_id NOT IN (?n)",$cities_from_ids);
        $delete_from_matrix_city_to = db_query("DELETE FROM ?:cp_matrix_data WHERE city_to_id NOT IN (?n)",$cities_to_ids);


    }

    public function cretaMatrixRecordByCities($cities_from,$cities_to){

        if(empty($cities_from) or empty($cities_to)){
            return false;
        }

        foreach ($cities_from as $cities_from_data){
            foreach ($cities_to as $cities_to_data) {

                if( empty($cities_from_data['city_id'])  or empty($cities_from_data['city_name']) or empty($cities_to_data['city_id'])  or empty($cities_to_data['city_name'])){
                    continue;
                }

                $check = db_get_field("SELECT city_from_id FROM ?:cp_matrix_data WHERE city_from_id =?i and city_to_id = ?i",$cities_from_data['city_id'],$cities_to_data['city_id']);

                if(!$check) {
                    $matrix_data = array();
                    $matrix_data['city_from_id'] = $cities_from_data['city_id'];
                    $matrix_data['city_to_id'] = $cities_to_data['city_id'];
                    $matrix_data['city_from'] = $cities_from_data['city_name'];
                    $matrix_data['city_to'] = $cities_to_data['city_name'];
                    $matrix_data['store_location_from_id'] = 0;
                    $matrix_data['time_from'] = 3;
                    $matrix_data['time_to'] = 3;
                    $matrix_data['time_average'] = 3;
                    $matrix_data['last_time_update'] = 0;
                    db_query('REPLACE INTO ?:cp_matrix_data ?e', $matrix_data);
                }

            }
        }
        /*
         *  CREATE TABLE IF NOT EXISTS ?:cp_matrix_data (
            city_from_id int(11) unsigned NOT NULL default '0',
            city_to_id int(11) unsigned NOT NULL default '0',
            city_from varchar(255)  NOT NULL default '0',
            city_to varchar(255)  NOT NULL default '0',
            store_location_from_id int(11) unsigned NOT NULL default '0',
            time_from int(11) unsigned NOT NULL default '0',
            time_to int(11) unsigned NOT NULL default '0',
            time_average int(11) unsigned NOT NULL default '0',
            last_time_update int(11) unsigned NOT NULL default '0',
            PRIMARY KEY (city_from_id,city_to_id),
            KEY last_time_update (last_time_update)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
         */
    }

    public  function uniqueCitiesFromStoresAndAdmin(){
        $cities_calc_from_admin = $this->getCitiesForCalculate();
        $stores_for_calc = $this->getStoresForCalculate();

        $uniqueCities = $this->uniqueCities();

        $uniqueCities = Matrix::createUniqueCitiesArray($uniqueCities,$cities_calc_from_admin);
        $uniqueCities = Matrix::createUniqueCitiesArray($uniqueCities,$stores_for_calc);

        $this->uniqueCities = $uniqueCities;


    }

    public static function createUniqueCitiesArray($uniqueCities,$cities){

    }

    public function findStoresForCalculate(Stores $stores){

        $stores_to_check_for_unique  = $stores->getstoresForCalculate(true,0);

        if(empty($stores_to_check_for_unique)){
            return array();
        }
        foreach ($stores_to_check_for_unique as $store) {
            if(empty($store['city_id'])){
                continue;
            }
            $res = db_get_field("SELECT * FROM ?:cp_matrix_data WHERE city_from_id =?i or city_to_id =?i ",$store['city_id'],$store['city_id']);
            if(empty($res)){
                $this->StoresForCalculate[] = array('city_name' =>$store['city_name'],'city_id'=>$store['city_id']);
            }
        }
    }

    public function findCitiesFromAdminForCalculate(City $city){

        $Cities = $city->getRusCitiesAddedByadmin();

        if(empty($Cities)){
            return array();
        }

        foreach ($Cities as $city) {
            // fn_Print_Die($city);
            $res = db_get_field("SELECT * FROM ?:cp_matrix_data WHERE city_from_id =?i or city_to_id =?i ",$city['city_id'],$city['city_id']);

            if(empty($res)){
                $this->CitiesForCalculate[] = array('city_name' =>$city['city_name'],'city_id'=>$city['city_id']);
            }
        }
    }




    /*
     * здесь поможем нитсраллеру проставить нужные данные.
     */

    public function updateWarehouseAmountsetCityId($store_location_id){

        $Stores = new Stores($this->langCode);
        $stores_data = $Stores->getstoresForCalculate(true,$store_location_id);

        if(!empty($stores_data)){
            foreach ($stores_data as $store) {
                if(!empty($store['city_id']) && !empty($store['city_name'])){
                    db_query("UPDATE ?:warehouses_products_amount SET city_id =?i WHERE warehouse_id =?i",$store['city_id'],$store['store_location_id']);
                }
            }
        }
    }


    public function findDeliveryForWarehouse($product,$user_city_id){

        if(empty($product['extra']['warehouse_id'])){
            return false;
        }

        $time_average  = db_get_field("SELECT cmd.time_average FROM ?:cp_matrix_data as cmd 
                                        INNER JOIN ?:warehouses_products_amount as wpa ON wpa.city_id = cmd.city_from_id
                                       WHERE wpa.warehouse_id =?i and cmd.city_to_id = ?i LIMIT 1",$product['extra']['warehouse_id'],$user_city_id);

        return $time_average;
    }

    public function recalculateDeliveryDate($products,$lang_code){

        if(empty($products)){
            return array();
        }

        foreach ($products as $key => $product) {


            $del_pre_summ =0;
            $del_pre_summ_count =0;

            if(!empty($product['extra_warehouse_data'])){
                foreach ($product['extra_warehouse_data'] as $war_id => $war_data) {

                    if(isset($war_data['time_average'])) {
                        $del_pre_summ += $war_data['time_average'];
                        $del_pre_summ_count++;
                    }
                }
            }

            if($del_pre_summ_count > 0){
                $days = round($del_pre_summ/$del_pre_summ_count);
                $products[$key]['delivery_data'] =fn_cp_np_generate_days_text(!empty($days) ? $days : 1, $lang_code);
            }
        }

        return $products;
    }
}
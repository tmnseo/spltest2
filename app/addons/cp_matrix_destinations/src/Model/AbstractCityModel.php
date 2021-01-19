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

namespace  Tygh\Addons\CpMatrixDestinations\Model;

use Tygh\Languages\Languages;
use Tygh\Addons\CpMatrixDestinations\Model;
use Tygh\Addons\CpMatrixDestinations\Service;


/**
 * @ignore
 */
class AbstractCityModel{
    protected $cityTable ='';
    protected $cityTableDescription ='';
    public  $langCode = 'ru';
    protected $isCityExistCheckEdost = true;
    protected $isCityExistCheck =true;
    
    
    public function getCityTable(){
        return $this->cityTable;
    }
    /*MANAGE CITIES DATA*/   /*extend function from abstract*/
    public function deleteCity($city_id){
        db_query("DELETE FROM ".$this->cityTable." WHERE city_id =?i",$city_id);
        db_query("DELETE FROM ".$this->cityTableDescription." WHERE city_id =?i",$city_id);
    }

    public function getCityName($city_id){
        return db_get_field("SELECT * FROM ".$this->cityTable." WHERE city_id =?i",$city_id);
    }


    public function updateCity($city_data, $city_id = 0, $lang_code = DESCR_SL){


        if(!isset($city_data['status'])) {
            $city_status = 'A';
        }
        //fn_print_die($city_data);
        if(!isset($city_data['city_name'])){
            $city_data['city_name']='';
        }


        $iso_code='';
        if(isset($city_data['state_code'])){
            $iso_code = $city_data['state_code'];
        }
        //проверяем на код едоста только если нужно для модели City/City.php
        if($this->isCityExistCheckEdost) {
            if (!CityHelper::checkCityForEdostCode($city_data['city_name'], $lang_code)) {
                Service::cmAddNotification(array('type' => "E", "title" => __("error"), 'text' => __('cp_matrix_city_not_in_edost')));
                $city_status = 'D';
            }
        }
        
        if(!isset($city_data['status'])) {
            $city_data['status'] = $city_status;
        }

        if (empty($city_id)) {
            if (!empty($city_data['city_name'])) {

                $check_exist = $this->isCityExist($city_data['city_name']);
                if($check_exist){
                    return $check_exist;
                }

                //сперва сделаем проверку на то, чтобы был код доставки
                $city_data['added_time'] = time();
                $city_data['city_id']  = db_query("REPLACE INTO ".$this->cityTable." ?e", $city_data);

                foreach (Languages::getAll() as $city_data['lang_code'] => $_v) {
                    db_query('REPLACE INTO '.$this->cityTableDescription.' ?e', $city_data);
                }
            }
        } else {
            db_query("UPDATE ".$this->cityTableDescription." SET ?u WHERE city_id = ?i AND lang_code = ?s", $city_data, $city_id, $lang_code);
        }
        return $city_id;
    }

    public  function isCityExist($name)
    {
        if(!$this->isCityExistCheck){
            
        }
        
        $id = db_get_field("SELECT city_id FROM ".$this->cityTableDescription." WHERE city_name =?s and lang_code =?s",$name,$this->langCode);
        if($id){
            return $id;
        }
        return false;
    }
    
    
    public function getCities($params,$items_per_page,$lang_code){

        $default_params = [
            'page'           => 1,
            'items_per_page' => $items_per_page,
        ];

        $params = array_merge($default_params, $params);

       // $fields = ['a.city_id', 'a.status', 'a.added_time', 'b.city_name'];
        $fields = ['a.*', 'b.city_name'];
        $joins = [
            'city_desc'   => db_quote('LEFT JOIN '.$this->cityTableDescription.' as b ON b.city_id = a.city_id AND b.lang_code = ?s', $lang_code),
        ];

        $condition = 'WHERE 1=1';


        /*
        if (!empty($params['only_avail'])) {
            $condition .= db_quote(' AND a.status = ?s', 'A');
        }
        */

        $sorting = 'ORDER BY  a.added_time';
        $limit = $group = '';
        if (!empty($params['items_per_page'])) {
            $params['total_items'] = db_get_field('SELECT count(*) FROM '.$this->cityTable.' as a ?p', $condition);
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

        $cities = db_get_array(
            'SELECT ' . implode(', ', $fields) . ' FROM '.$this->cityTable.' as a ?p ?p ?p ?p ?p',
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
        return array($cities, $params);

    }
}
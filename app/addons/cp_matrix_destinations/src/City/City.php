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

namespace Tygh\Addons\CpMatrixDestinations\City;

use Tygh\Addons\CpMatrixDestinations\Service;
use Tygh\Addons\CpMatrixDestinations\Model\AbstractCityModel;

use Tygh\Languages\Languages;

/**
 * Class FeaturePurposes
 *
 * @package Tygh\Addons\ProductVariations\Product
 */
class City extends AbstractCityModel
{

    protected $isCityExistCheckEdost = true;
    public  $isFoundedCitiesByAdmin = false;
    protected  $rusCities = array();
    public  $langCode = 'ru';

    protected $cityTable ='?:cp_matrix_cities';
    protected $cityTableDescription ='?:cp_matrix_cities_descriptions';

    public function __construct($lang_code='ru')
    {
        $this->langCode = $lang_code;
    }


    public  function isCityExist($name,$iso_code='')
    {
        if(!$this->isCityExistCheck){

        }

        $sql_iso ='';
        if(!empty($iso_code)){

            $sql_iso = db_quote("  AND ".$this->cityTable.".state_code");
        }


        $id = db_get_field("SELECT ".$this->cityTable.".city_id 
                            FROM ".$this->cityTableDescription." 
                            LEFT JOIN ".$this->cityTable." ON  ".$this->cityTableDescription.".city_id = ".$this->cityTable.".city_id
                             WHERE ".$this->cityTableDescription.".city_name =?s and ".$this->cityTableDescription.".lang_code =?s $sql_iso",$name,$this->langCode);
        if($id){
            return $id;
        }
        return false;
    }

    public static function checkGeoCity($city_name){
        
    }

    public function installDemoData(){

        $demo_cities = array();


        db_query("TRUNCATE ".$this->cityTable);

        db_query("TRUNCATE ".$this->cityTableDescription);


        $demo_cities["MOW"] ='Москва';
        $demo_cities['SPE'] ='Санкт-Петербург';
        $demo_cities['NVS'] ='Новосибирск';
        $demo_cities['SVE'] ='Екатеринбург';
        $demo_cities['TA'] ='Казань';
        $demo_cities['NIZ'] ='Нижний Новгород';
        $demo_cities['CHE'] ='Челябинск';
        $demo_cities['SAM'] ='Самара';
        $demo_cities['OMS'] ='Омск';
        $demo_cities['ROS'] ='Ростов-на-Дону';
        $demo_cities['BA'] ='Уфа';
        $demo_cities['KYA'] ='Красноярск';
        $demo_cities['VOR'] ='Воронеж';
        $demo_cities['PER'] ='Пермь';
        $demo_cities['VGG'] ='Волгоград';


        foreach ($demo_cities as $state_code => $dem_city) {
            $city_data = array();
            $city_data['city_name'] = $dem_city;
            $city_data['state_code'] = $state_code;
            $this->updateCity($city_data, 0, 'ru');
        }
    }


    public function findRusCitiesAddedByadmin($lang_code=''){

        if(empty($lang_code)){
            $lang_code = $this->langCode;
        }
        $result = db_get_array("SELECT cpcd.city_name, rcd.city_id 
                                FROM ".$this->cityTableDescription." as cpcd 
                                LEFT JOIN ".$this->cityTable." ON  cpcd.city_id = ".$this->cityTable.".city_id
                                LEFT JOIN ?:rus_city_descriptions as rcd ON rcd.city = cpcd.city_name 
                                 INNER JOIN ?:rus_cities as rc ON rc.city_id = rcd.city_id and rc.state_code = ".$this->cityTable.".state_code
                                LEFT JOIN ?:rus_edost_cities_link as recl ON recl.city_id = rcd.city_id
                                WHERE rcd.lang_code =?s  GROUP BY cpcd.city_name",$lang_code);

        $this->isFoundedCitiesByAdmin = true;
        $this->rusCities =$result;

        return $this->rusCities;
    }


    public function getRusCitiesAddedByadmin(){
        
        if(!$this->isFoundedCitiesByAdmin){
            $this->findRusCitiesAddedByadmin();
        }
        return $this->rusCities;
    }
    
    public function setCityExistCheck($value){

        if($value=== true or $value === false) {
            $this->isCityExistCheck = $value;
        }
        else{

        }
    }

    public  function isCityExistEdost($name)
    {
        return (db_get_row(""))?true:false;
    }
    
}
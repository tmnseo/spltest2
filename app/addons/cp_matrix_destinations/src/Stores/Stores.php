<?php

namespace Tygh\Addons\CpMatrixDestinations\Stores;
use Tygh\Addons\CpMatrixDestinations\City\City;
use Tygh\Languages\Languages;

/**
 * Class FeaturePurposes
 *
 * @package Tygh\Addons\ProductVariations\Product
 */
class Stores
{

    protected $storesForCalculate = array();
    public  $isFoundedCitiesByAdmin = false;
    protected $storesUnique = array();
    public  $langCode = 'ru';

    public function __construct($lang_code='ru')
    {
        $this->langCode = $lang_code;
    }
    public function getstoresUnique(){
        return $this->storesUnique;
    }
    
    public function getstoresForCalculate($get_unique=true,$store_location_id=0){
        
        if(!$this->isFoundedCitiesByAdmin) {
            $this->findStoresForCalculate($get_unique,$store_location_id);
            $this->isFoundedCitiesByAdmin = true;
        }
        return $this->storesForCalculate;
    }

    //достаем список уникальныъ городов из ПВЗ
    public function findStoresForCalculate($get_unique=true,$store_location_id=0){

        //store_location_descriptions
        //rus_city_descriptions
        $sql ='';
        if($store_location_id > 0){
            $sql .=db_quote("  and sl.store_location_id = ?i",$store_location_id);
        }
        
        
        if($get_unique){
            $sql .="  GROUP BY city_name";
        }
        
        $this->storesForCalculate = db_get_array("SELECT rcd.city as city_name, rcd.city_id ,sl.store_location_id  FROM ?:store_location_descriptions as sld 
                            LEFT JOIN ?:rus_city_descriptions as rcd ON rcd.city = sld.city 
                            LEFT JOIN ?:rus_cities as rc ON rc.city_id = rcd.city_id
                            LEFT JOIN ?:store_locations as sl ON sl.store_location_id = sld.store_location_id and sl.state = rc.state_code
                            INNER JOIN ?:rus_edost_cities_link as recl ON recl.city_id = rcd.city_id
                            WHERE sld.lang_code =?s and (sl.status ='A' or sl.status ='P') $sql",$this->langCode);

    }

    public function findStoresUnique(){

    }


    public function getAllStores(){

    }



   

}
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
/**
 * Created by PhpStorm.
 * User: lemursky
 * Date: 14.09.2020
 * Time: 23:10
 */
use Tygh\Addons\CpMatrixDestinations\ServiceProvider;

use Tygh\Registry;

class CityHelper{

    public static $lang_code ='ru';

    public static function getAllEdostCitiesByAlph(){
        return db_get_array("SELECT rcd.city_id,rcd.city, rc.state_code
                                FROM  ?:rus_city_descriptions as rcd 
                                LEFT JOIN ?:rus_cities as rc ON rc.city_id = rcd.city_id
                                INNER JOIN ?:rus_edost_cities_link as recl ON recl.city_id = rcd.city_id
                                WHERE rcd.lang_code =?s ",CART_LANGUAGE);
    }

    public static function checkCityForEdostCode($city_name,$lang_code='ru',$iso_code=''){

        if(empty($lang_code)){
            $lang_code = self::$lang_code;
        }

        if(empty($city_name)){
            return false;
        }

        $sql_add = '';
        if(!empty($iso_code)){
            $sql_add = db_quote("  AND rc.state_code =?s ",$iso_code);
        }

        $check = db_get_field("SELECT rcd.city_id
                                FROM  ?:rus_city_descriptions as rcd 
                                LEFT JOIN ?:rus_cities as rc ON rc.city_id = rcd.city_id
                                INNER JOIN ?:rus_edost_cities_link as recl ON recl.city_id = rcd.city_id
                                WHERE rcd.lang_code =?s  and  rcd.city =?s  $sql_add",$lang_code,$city_name);



        if(empty($check)){
            return false;
        }

        return $check;
    }

}
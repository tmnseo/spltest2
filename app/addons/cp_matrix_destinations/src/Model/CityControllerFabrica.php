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

class CityControllerFabrica{
    
    public static function CityPostUpdate (AbstractCityModel $City_model,$city_data,$city_id,$lang_code){

        $City_model->updateCity($city_data,$city_id, $lang_code);
    }

    public static function CityPostMassUpdate (AbstractCityModel $City_model,$city_data,$lang_code){

        foreach ($city_data as $city_id => $_data) {
            if (!empty($_data)) {
                $City_model->updateCity($_data, $city_id,$lang_code);
            }
        }
    }

    public static function CityPostMassDelete (AbstractCityModel $City_model,$city_ids){
        if (!empty($city_ids)) {
            foreach ($city_ids as $city_id) {
                $City_model->deleteCity($city_id);
            }
        }
    }
    
    public static function CityPostDelete(AbstractCityModel $City_model,$city_id){
        $City_model->deleteCity($city_id);
    }

    public static function CityGetManage (AbstractCityModel $City_model,$params,$items_per_page,$lang_code){
        $cities= array();
        $city_data_model = ServiceProvider::getPreCity();
        return $City_model->getCities($params, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);
        
    }
}
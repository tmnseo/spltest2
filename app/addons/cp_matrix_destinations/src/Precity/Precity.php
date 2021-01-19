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

namespace Tygh\Addons\CpMatrixDestinations\Precity;

use Tygh\Addons\CpMatrixDestinations\City\City;
use Tygh\Addons\CpMatrixDestinations\Stores\Stores;
use Tygh\Addons\CpMatrixDestinations\Edost\Edost;
use Tygh\Addons\CpMatrixDestinations\Model\AbstractCityModel;
use Tygh\Languages\Languages;


class Precity extends AbstractCityModel{
    protected $isCityExistCheckEdost = false;

    protected $cityTable ='?:cp_matrix_pre_cities';
    protected $cityTableDescription ='?:cp_matrix_pre_cities_descriptions';

    
    
    //public function deleteCity($city_id){
    
    //public function updateCity($city_data, $city_id = 0, $lang_code = DESCR_SL){
    
    //public  function isCityExist($name)
    
    //public function getCities($params,$items_per_page,$lang_code){
}
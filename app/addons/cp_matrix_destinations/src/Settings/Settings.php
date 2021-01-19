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

namespace Tygh\Addons\CpMatrixDestinations\Settings;

use Tygh\Registry;


use Tygh\Addons\CpMatrixDestinations\Service;
use Tygh\Addons\CpMatrixDestinations\ServiceProvider;
use Tygh\Addons\CpMatrixDestinations\Model\AbstractCityModel;
use Tygh\Addons\CpMatrixDestinations\City\City;
use Tygh\Addons\CpMatrixDestinations\Model\CityHelper;


class Settings{

    
    public static function getCpMatrixSettings(){
    
        
        
        return array('edost_id'=>Registry::get('addons.cp_matrix_destinations.edost_id'),
                     'edost_password'=>Registry::get('addons.cp_matrix_destinations.edost_password'),
                     'edost_weight'=>Registry::get('addons.cp_matrix_destinations.edost_weight'));
    }   
    
    public static function getRedeclareRelations(){
        $array_settings = array('cp_edost_counter'=>1);
        return $array_settings;
    }

    public static function redeclareIds($name_settings){

        $array_settings = self::getRedeclareRelations();

        if(isset($array_settings[$name_settings])){
            return $array_settings[$name_settings];
        }
        else{
            return 0;
        }
    }

    public static function incrementEdostCounter($id){

       
        if(empty($id)){
            return false;
        }
        $current_count = self::getSettingById($id);
        self::updateSettingById($current_count+1,$id);

    }

    public static function getSettingById($id){
        $id = self::redeclareIds($id);
        if(empty($id)){
            return false;
        }
        
        if(empty($id)){
            return false;
        }
        
        return db_get_field("SELECT `value` FROM ?:cp_matrix_settings WHERE settings_id =?i",$id);
    }

    public static function updateSettingById($value,$id){

        $id = self::redeclareIds($id);
        if(empty($id)){
            return false;
        }
        
        if(empty($id)){
            return false;
        }

        return db_query("UPDATE ?:cp_matrix_settings SET `value` =?s WHERE settings_id =?i",$value,$id);

    }

    public static function deleteSettingById($id){

        $id = self::redeclareIds($id);
        if(empty($id)){
            return false;
        }
        
        if(empty($id)){
            return false;
        }

        return db_query("DELETE FROM ?:cp_matrix_settings WHERE settings_id =?i",$id);

    }

    //settings_id
    //value
    public static function getSettings(){

        $array_settings = self::getRedeclareRelations();
        $array_settings =array_flip($array_settings);
        
        $settings =  db_get_array("SELECT * FROM ?:cp_matrix_settings");
        
        foreach ($settings as $key => $setting) {
            
            if(!empty($array_settings[$setting['settings_id']])){

                $settings[$key]['settings_id'] = $array_settings[$setting['settings_id']];
            }
        }
        
        return $settings;
    }
    
    
    public static function installSettingsData($settings_data = array()){

        $settings_data['settings_id'] = self::redeclareIds($settings_data['settings_id']);

        //fn_print_r($settings_data);
        
        if(!isset($settings_data['settings_id']) or !isset($settings_data['value'])){
            return false;
        }
        db_query("REPLACE INTO ?:cp_matrix_settings ?e", $settings_data);

    }
}

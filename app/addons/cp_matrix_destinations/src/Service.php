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

namespace Tygh\Addons\CpMatrixDestinations;



use Tygh\Addons\CpMatrixDestinations\Matrix\Matrix;
use Tygh\Addons\CpMatrixDestinations\City\City;
use Tygh\Addons\CpMatrixDestinations\Stores\Stores;
use Tygh\Addons\CpMatrixDestinations\Settings\Settings;

/**
 * Implements methods for working with a variation group
 *
 * @package Tygh\Addons\ProductVariations
 */
class Service
{
    protected $store_locations_for_calculate;

    protected $group_code_generator;
    
    protected $lang_code ='ru';


    protected  static $notifications =array();
    
    public static function cmGetNotifications(){

        return self::$notifications;
    }

    public static function cmAddNotification($notice_data){

        self::$notifications[] = $notice_data;
    }
    
    public static function cmShowNotifications(){


        foreach (self::$notifications as $notification) {
            fn_set_notification(
                $notification['type'],
                $notification['title'],
                $notification['text']
            );
        }

        self::$notifications = array();
      
    }
    
    public function __construct() {
      
    }

    public function rebuild(){

        $Matrix = new Matrix($this->lang_code);
        $City = new City($this->lang_code);
        $Stores = new Stores($this->lang_code);

        //получаем все текущие города, добавленные в даминке для просчета сроков доставки.
        $City->findRusCitiesAddedByadmin($this->lang_code);

        //$CitiesForCalculate = $Matrix->getCitiesForCalculate();
        
        // fn_print_r($City);
        //fn_print_die($CitiesForCalculate);
        $res = $Matrix->recalculateMatrixTable($City,$Stores);

        return true;

    }
    //смотрим по таблице матрицы и отправляем запрос в едост чтобы получить список тарифов и сохранить в отдельной таблицу
    public function recalculateMatrix(){
        $Matrix = new Matrix($this->lang_code);

        $Matrix->buildMatrixTarriffs('live')->updateMatrixTableByTariffs();
        
        return $Matrix;

    }
    
    
    public function installFirstData(){
        $res = db_get_row("SELECT * FROM ?:warehouses_products_amount LIMIT 1");
        if(!isset($res['city_id'])){
            db_query(" ALTER TABLE `?:warehouses_products_amount`
            ADD COLUMN `city_id` int(11) unsigned NOT NULL DEFAULT '0',
            ADD KEY `city_id` (`city_id`);");
        }

        $matrix_model = ServiceProvider::getMatrix();
        $matrix_model->updateWarehouseAmountsetCityId(0);

        $city_model = ServiceProvider::getCity();
        $city_model->installDemoData();


        $settings_data['settings_id'] ='cp_edost_counter';
        $settings_data['value'] = 0;
        Settings::installSettingsData($settings_data);
    }


    //на основе свежих тарифов мы
   // public function updateMatrixTableByTariffs(){

  //  }
    
}

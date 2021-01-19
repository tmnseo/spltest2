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

use Tygh\Addons\CpMatrixDestinations\ServiceProvider;
use Tygh\Addons\CpMatrixDestinations\Service;
use Tygh\Addons\CpMatrixDestinations\Model\CityControllerFabrica;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mode === 'update') {
        $City_model = ServiceProvider::getCity();
        CityControllerFabrica::CityPostUpdate($City_model,$_REQUEST['city_data'],$_REQUEST['city_id'], DESCR_SL);
       // $result = $service->updateCity($_REQUEST['city_data'],$_REQUEST['city_id'], DESCR_SL);
    }


    if ($mode == 'm_update') {

        $City_model = ServiceProvider::getCity();
        //AbstractCityModel $City_model,$city_data,$lang_code
        CityControllerFabrica::CityPostMassUpdate($City_model,$_REQUEST['city_data'],DESCR_SL);

        /*
        foreach ($_REQUEST['city_data'] as $city_id => $_data) {
            if (!empty($_data)) {
                $service = ServiceProvider::getCity();
                $service->updateCity($_data, $city_id,DESCR_SL);
            }
        }
        */
    }

    if ($mode === 'm_delete') {
        $City_model = ServiceProvider::getCity();
        //AbstractCityModel $City_model,$city_ids
        CityControllerFabrica::CityPostMassDelete($City_model,$_REQUEST['city_ids']);
        
        /*
        if (!empty($_REQUEST['city_ids'])) {
            foreach ($_REQUEST['city_ids'] as $city_id) {
                $service->deleteCity($city_id);
            }
        }
        */
    }

    if ($mode === 'delete') {
        $City_model = ServiceProvider::getCity();
        CityControllerFabrica::CityPostDelete($City_model,$_REQUEST['city_id']);
        // $result = $service->updateCity($_REQUEST['city_data'],$_REQUEST['city_id'], DESCR_SL);
    }

    Service::cmShowNotifications();
    return array(CONTROLLER_STATUS_OK, 'cp_city.manage');

}


if ($mode === 'manage') {

    $params = $_REQUEST;
    /** @var \Tygh\SmartyEngine\Core $view */
    $view = Tygh::$app['view'];

    /*
    $cities= array();
    $city_data_model = ServiceProvider::getCity();
    list($cities, $search) = $city_data_model->getCities($params, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);
    */
    $city_data_model = ServiceProvider::getCity();
    list($cities,$search) = CityControllerFabrica::CityGetManage($city_data_model,$params,Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);

    $params['country_code'] ='RU';
    list($states, $search) = fn_get_states($params, 0, DESCR_SL);

    $view->assign([
        'cities'        => $cities,
        'search'           => $search,
        'states' =>    $states
    ]);
}

if($mode == 'installDemoData'){
    $city_data_model = ServiceProvider::getCity();

    $city_data_model->installDemoData();

    exit('done');

}




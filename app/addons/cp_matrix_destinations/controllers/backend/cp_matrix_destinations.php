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
use Tygh\Registry;


defined('BOOTSTRAP') or die('Access denied');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($mode === 'update') {


        $service = ServiceProvider::getService();
    }
}


if($mode == 'startFullBuildMatrix'){
    $service = ServiceProvider::getService();
    $service->rebuild();
    $service->recalculateMatrix();
    
    exit('done');
}

if($mode == 'rebuild'){

    
   // db_query("TRUNCATE ?:cp_matrix_data");
    $service = ServiceProvider::getService();
    
    $rebuild = $service->rebuild();
    
    exit(
        'done'
    );
}

if($mode == 'recalculate'){

    $service = ServiceProvider::getService();

    $rebuild = $service->recalculateMatrix();

    exit('finish');
}


if ($mode =='install_city_id'){


    $res = db_get_row("SELECT * FROM ?:warehouses_products_amount LIMIT 1");
    if(!isset($res['city_id'])){
        db_query(" ALTER TABLE `?:warehouses_products_amount`
            ADD COLUMN `city_id` int(11) unsigned NOT NULL DEFAULT '0',
            ADD KEY `city_id` (`city_id`);");
    }
    $matrix_model = ServiceProvider::getMatrix();

    $matrix_model->updateWarehouseAmountsetCityId(0);

    exit('done');
}

if ($mode === 'manage') {
    $params = $_REQUEST;
    /** @var \Tygh\SmartyEngine\Core $view */
    $view = Tygh::$app['view'];
    $cities= array();
    $matrix_data_model = ServiceProvider::getMatrix();
    list($matrix_data, $search) = $matrix_data_model->getMatrixTable($params, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);

    $unique_ids = \Tygh\Addons\CpMatrixDestinations\Matrix\Matrix::sortUniqueCityIds($matrix_data);
    $state_codes = $matrix_data_model->getMatrixTableStateCodes($unique_ids);

    foreach ($matrix_data as $key => $item) {


        $matrix_data[$key]['city_from_state']='';
        $matrix_data[$key]['city_to_state']='';

        if(!empty($state_codes[$item['city_from_id']])){
            $matrix_data[$key]['city_from_state'] = $state_codes[$item['city_from_id']];
        }


        if(!empty($state_codes[$item['city_to_id']])){
            $matrix_data[$key]['city_to_state'] = $state_codes[$item['city_to_id']];
        }
    }


    /*
     *
     *  $group_repository = ServiceProvider::getGroupRepository();
    $product_repository = ServiceProvider::getProductRepository();

    $group = $group_repository->findGroupByProductId($product_id);
     */
    $view->assign([
        'matrix_data'        => $matrix_data,
        'search'           => $search,
        'state_codes' => $state_codes,
    ]);
}




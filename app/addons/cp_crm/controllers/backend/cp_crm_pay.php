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

use Tygh\Cpcrm\CrmPayments;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

if (in_array($mode,array('update','manage','update','delete','m_delete','m_update'))) {
    $CrmPayments = CrmPayments::getInstance();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mode === 'update') {
    }



    if ($mode == 'm_update') {

    }

    if ($mode === 'm_delete') {

    }

    if ($mode === 'delete') {
        CityControllerFabrica::CityPostDelete($City_model,$_REQUEST['city_id']);
        // $result = $service->updateCity($_REQUEST['city_data'],$_REQUEST['city_id'], DESCR_SL);
    }

    return array(CONTROLLER_STATUS_OK, 'cp_crm.manage');

}


if ($mode === 'manage') {

    $params = $_REQUEST;
    /** @var \Tygh\SmartyEngine\Core $view */
    $view = Tygh::$app['view'];

    list($payments,$search) = $CrmPayments->getPayments($params,Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);


    $view->assign([
        'payments'        => $payments,
        'search'           => $search,
        'table_name' =>  $CrmPayments->getTablename()
    ]);
}




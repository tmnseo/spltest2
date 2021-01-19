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

use Tygh\Registry;
use Tygh\Addons\CpMatrixDestinations\Geo\Geo;

defined('BOOTSTRAP') or die('Access denied');

/** @var string $mode */

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'confirm_location') {

        //$auth = & Tygh::$app['session']['auth'];        
        //$auth['cp_location_confirmed'] = !empty($_REQUEST['status']) ? $_REQUEST['status'] : true;
        fn_set_cookie('cp_location_confirmed', !empty($_REQUEST['status']) ? $_REQUEST['status'] : true, SESSION_ALIVE_TIME);

        $res_city_id = Tygh::$app['session']['cp_user_has_defined_city_id_global'];
        $was_show = Tygh::$app['session']['cp_user_not_edost_was_show'];

        if ($res_city_id && !$was_show) {
            Geo::$show_not_in_edost_notice = true;
            Tygh::$app['session']['cp_user_not_edost_was_show'] = true;
            Geo::setupCustomerLocation($res_city_id);
        }
        
        if (defined('AJAX_REQUEST')) {
            exit;
        }
    }

    return [CONTROLLER_STATUS_OK];
}
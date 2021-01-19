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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_cp_geo_maps_ext_get_geolocation_data_post(&$geolocation_data)
{
    //$auth = & Tygh::$app['session']['auth'];
    //$geolocation_data['cp_location_confirmed'] = !empty($auth['cp_location_confirmed']) ? true : false;
    $geolocation_data['cp_location_confirmed'] = !empty(fn_get_cookie('cp_location_confirmed')) ? true : false;
}

function fn_cp_geo_maps_ext_geo_maps_customer_location($location)
{
    if (!empty($location['state_code'])) {
        //$auth = & Tygh::$app['session']['auth'];        
        //$auth['cp_location_confirmed'] = true;
    }
}

function fn_cp_geo_is_geolocation_confirmed(){

    return !empty(fn_get_cookie('cp_location_confirmed')) ? true : false;
}
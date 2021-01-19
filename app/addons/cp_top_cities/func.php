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

function fn_cp_add_city_popularity($city_id)
{
    db_query("UPDATE ?:rus_cities SET cp_popularity = cp_popularity + 1 WHERE city_id = ?i", $city_id);
}
function fn_cp_get_top_cities()
{
    $top_cities_amount = Registry::get('addons.cp_top_cities.top_cities_amount');
    
    $top_cities = db_get_array("
        SELECT rc.city_id as rus_cities_city_id, mc.*, rcd.*  FROM ?:rus_cities as rc 
        INNER JOIN ?:cp_matrix_cities as mc ON rc.state_code = mc.state_code 
        INNER JOIN ?:rus_city_descriptions as rcd ON rcd.city_id = rc.city_id 
        INNER JOIN ?:cp_matrix_cities_descriptions as mcd ON mcd.city_id = mc.city_id
        WHERE mcd.city_name = rcd.city AND rcd.lang_code = ?s AND mcd.lang_code = ?s ORDER BY rc.cp_popularity DESC LIMIT ?i", CART_LANGUAGE, CART_LANGUAGE, $top_cities_amount);

    return $top_cities;
}
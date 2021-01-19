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

if ($mode == 'view') {
    if (!empty($_REQUEST['product_id'])) {


        $city_name = Tygh::$app['session']['cp_user_has_defined_city_name'];
        if($city_name){

        }
        else {


            $city_id = Tygh::$app['session']['cp_user_has_defined_city'];

            if ($city_id) {
                $city_name = db_get_field("SELECT city FROM ?:rus_city_descriptions WHERE city_id = ?i", $city_id);

                Tygh::$app['session']['cp_user_has_defined_city_name'] = $city_name;
            }
        }

        if($city_name){
            Registry::get('view')->assign('cp_matrix_city_name', $city_name);

        }
    }
}
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
use Tygh\Addons\CpMatrixDestinations\Model\CityHelper;

defined('BOOTSTRAP') or die('Access denied');



if ($mode =='update_steps' ){
    if(!empty($_REQUEST['user_data']['s_city']) && !empty($_REQUEST['user_data']['s_state'])){
        //$result_city_exist_id = Geo::checkGeoCity($_REQUEST['user_data']['s_city'],$_REQUEST['user_data']['s_state']);
        // if(!$result_city_exist_id) {
        //fn_set_notification('W', __('warning'), __('cp_geo_maxm_define_but_not_edost'));
        //}




        /*
        $city_id = db_get_field("SELECT rcd.city_id
                                FROM  ?:rus_city_descriptions as rcd 
                                LEFT JOIN ?:rus_cities as rc ON rc.city_id = rcd.city_id
                                
                                WHERE rcd.lang_code =?s  and  rcd.city =?s and rc.state_code =?s",CART_LANGUAGE,$_REQUEST['user_data']['s_city'],$_REQUEST['user_data']['s_state']);

        if(empty($city_id)){
            return false;
        }

        Geo::$show_not_in_edost_notice = true;
        Geo::setupCustomerLocation($city_id);
        */
    }
}


if($mode =='checkout'){

    $show = false;
    $res_city_id = Tygh::$app['session']['cp_user_has_defined_city_id_global'];
    if ($res_city_id ) {
        Geo::$show_not_in_edost_notice = false;
        Tygh::$app['session']['cp_user_not_edost_was_show'] = true;
        Geo::setupCustomerLocation($res_city_id);
        $ch = Tygh::$app['session']['cp_user_has_defined_city'];
        if(!$ch){
            $show = true;
            fn_set_notification('WW', __('warning'), __('cp_geo_maxm_define_but_not_edost'));
        }
    }
    if($show) {
        Tygh::$app['view']->assign('cp_show_notice', true);
        Tygh::$app['view']->assign('cp_popup_order_completed', true);
    }
    // $cart = &Tygh::$app['session']['cart'];
    // $location  = Geo::getCustomerLocation();
    //if(empty($cart['user_data']['s_city']) && empty($cart['user_data']['s_state'])){

    // }

}

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
use Tygh\Addons\CpMatrixDestinations\ServiceProvider;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == 'view') {
    if (!empty($_REQUEST['product_id']) && defined('AJAX_REQUEST')) {
        $product = Registry::get('view')->getTemplateVars('product');
        if (!empty($product)) {
            $new_met_data = array(
                'description' => $product['product'],
                'keywords' => !empty($product['meta_keywords']) ? $product['meta_keywords'] : '',
                'canonical' => fn_url('products.view?product_id=' . $product['product_id'])
            );
            Tygh::$app['ajax']->assign('cp_new_meta_data', $new_met_data);
        }
    }

    $params_get = array();
    $params_set = array();
    if(isset($_REQUEST['warehouse_id'])){
        $params_set['cp_cur_wh_id'] = $_REQUEST['warehouse_id'];

        $params_get['extra']['warehouse_id'] =  $_REQUEST['warehouse_id'];
    }
    if(isset($_REQUEST['features_hash'])){
        $params_set['features_hash'] = $_REQUEST['features_hash'];
    }
    if(isset($_REQUEST['product_id'])){
        $params_set['product_id'] = $_REQUEST['product_id'];
    }

    if(isset($_REQUEST['cp_matrix_filter_days'])){
        $params_set['cp_matrix_filter_days'] = $_REQUEST['cp_matrix_filter_days'];
    }



    //  $items['most_deliv'] = \Tygh\ProductPage\ProductPageDataHelper::runAction("getBestDelivery",$params_set,CART_LANGUAGE);


    $matrix_model = ServiceProvider::getMatrix();
    if(isset(Tygh::$app['session']['cp_user_has_defined_city'])) {
        $user_city_id = Tygh::$app['session']['cp_user_has_defined_city'];
    }
    else{
        $user_city_id = 0;
    }
    $date_delivery = $matrix_model->findDeliveryForWarehouse($params_get,$user_city_id);

    if($date_delivery === '0'){
        $date_delivery =1;
    }

    if($date_delivery !==false){
        $date_delivery = fn_cp_np_generate_days_text($date_delivery, CART_LANGUAGE);
    }




    /*
    if(isset($items['most_deliv']['cp_fastest_delivery'])){

        $cp_fastest_delivery = $items['most_deliv']['cp_fastest_delivery'];
    }
    else{
        $cp_fastest_delivery ='';
    }
    */


    Tygh::$app['view']->assign('cp_fastest_delivery_con', $date_delivery);

}
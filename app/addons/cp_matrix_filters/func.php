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
use Tygh\Enum\YesNo;

if (!defined('BOOTSTRAP')) { die('Access denied'); }


function fn_cp_matrix_filter_get_time_vars($params,$lang_code = CART_LANGUAGE){

    if(empty(Tygh::$app['session']['cp_user_has_defined_city'])){
        return false;
    }
    
    if(isset($params['cd'])){
        unset($params['cd']);
    }
   //if(isset($_REQUEST['cp_matrix_filter_days'])){
        //unset($_REQUEST['cp_matrix_filter_days']);
  // }
    $deliv_params = array();
    if (!empty(Tygh::$app['session']['cp_user_has_defined_city'])) {
        $deliv_params['cp_np_type'] = 'B';
        $deliv_params['sort_by'] = 'cp_np_weight';
    } else {
        $deliv_params['cp_np_type'] = 'C';
        $deliv_params['sort_by'] = 'cp_np_price';
    }
//     if (!empty($params['cp_np_this_product'])) {
//         unset($params['cp_np_this_product']);
//     }
    $manuf_code = fn_cp_np_getproduct_manuf_art($params['product_id']);
    $deliv_params['art_q'] = $manuf_code;
    $addon_settings = Registry::get('addons.cp_product_page');
    $deliv_params['cp_np_settigns'] = $addon_settings;

    /* gMelnikov modifs*/
    if (Registry::get('runtime.controller') == 'companies' && Registry::get('runtime.mode') == 'products' && !empty($_REQUEST['company_id'])) {
        
        $_pids = db_get_fields("SELECT product_id FROM ?:products WHERE company_id = ?i", $_REQUEST['company_id']);
        $_times = [];
        if (!empty($_pids)) {
            foreach ($_pids as $product_id) {
                $_times[]['time_average'] = db_get_fields("SELECT ?:cp_matrix_data.time_average FROM ?:warehouses_products_amount 
                LEFT JOIN ?:cp_matrix_data ON ?:cp_matrix_data.city_from_id = ?:warehouses_products_amount.city_id
                WHERE ?:warehouses_products_amount.product_id = ?i AND ?:cp_matrix_data.city_to_id = ?i", $product_id, Tygh::$app['session']['cp_user_has_defined_city']); 
            }   
        }
        /* gMelnikov modifs*/
    }else {
        list($most_deliv, $deliv_s) = fn_get_products($deliv_params, 0, $lang_code);
    }
    
    if(!empty($_REQUEST['cp_show_most_deliv'])){

        fn_print_r($most_deliv);
    }


    $times_average = array();
    if(!empty($most_deliv)){
        foreach ($most_deliv as $item) {
            if(!empty($item['extra_warehouse_data'])){
                foreach ($item['extra_warehouse_data'] as $item_warehouse) {
                    $times_average[$item_warehouse['time_average']] = $item_warehouse['time_average'];
                }
            }
        }
        /* gMelnikov modifs*/
    }elseif (!empty($_times)) {
        foreach ($_times as $wh_times) {
            if (!empty($wh_times['time_average'])) {
                foreach ($wh_times['time_average'] as $item) {
                    $times_average[$item] = $item;
                }
            }
        }
    }
    /* gMelnikov modifs*/

    if(empty($times_average)){
        return false;
    }

   $min = min($times_average);
    if($min ==0){
        $min=1;
    }
    $max = max($times_average);

    if($max ==0){
        $max =1;
    }
    $filters_value = array();
    $i = 1;
    for ($value = $min; $value <= $max; $value++) {
        $filters_value[$value] =$value;
    }

    $current_value = false;

    if(!empty(Tygh::$app['session']['cd'])){
        $current_value = Tygh::$app['session']['cd'];
    }

    if(isset($_REQUEST['cd'])){
        $current_value = $_REQUEST['cd'];

    }

    return array('min'=>$min,'max'=>$max,'values'=>$filters_value,'current_value'=>$current_value);

}


function fn_cp_matrix_filters_get_products($params, &$fields, &$sortings, &$condition, &$join, $sorting, &$group_by, $lang_code, $having)
{
    if(isset($params['cd'])){
        Tygh::$app['session']['cp_matrix_filter_days'] = $params['cd'];
        $condition .= db_quote(" AND ?:cp_matrix_data.time_average <= ?i", $params['cd']);
    }
    else{
        Tygh::$app['session']['cp_matrix_filter_days'] = false;
    }

}

function fn_cp_matrix_reset_cp_matrix_filter_days(){
    Tygh::$app['session']['cp_matrix_filter_days'] =false;
}
function fn_cp_matrix_filters_add_warehouses_extra_data_post(&$products, $product_id, &$product_data, $params, $lang_code)
{   

    if (AREA == 'C' && !empty($params['cd']) && Registry::get('runtime.controller') == 'companies' && Registry::get('runtime.mode') == 'products') {
        foreach ($product_data['extra_warehouse_data'] as $warehouse_id => $warehouses_data) {
            if (!empty($warehouses_data['time_average']) && $warehouses_data['time_average'] > $params['cd']) {
                unset($product_data['extra_warehouse_data'][$warehouse_id]);
            }
        }
    } 
}
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


namespace Tygh\ProductPage;

use   Tygh\Registry;

class ProductPageDataHelper{
    

    public static $cache_data = array();

    public static function runAction($method,$params,$lang_code){

        $params_cache = serialize($params);
        $params_cache = md5($params_cache);

        $static_cache  = self::$cache_data;
        
        if(isset($static_cache[$method.$params_cache])){
            return $static_cache[$method.$params_cache];
        }

        self::$cache_data[$method.$params_cache] = self::getBestDelivery($params,$lang_code);

        return self::$cache_data[$method.$params_cache];
    }
    
    public static function getParamsSettings(){
        
    }
    
    public static function getBestDelivery($params,$lang_code){


        $addon_settings = Registry::get('addons.cp_product_page');
        $params['cp_np_settigns'] = $addon_settings;


        $is_search = false;
        if (!empty($params['cp_np_search_run']) && !empty($params['q'])) {
            $is_search = true;
        }

        if (empty($is_search)) {
            $manuf_code = fn_cp_np_getproduct_manuf_art($params['product_id']);
            //$not_this_dis = array($params['product_id']);
            $skip_this_wh = array();
            $skip_combos = array();
        }

        if (!empty($is_search)) {
            $params['art_q'] = $params['q'];
        } else {
            $params['art_q'] = $manuf_code;
        }
        
        $deliv_params = $params;
        $deliv_params['page'] = 1;
        $deliv_params['cp_np_type'] = 'D';
        $deliv_params['sort_by'] = 'cp_np_delivery';
        $deliv_params['sort_order'] = 'asc';
        list($most_deliv, $deliv_s) = fn_get_products($deliv_params, 1, $lang_code);
        
        if (!empty($most_deliv)) {
            fn_gather_additional_products_data($most_deliv, array(
                'get_icon' => false,
                'get_detailed' => false,
                'get_additional' => false,
                'get_options' => true,
                'get_discounts' => true,
                'get_features' => true
            ));
            $most_deliv = reset($most_deliv);
            if (!empty($most_deliv['extra_warehouse_data'])) {
                $fastest_price = $fastest_wh_id = $fastest_delivery = 0;
                $fastest_city = '';
                foreach($most_deliv['extra_warehouse_data'] as $wh_key => $wh_data) {
                    if ($most_deliv['cp_wh_id'] == $wh_data['warehouse_id']) {
                        //                             if ($wh_data['amount'] > 0) {
                        //                                 if ((!empty($fastest_price) && isset($wh_data['time_average']) && $fastest_price > $wh_data['time_average']) || empty($fastest_price)) {
                        $fastest_price = $wh_data['price'];
                        $fastest_wh_id = $wh_data['warehouse_id'];
                        $fastest_delivery = !empty($wh_data['time_average']) ? $wh_data['time_average'] : 1;
                        $fastest_city = !empty($wh_data['warehouse_city']) ? $wh_data['warehouse_city'] : '';
                        //                                 }
                        //                             }
                        break;
                    }
                }
                if (!isset($skip_this_wh[$most_deliv['product_id']])) {
                    $skip_this_wh[$most_deliv['product_id']] = array();
                }
                $skip_this_wh[$most_deliv['product_id']][] = $fastest_wh_id;
                $skip_combos[] = $most_deliv['product_id'] . '-' . $fastest_wh_id;
                $most_deliv['cp_fastest_price'] = $fastest_price;
                $most_deliv['cp_fast_warehouse_id'] = $fastest_wh_id;
                $most_deliv['cp_fastest_delivery'] = fn_cp_np_generate_days_text($fastest_delivery, $lang_code);
                $most_deliv['cp_fastest_city'] = $fastest_city;
                //$not_this_dis[] = $most_deliv['product_id'];
                //$items['most_deliv'] = $most_deliv;
            }
        }
        /* gMelnikov to exclude identical products from the best offer block */
        if (!empty($most_deliv)) {
            
            $most_deliv_p_id = !empty($most_deliv['product_id']) ? $most_deliv['product_id'] : null;
            $most_deliv_wh_id = !empty($most_deliv['cp_fast_warehouse_id']) ? $most_deliv['cp_fast_warehouse_id'] : null;
            
            if (!empty($params['cp_cur_wh_id']) && !empty($params['product_id'])) {
                
                if ($params['product_id'] == $most_deliv_p_id && $params['cp_cur_wh_id'] == $most_deliv_wh_id) {
                    unset($most_deliv);
                }
            }
        }
        /* gMelnikov to exclude identical products from the best offer block */

        return $most_deliv;
    }



    public static function getBestPrice(){

    }


    public static function getOtherOffers(){

    }
}

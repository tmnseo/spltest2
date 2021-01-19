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

//HOOKS

function fn_cp_product_page_cp_get_products_set_keys($products, $params, $lang_code, $group_by, &$cp_field)
{
    if (AREA == 'C' && !empty($group_by) && $group_by == 'cp_key_combo') {
        $cp_field = 'cp_key_combo';
    }
}

function fn_cp_product_page_get_products($params, &$fields, &$sortings, &$condition, &$join, $sorting, &$group_by, $lang_code, $having)
{
    $is_details_page = Registry::get('cp_np_is_product_details');
    if (!empty($params) && !empty($params['cp_np_type'])) {
        $price_coeffv = $deliv_coeffv = 1;
        $fields['cp_key_combo'] = 'CONCAT(products.product_id, "-", cp_wh_am.warehouse_id) as cp_key_combo';

        $fields['cp_wh_id'] = 'cp_wh_am.warehouse_id as cp_wh_id';
        $group_by = 'cp_key_combo';

        //$join .= db_quote(' LEFT JOIN ?:warehouses_products_amount as cp_wh_am ON CONCAT(cp_wh_am.product_id, "-", cp_wh_am.warehouse_id) = CONCAT(products.product_id, "-", cp_wh_am.warehouse_id)');
        $join .= db_quote(' INNER JOIN ?:warehouses_products_amount AS cp_wh_am ON cp_wh_am.product_id = products.product_id');

        if (!empty($params['cp_np_settigns']) && !empty($params['cp_np_settigns']['price_weight']) && $params['cp_np_settigns']['price_weight'] > 0) {
            $price_coeffv = $params['cp_np_settigns']['price_weight'];
        }
        if (!empty($params['cp_np_settigns']) && !empty($params['cp_np_settigns']['delivery_weight']) && $params['cp_np_settigns']['delivery_weight'] > 0) {
            $deliv_coeffv = $params['cp_np_settigns']['delivery_weight'];
        }
        $condition .= db_quote(" AND cp_wh_am.price > ?i AND cp_wh_am.amount > ?i", 0, 0);
        if ($params['cp_np_type'] == 'C') { //cheapest

            if (Registry::get('addons.cp_matrix_destinations.status') == 'A') {
                $join .= db_quote(' LEFT JOIN ?:cp_matrix_data ON ?:cp_matrix_data.city_from_id = cp_wh_am.city_id');

                if (!empty(Tygh::$app['session']['cp_user_has_defined_city'])) {
                    $condition .= db_quote(" AND ?:cp_matrix_data.city_to_id = ?i", Tygh::$app['session']['cp_user_has_defined_city']);
                }
            }
            $fields['cp_np_price'] = 'cp_wh_am.price*' . $price_coeffv . ' as cp_np_price';
            $sortings['cp_np_price'] = 'cp_np_price';
        } elseif ($params['cp_np_type'] == 'D') { // fastest
            if (Registry::get('addons.cp_matrix_destinations.status') == 'A') {
                $join .= db_quote(' LEFT JOIN ?:cp_matrix_data ON ?:cp_matrix_data.city_from_id = cp_wh_am.city_id');
                $fields['cp_np_delivery'] = '(?:cp_matrix_data.time_average + 1)*' . $deliv_coeffv . ' as cp_np_delivery';
                $sortings['cp_np_delivery'] = 'cp_np_delivery';
                if (!empty(Tygh::$app['session']['cp_user_has_defined_city'])) {
                    $condition .= db_quote(" AND ?:cp_matrix_data.city_to_id = ?i", Tygh::$app['session']['cp_user_has_defined_city']);
                }
            }
        } elseif ($params['cp_np_type'] == 'B') {
            if (Registry::get('addons.cp_matrix_destinations.status') == 'A') {
                $join .= db_quote(' LEFT JOIN ?:cp_matrix_data ON ?:cp_matrix_data.city_from_id = cp_wh_am.city_id');
                $fields['cp_np_weight'] = 'cp_wh_am.price*' . $price_coeffv . '*(?:cp_matrix_data.time_average + 1)*' . $deliv_coeffv . ' as cp_np_weight';
                if (!empty(Tygh::$app['session']['cp_user_has_defined_city'])) {
                    $condition .= db_quote(" AND ?:cp_matrix_data.city_to_id = ?i", Tygh::$app['session']['cp_user_has_defined_city']);
                }
            } else {
                $fields['cp_np_weight'] = 'cp_wh_am.price*' . $price_coeffv . ' as cp_np_weight';
            }
            if (!empty($params['cp_np_this_product_id'])) {
                $condition .= db_quote(" AND products.product_id NOT IN (?n)", $params['cp_np_this_product_id']);
            }
            $sortings['cp_np_weight'] = 'cp_np_weight';

        } elseif ($params['cp_np_type'] == 'A') { //amount sorting
            $fields['cp_np_amount'] = 'cp_wh_am.amount as cp_np_amount';
            if (Registry::get('addons.cp_matrix_destinations.status') == 'A') {
                $join .= db_quote(' LEFT JOIN ?:cp_matrix_data ON ?:cp_matrix_data.city_from_id = cp_wh_am.city_id');

                if (!empty(Tygh::$app['session']['cp_user_has_defined_city'])) {
                    $condition .= db_quote(" AND ?:cp_matrix_data.city_to_id = ?i", Tygh::$app['session']['cp_user_has_defined_city']);
                }
            }
            $sortings['cp_np_amount'] = 'cp_np_amount';
        }
        if (!empty($params['cp_skip_combos'])) {

            $condition .= db_quote(' AND CONCAT(products.product_id, "-", cp_wh_am.warehouse_id) NOT IN (?a)', $params['cp_skip_combos']);
        }
    } elseif (!empty($is_details_page)) {
        $join .= db_quote(' LEFT JOIN ?:warehouses_products_amount as cp_wh_am ON cp_wh_am.product_id = products.product_id');

        if (Registry::get('addons.cp_matrix_destinations.status') == 'A') {
            $join .= db_quote(' LEFT JOIN ?:cp_matrix_data ON ?:cp_matrix_data.city_from_id = cp_wh_am.city_id');
            if (!empty(Tygh::$app['session']['cp_user_has_defined_city'])) {
                $condition .= db_quote(" AND ?:cp_matrix_data.city_to_id = ?i", Tygh::$app['session']['cp_user_has_defined_city']);
            }
        }
        $condition .= db_quote(" AND cp_wh_am.price > ?i AND cp_wh_am.amount > ?i", 0, 0);
    }
}

function fn_cp_product_page_get_filters_products_count_pre(&$params, &$cache_params, $cache_tables)
{
    $is_details_page = Registry::get('cp_np_is_product_details');
    if (!empty($is_details_page)) {
        $cache_params[] = 'cp_np_prod_id';
        $params['check_location'] = false;
        if (isset($params['company_id'])) {
            unset($params['company_id']);
        }
        if (!empty($params['cp_np_prod_id'])) {
            $manuf_code = fn_cp_np_getproduct_manuf_art($params['cp_np_prod_id']);
            if (!empty($manuf_code)) {
                $params['art_q'] = $manuf_code;
            }
        }
    }
}

//FUNCTIONS

function fn_cp_np_getproduct_manuf_art($product_id, $lang_code = CART_LANGUAGE)
{
    $manuf_code = '';
    if (!empty($product_id)) {
        $search_in_id = Registry::get('addons.cp_catalog_changes.original_article');
        if (!empty($search_in_id)) {
            $manuf_code = db_get_field("SELECT ?:product_feature_variant_descriptions.variant FROM ?:product_features_values
                LEFT JOIN ?:product_feature_variant_descriptions ON ?:product_feature_variant_descriptions.variant_id = ?:product_features_values.variant_id AND ?:product_feature_variant_descriptions.lang_code = ?s
                WHERE ?:product_features_values.product_id = ?i AND ?:product_features_values.feature_id = ?i", $lang_code, $product_id, $search_in_id
            );
        }
    }
    return $manuf_code;
}

function fn_cp_np_get_cur_product_best_wh($product_id)
{
    $best_wh_id = 0;
    if (!empty($product_id)) {
        $best_wh_id = db_get_field("SELECT warehouse_id FROM ?:warehouses_products_amount WHERE product_id = ?i AND amount > ?i ORDER BY price ASC", $product_id, 0);
    }
    return $best_wh_id;
}

function fn_cp_np_get_mosts_products($params, $get_best = false, $skip_blocks = false, $lang_code = CART_LANGUAGE)
{
    $items = $most_cheap = $most_deliv = $others = array();
    $is_search = false;
    if (!empty($params['cp_np_search_run']) && !empty($params['q'])) {
        $is_search = true;
    }
    if (!empty($params) && (!empty($params['product_id']) || !empty($is_search))) {

        if (empty($is_search)) {
            $manuf_code = fn_cp_np_getproduct_manuf_art($params['product_id']);
            //$not_this_dis = array($params['product_id']);
            $skip_this_wh = array();
            $skip_combos = array();
        }
        if (!empty($manuf_code) || !empty($is_search)) {
            //$params['cp_np_this_product_id'] = array($params['product_id']);
            $get_delivery = false;
            if (!empty(Tygh::$app['session']['cp_user_has_defined_city'])) {
                $get_delivery = true;
            }
            if (!empty($params['cp_cur_wh_id'])) {
                $skip_combos[] = $params['product_id'] . '-' . $params['cp_cur_wh_id'];
            }
            unset($params['product_id']);
            $addon_settings = Registry::get('addons.cp_product_page');
            $params['cp_np_settigns'] = $addon_settings;
            if (!empty($is_search)) {
                $params['art_q'] = $params['q'];
            } else {
                $params['art_q'] = $manuf_code;
            }
            if (!empty($get_best)) {
                $best_params = $params;
                $best_params['page'] = 1;
                $best_params['cp_np_type'] = 'B';
                $best_params['sort_by'] = 'cp_np_weight';
                $best_params['sort_order'] = 'asc';
                list($best_offer, $best_s) = fn_get_products($best_params, 1, $lang_code);
                if (!empty($best_offer)) {
                    $price_coeffv = $deliv_coeffv = 1;
                    $best_wh_id = 0;
                    $best_offer = reset($best_offer);
                    if (!empty($addon_settings) && !empty($addon_settings['price_weight']) && $addon_settings['price_weight'] > 0) {
                        $price_coeffv = $addon_settings['price_weight'];
                    }
                    if (!empty($addon_settings) && !empty($addon_settings['delivery_weight']) && $addon_settings['delivery_weight'] > 0) {
                        $deliv_coeffv = $addon_settings['delivery_weight'];
                    }
                    $best_weight = 0;
                    foreach($best_offer['extra_warehouse_data'] as $wh_key => $wh_data) {
                        if ($wh_data['amount'] > 0) {
                            $delivery = isset($wh_data['time_average']) ? $wh_data['time_average'] + 1 : 1;
                            $wh_weight = $wh_data['price']*$price_coeffv * $delivery*$deliv_coeffv;
                            if ((!empty($best_weight) && $best_weight > $wh_weight) || empty($best_weight)) {
                                $best_weight = $wh_weight;
                                $best_wh_id = $wh_data['warehouse_id'];
                            }
                        }
                    }
                    return array($best_offer['product_id'], $best_wh_id);
                } else {
                    return array(0,0);
                }
            }
            if (empty($skip_blocks)) {
                $cheap_params = $params;
                $cheap_params['page'] = 1;
                $cheap_params['cp_np_type'] = 'C';
                $cheap_params['sort_by'] = 'cp_np_price';
                $cheap_params['sort_order'] = 'asc';
                list($most_cheap, $most_s) = fn_get_products($cheap_params, 1, $lang_code);

                if (!empty($most_cheap)) {
                    fn_gather_additional_products_data($most_cheap, array(
                        'get_icon' => false,
                        'get_detailed' => false,
                        'get_additional' => false,
                        'get_options' => true,
                        'get_discounts' => true,
                        'get_features' => true
                    ));
                    $most_cheap = reset($most_cheap);
                    if (!empty($most_cheap['extra_warehouse_data'])) {
                        $lowest_price = $lowest_wh_id = $lowest_delivery = 0;
                        $lowest_city = '';
                        foreach($most_cheap['extra_warehouse_data'] as $wh_key => $wh_data) {
                            if ($most_cheap['cp_wh_id'] == $wh_data['warehouse_id']) {
                                //if ($wh_data['amount'] > 0) {
                                // if ((!empty($lowest_price) && $lowest_price > $wh_data['price']) || empty($lowest_price)) {
                                $lowest_price = $wh_data['price'];
                                $lowest_wh_id = $wh_data['warehouse_id'];
                                $lowest_delivery = !empty($wh_data['time_average']) ? $wh_data['time_average'] : 1;
                                $lowest_city = !empty($wh_data['warehouse_city']) ? $wh_data['warehouse_city'] : '';
                                //}
                                //}
                                break;
                            }
                        }
                        if (!isset($skip_this_wh[$most_cheap['product_id']])) {
                            $skip_this_wh[$most_cheap['product_id']] = array();
                        }
                        $skip_this_wh[$most_cheap['product_id']][] = $lowest_wh_id;
                        $skip_combos[] = $most_cheap['product_id'] . '-' . $lowest_wh_id;
                        $most_cheap['cp_lowest_price'] = $lowest_price;
                        $most_cheap['cp_lowest_warehouse_id'] = $lowest_wh_id;
                        $most_cheap['cp_lowest_delivery'] = fn_cp_np_generate_days_text($lowest_delivery, $lang_code);
                        $most_cheap['cp_lowest_city'] = $lowest_city;
                        //$not_this_dis[] = $most_cheap['product_id'];
                        /* gMelnikov to exclude identical products from the best offer block */
                        if (!empty($most_cheap)) {

                            $most_cheap_p_id = !empty($most_cheap['product_id']) ? $most_cheap['product_id'] : null;
                            $most_cheap_wh_id = !empty($most_cheap['cp_lowest_warehouse_id']) ? $most_cheap['cp_lowest_warehouse_id'] : null;
                            if (!empty($skip_combos)) {

                                if (($most_cheap_p_id.'-'.$most_cheap_wh_id) == current($skip_combos)) {
                                    unset($most_cheap);
                                }
                            }
                        }
                        /* gMelnikov to exclude identical products from the best offer block */
                        if (!empty($most_cheap)) {
                            $items['most_cheap'] = $most_cheap;
                        }
                    }
                }

                if (!empty($get_delivery)) {

                    $params_set = array();
                    if(isset($params['cp_cur_wh_id'])){
                        $params_set['cp_cur_wh_id'] = $params['cp_cur_wh_id'];
                    }
                    if(isset($params['features_hash'])){
                        $params_set['features_hash'] = $params['features_hash'];
                    }
                    if(isset($_REQUEST['product_id'])){
                        $params_set['product_id'] = $_REQUEST['product_id'];
                    }
                    if(isset($params['cd'])){
                        $params_set['cd'] = $params['cd'];
                    }

                    $params_set['cp_np_settigns'] = $params['cp_np_settigns'];
                    $params_set['art_q']  = $params['art_q'];

                    $items['most_deliv'] = \Tygh\ProductPage\ProductPageDataHelper::runAction("getBestDelivery",$params_set,$lang_code);
                    if (!empty($items['most_deliv']) && !empty($items['most_deliv']['extra_warehouse_data'])) {
                        $fastest_price = $fastest_wh_id = $fastest_delivery = 0;
                        $fastest_city = '';
                        foreach($items['most_deliv']['extra_warehouse_data'] as $wh_key => $wh_data) {
                            if ($items['most_deliv']['cp_wh_id'] == $wh_data['warehouse_id']) {
                                $fastest_price = $wh_data['price'];
                                $fastest_wh_id = $wh_data['warehouse_id'];
                                $fastest_delivery = !empty($wh_data['time_average']) ? $wh_data['time_average'] : 1;
                                $fastest_city = !empty($wh_data['warehouse_city']) ? $wh_data['warehouse_city'] : '';
                                break;
                            }
                        }
                        if (!isset($skip_this_wh[$items['most_deliv']['product_id']])) {
                            $skip_this_wh[$items['most_deliv']['product_id']] = array();
                        }
                        $skip_this_wh[$items['most_deliv']['product_id']][] = $fastest_wh_id;
                        $skip_combos[] = $items['most_deliv']['product_id'] . '-' . $fastest_wh_id;
                    }
                    /*
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
                            $items['most_deliv'] = $most_deliv;
                        }
                    }
                    */
                }
                $others_params = $params;
                //$others_params['cp_np_this_product_id'] = array_unique($not_this_dis);
                if (!empty($params['cp_np_sort_by']) && !empty($params['cp_np_sorting_run'])) {
                    $others_params['sort_by'] = $params['cp_np_sort_by'];
                    if (!empty($params['warehouse_id'])) {
                        $skip_combos[] = $params['cp_current_prod_id'] . '-' . $params['warehouse_id'];
                    }
                } else {
                    $others_params['cp_np_type'] = 'B';
                    $others_params['sort_by'] = 'cp_np_weight';
                    $others_params['sort_order'] = 'asc';
                }
                if (!empty($params['cp_np_pagination']) && !empty($params['warehouse_id'])) {
                    $skip_combos[] = $params['cp_current_prod_id'] . '-' . $params['warehouse_id'];
                }
                $others_params['cp_skip_combos'] = $skip_combos;
                $others_params['load_products_extra_data'] = false;
                if (defined('CP_NP_OTHER_IPP')) {
                    $o_ipp = CP_NP_OTHER_IPP;
                } else {
                    $o_ipp = 10;
                }
                list($others, $others_s) = fn_get_products($others_params, $o_ipp, $lang_code);
                if (!empty($others)) {
                    fn_gather_additional_products_data($others, array(
                        'get_icon' => false,
                        'get_detailed' => false,
                        'get_additional' => false,
                        'get_options' => true,
                        'get_discounts' => true,
                        'get_features' => true
                    ));
                    if (!empty($skip_this_wh) || !empty($params['cp_np_sorting_run']) || true) {
                        $other_list_order = array();
                        foreach($others as $o_key=> &$o_data) {
                            if (!empty($o_data['extra_warehouse_data'])) {

                                foreach($o_data['extra_warehouse_data'] as $owh_key => $owh_data) {
                                    if ($o_data['cp_wh_id'] == $owh_data['warehouse_id']) {
                                        $o_data['extra_warehouse_data'][$owh_key]['cp_delivery'] = fn_cp_np_generate_days_text(!empty($owh_data['time_average']) ? $owh_data['time_average'] : 1, $lang_code);
                                        $other_list_order[$o_data['product_id'] . '_' . $owh_data['warehouse_id']] = $owh_data;
                                    } else {
                                        unset($o_data['extra_warehouse_data'][$owh_key]);
                                    }
                                }
                                if (empty($o_data['extra_warehouse_data'])) {
                                    unset($others[$o_key]);
                                }
                            }
                        }
                        // for sortings
                        if (!empty($params['cp_np_sorting_run']) && !empty($other_list_order)) {
                            $sort_field = 'price';
                            if ($others_s['cp_np_type'] == 'C') {
                                $sort_field = 'price';
                            } elseif ($others_s['cp_np_type'] == 'A') {
                                $sort_field = 'amount';
                            } elseif ($others_s['cp_np_type'] == 'D') {
                                $sort_field = 'time_average';
                            }
                            $sort_func = 'fn_cp_np_sorting_' . $sort_field . '_' . $others_s['sort_order'];
                            if (function_exists($sort_func)) {
                                usort($other_list_order, $sort_func);
                            }
                        }
                    }
                    $items['from_others'] = $others;
                    $items['o_search'] = $others_s;
                }
            }
        }
    }
    if (!empty($params['cp_np_sorting_run'])) {
        return array($items, $others_s, $other_list_order);
    } else {
        return array($items);
    }
}

function fn_cp_np_generate_days_text($days, $lang_code = CART_LANGUAGE)
{
    $txt = $days;
    if (!empty($days)) {
        $last_char = substr($days, -1);
        if ($lang_code == 'ru') {
            if ($last_char == 1 && substr($txt, -2) != 11) {
                $use_lang = __('cp_np_day_txt');
            } elseif (in_array($last_char, array(2,3,4)) && substr($txt, -2, 1) != 1) {
                $use_lang = __('cp_np_days_txt');
            } else {
                $use_lang = __('cp_np_days2_txt');
            }
        } else {
            if ($txt == 1) {
                $use_lang = __('cp_np_day_txt');
            } else {
                $use_lang = __('cp_np_days_txt');
            }
        }
        $txt .= ' ' . $use_lang;
    }
    return $txt;
}

// sortings
function fn_cp_np_sorting_price_desc($a, $b)
{
    if ($a['price'] == $b['price']) {
        return 0;
    }
    return ($a['price'] < $b['price']) ? 1 : -1;
}

function fn_cp_np_sorting_price_asc($a, $b)
{
    if ($a['price'] == $b['price']) {
        return 0;
    }
    return ($a['price'] < $b['price']) ? -1 : 1;
}

function fn_cp_np_sorting_amount_desc($a, $b)
{
    if ($a['amount'] == $b['amount']) {
        return 0;
    }
    return ($a['amount'] < $b['amount']) ? 1 : -1;
}

function fn_cp_np_sorting_amount_asc($a, $b)
{
    if ($a['amount'] == $b['amount']) {
        return 0;
    }
    return ($a['amount'] < $b['amount']) ? -1 : 1;
}

function fn_cp_np_sorting_delivery_desc($a, $b)
{
    if ($a['delivery'] == $b['delivery']) {
        return 0;
    }
    return ($a['delivery'] < $b['delivery']) ? 1 : -1;
}

function fn_cp_np_sorting_delivery_asc($a, $b)
{
    if ($a['delivery'] == $b['delivery']) {
        return 0;
    }
    return ($a['delivery'] < $b['delivery']) ? -1 : 1;
}
//

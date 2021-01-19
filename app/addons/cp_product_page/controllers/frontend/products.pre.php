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

if ($mode == 'search') {
    // switch to best offer from search
    $params = $_REQUEST;
    $params['cp_np_search_run'] = true;
    list($new_product_id, $best_wh_id) = fn_cp_np_get_mosts_products($params, true, true);
    if (!empty($new_product_id)) {
        $switch_url = 'products.view?product_id=' . $new_product_id;
        if (!empty($best_wh_id)) {
            $switch_url .= '&warehouse_id=' . $best_wh_id;
        }
        return array(CONTROLLER_STATUS_REDIRECT, $switch_url);
    }
}
if ($mode == 'view') {
    if (!empty($_REQUEST['product_id'])) {
        $manuf_code = fn_cp_np_getproduct_manuf_art($_REQUEST['product_id']);
        if (!empty($manuf_code)) {
    // save/get features_hase n session
            if (defined('AJAX_REQUEST') && !empty($_REQUEST['cp_is_filter_run'])) {
                $feaures_hash = isset($_REQUEST['features_hash']) ? $_REQUEST['features_hash'] : '';
                if (!isset(Tygh::$app['session']['cp_np_saved_hashes'])) {
                    Tygh::$app['session']['cp_np_saved_hashes'] = array();
                }
                Tygh::$app['session']['cp_np_saved_hashes'][$manuf_code] = $feaures_hash;
                exit;
            } elseif (isset(Tygh::$app['session']['cp_np_saved_hashes'][$manuf_code])) {
                $_GET['features_hash'] = $_REQUEST['features_hash'] = Tygh::$app['session']['cp_np_saved_hashes'][$manuf_code];
            }
    // fix fro click on product links on page and refresh this products 
            if (defined('AJAX_REQUEST')) {
                if (!isset(Tygh::$app['session']['cp_np_switches'])) {
                    Tygh::$app['session']['cp_np_switches'] = array();
                }
                if (!empty($_REQUEST['cp_np_this_product'])) {
                    Tygh::$app['session']['cp_np_switches'][$manuf_code] = $_REQUEST['product_id'];
                } elseif (isset(Tygh::$app['session']['cp_np_switches'][$manuf_code])) {
                    unset(Tygh::$app['session']['cp_np_switches'][$manuf_code]);
                }
            } elseif (!empty(Tygh::$app['session']['cp_np_switches']) && !empty(Tygh::$app['session']['cp_np_switches'][$manuf_code])) {
                $_GET['cp_np_this_product'] = $_REQUEST['cp_np_this_product'] = Tygh::$app['session']['cp_np_switches'][$manuf_code];
            }
    //
        }
        $params = $_REQUEST;
        list($new_product_id, $best_wh_id) = fn_cp_np_get_mosts_products($params, true, true);
        Tygh::$app['view']->assign('cp_block_bo_prod_id', $new_product_id);
        Tygh::$app['view']->assign('cp_block_bo_wh_id', $best_wh_id);
        
        Registry::set('cp_np_is_product_details', true);
        if ((!empty($params['cp_np_sorting_run']) || !empty($params['cp_np_pagination'])) && defined('AJAX_REQUEST')) {
            
            if (!empty($params['cp_np_sorting_run'])) {
                if (!isset(Tygh::$app['session']['cp_np_saved_sortings'])) {
                    Tygh::$app['session']['cp_np_saved_sortings'] = array();
                }
                Tygh::$app['session']['cp_np_saved_sortings'][$manuf_code] = array(
                    'cp_np_sort_by' => $params['cp_np_sort_by'],
                    'sort_order' => $params['sort_order']
                );
            } elseif (!empty($params['cp_np_pagination'])) {
                $params['cp_np_sorting_run'] = true;
                if (isset(Tygh::$app['session']['cp_np_saved_sortings']) && !empty(Tygh::$app['session']['cp_np_saved_sortings'][$manuf_code])) {
                    $params['cp_np_sort_by'] = Tygh::$app['session']['cp_np_saved_sortings'][$manuf_code]['cp_np_sort_by'];
                    $params['sort_order'] = Tygh::$app['session']['cp_np_saved_sortings'][$manuf_code]['sort_order'];
                }
            }
            $params['cp_current_prod_id'] = $params['product_id'];
            list($items, $o_search, $other_list_order) = fn_cp_np_get_mosts_products($params, false, false);
            
            Tygh::$app['view']->assign('items', $items);
            Tygh::$app['view']->assign('o_search', $o_search);
            Tygh::$app['view']->assign('other_list_order', $other_list_order);
            
            if (!empty($params['cp_np_pagination'])) {
                Tygh::$app['view']->assign('current_prod_id', $_REQUEST['product_id']);
                Tygh::$app['view']->assign('current_wh_id', !empty($_REQUEST['warehouse_id']) ? $_REQUEST['warehouse_id'] : 0);
                $more_list = Registry::get('view')->fetch('addons/cp_product_page/components/more_products_table.tpl');
                Tygh::$app['ajax']->assign('more_list', $more_list);
            }
            
            Registry::get('view')->display('addons/cp_product_page/blocks/product_most_blocks.tpl');
            exit;
        } else {
            if (isset(Tygh::$app['session']['cp_np_saved_sortings']) && isset(Tygh::$app['session']['cp_np_saved_sortings'] [$manuf_code])) {
                unset(Tygh::$app['session']['cp_np_saved_sortings'] [$manuf_code]);
            }
            if (!empty($_REQUEST['features_hash']) || (!empty($_REQUEST['cp_np_this_product']) && defined('AJAX_REQUEST'))) {
                
                if (!empty($_REQUEST['cp_np_this_product'])) {
                    $new_product_id = $_REQUEST['product_id'];
                    if (!empty($_REQUEST['warehouse_id'])) {
                        $best_wh_id = $_REQUEST['warehouse_id'];
                    }
                }
                if (!empty($new_product_id)) {
                    if (empty($_REQUEST['cp_np_this_product'])) {
                        $_GET['product_id'] = $_REQUEST['product_id'] = $new_product_id;
                        if (!empty($best_wh_id)) {
                            $_GET['warehouse_id'] = $_REQUEST['warehouse_id'] = $best_wh_id;
                        }
                    }
                    if (defined('AJAX_REQUEST')) {
                        $page = isset($_REQUEST['page']) ? '&page=' . $_REQUEST['page'] : '';
                        Tygh::$app['ajax']->assign('cp_is_new_link', fn_url('products.view?product_id=' . $_REQUEST['product_id'] . '&warehouse_id=' . $best_wh_id . $page));
                    }
    //                 if (empty($_REQUEST['cp_np_this_product'])) {
    //                     Tygh::$app['view']->assign('cp_is_best_offer', $new_product_id);
    //                 }
                }
            } elseif (empty($_REQUEST['warehouse_id'])) {
                $params = $_REQUEST;
                $best_wh_id = fn_cp_np_get_cur_product_best_wh($_REQUEST['product_id']);
                if (!empty($best_wh_id)) {
                    $_GET['warehouse_id'] = $_REQUEST['warehouse_id'] = $best_wh_id;
                    $page = isset($_REQUEST['page']) ? '&page=' . $_REQUEST['page'] : '';
                    $url = fn_url('products.view?product_id=' . $_REQUEST['product_id'] . '&warehouse_id=' . $best_wh_id . $page);
                    Tygh::$app['view']->assign('cp_is_new_link2', $url);
                    if (defined('AJAX_REQUEST')) {
                        Tygh::$app['ajax']->assign('cp_is_new_link', $url);
                    }
                }
            }
        }
    }
}
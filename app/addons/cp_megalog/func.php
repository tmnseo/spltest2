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
/**
Hook for delete order
*/
function fn_cp_megalog_delete_order($order_id) 
{
    $types = fn_get_schema('cp_ml', 'types');
    if (!empty($order_id) && !empty($types) && !empty($types['orders']) && !empty($types['orders']['delete'])) {
        $req_data = array(
            'order_id' => $order_id
        );
        $put_data = array(
            'controller' => 'orders',
            'mode' => 'delete',
            'method' => 'post',
            'timestamp' => time(),
            'user_id' => !empty(Tygh::$app['session']['auth']['user_id']) ? Tygh::$app['session']['auth']['user_id'] : 0,
            'object_id' => $order_id,
            'request' => json_encode($req_data)
        );
        fn_cp_megalog_ml_add_log($put_data);
    }
}
/**
Hook for update user pre
*/
function fn_cp_megalog_update_user_pre ($user_id, $user_data, $auth, $ship_to_another, $notify_user) { 
    if (!empty($user_id) && !empty($user_data)) {
        $types = fn_get_schema('cp_ml', 'types');
        if (!empty($types) && !empty($types['profiles']) && !empty($types['profiles']['update'])) {
            $user_type = db_get_field("SELECT user_type FROM ?:users WHERE user_id = ?i", $user_id);
            if (!empty($user_type) && $user_type == 'V') {
                $user_data['user_id'] = $user_id;
                $user_data['area'] = AREA;
                $put_data = array(
                    'controller' => 'profiles',
                    'mode' => 'update',
                    'method' => 'post',
                    'timestamp' => time(),
                    'user_id' => !empty(Tygh::$app['session']['auth']['user_id']) ? Tygh::$app['session']['auth']['user_id'] : 0,
                    'object_id' => $user_id,
                    'request' => json_encode($user_data)
                );
                fn_cp_megalog_ml_add_log($put_data);
            }
        }
    }
}
//
/*
Hook for change order status
*/
function fn_cp_megalog_change_order_status ($status_to, $status_from, $order_info, $force_notification, $order_statuses, $place_order) {
    
    $types = fn_get_schema('cp_ml', 'types');
    if (!empty($types) && !empty($types['orders']) && !empty($types['orders']['update_status'])) {
        if ($status_to != $status_from) {
            $notice = Registry::get('cp_ml_changing_status_comment');
            if (empty($notice)) {
                $notice = '';
            }
            $description = $order_statuses[$status_from]['description'] . ' -> ' . $order_statuses[$status_to]['description'];
            if (!$place_order && $status_to != 'N') {
                $req_data = array(
                    'order_id' => $order_info['order_id'],
                    'label' => 'cp_ml_status_changed',
                    'description' => $description,
                    'notice' => $notice
                );
                $put_data = array(
                    'controller' => 'orders',
                    'mode' => 'update_status',
                    'method' => 'post',
                    'timestamp' => time(),
                    'user_id' => !empty(Tygh::$app['session']['auth']['user_id']) ? Tygh::$app['session']['auth']['user_id'] : 0,
                    'object_id' => $order_info['order_id'],
                    'request' => json_encode($req_data)
                );
                fn_cp_megalog_ml_add_log($put_data);
            }
        }
    }
}
/**
Hook for place order
*/
function fn_cp_megalog_place_order($order_id, $action, $order_status, $cart, $auth)
{
    if (!empty($order_id)) {
        $types = fn_get_schema('cp_ml', 'types');
        if (!empty($types) && !empty($types['checkout']) && !empty($types['checkout']['place_order'])) {
            if ($order_status == 'N') {
                $action_status = 'cp_ml_order_created';
            } else {
                $action_status = 'cp_ml_order_changed';
            }
            $req_data = array(
                'order_id' => $order_id,
                'label' => $action_status
            );
            $put_data = array(
                'controller' => 'orders',
                'mode' => 'place_order',
                'method' => 'post',
                'timestamp' => time(),
                'user_id' => !empty(Tygh::$app['session']['auth']['user_id']) ? Tygh::$app['session']['auth']['user_id'] : 0,
                'object_id' => $order_id,
                'request' => json_encode($req_data)
            );
            fn_cp_megalog_ml_add_log($put_data);
        }
    }
}
/**
Hook for update shipping
*/
function fn_cp_megalog_update_shipping_post ($shipping_data, $shipping_id, $lang_code, $action) {
    
    //for megalog
    if (!empty($shipping_id) && !empty($shipping_data)) {
        $mode = !empty($action) ? $action : 'update';
        $types = fn_get_schema('cp_ml', 'types');
        if (!empty($types) && !empty($types['shippings']) && !empty($types['shippings'][$mode])) {
            $shipping_data['shipping_id'] = $shipping_id;
            $put_data = array(
                'controller' => 'shippings',
                'mode' => $mode,
                'method' => 'post',
                'timestamp' => time(),
                'user_id' => !empty(Tygh::$app['session']['auth']['user_id']) ? Tygh::$app['session']['auth']['user_id'] : 0,
                'object_id' => $shipping_id,
                'request' => json_encode($shipping_data)
            );
            fn_cp_megalog_ml_add_log($put_data);
        }
    }
}
/**
Hook for delete shipping. Megalog
*/
function fn_cp_megalog_delete_shipping ($shipping_id, $result) {
    if (!empty($shipping_id) && !empty($result)) {
    //for megalog
        $types = fn_get_schema('cp_ml', 'types');
        if (!empty($types) && !empty($types['shippings']) && !empty($types['shippings']['delete'])) {
            $req_data = array(
                'shipping_id' => $shipping_id
            );
            $put_data = array(
                'controller' => 'shippings',
                'mode' => 'delete',
                'method' => 'post',
                'timestamp' => time(),
                'user_id' => !empty(Tygh::$app['session']['auth']['user_id']) ? Tygh::$app['session']['auth']['user_id'] : 0,
                'object_id' => $shipping_id,
                'request' => json_encode($req_data)
            );
            fn_cp_megalog_ml_add_log($put_data);
        }
    }
}
/**
Hook for delete category. Megalog
*/
function fn_cp_megalog_delete_category_after ($category_id) {
    if (!empty($category_id)) {
    //for megalog
        $types = fn_get_schema('cp_ml', 'types');
        if (!empty($types) && !empty($types['categories']) && !empty($types['categories']['delete'])) {
            $req_data = array(
                'category_id' => $category_id
            );
            $put_data = array(
                'controller' => 'categories',
                'mode' => 'delete',
                'method' => 'post',
                'timestamp' => time(),
                'user_id' => !empty(Tygh::$app['session']['auth']['user_id']) ? Tygh::$app['session']['auth']['user_id'] : 0,
                'object_id' => $category_id,
                'request' => json_encode($req_data)
            );
            fn_cp_megalog_ml_add_log($put_data);
        }
    }
}
//FUNCTIONS
/**
Hook for delete product filter. Megalog
*/
function fn_cp_megalog_delete_product_filter_post ($filter_id) {
    if (!empty($filter_id)) {
        $types = fn_get_schema('cp_ml', 'types');
        if (!empty($types) && !empty($types['product_filters']) && !empty($types['product_filters']['delete'])) {
            $req_data = array(
                'filter_id' => $filter_id
            );
            $put_data = array(
                'controller' => 'product_filters',
                'mode' => 'delete',
                'method' => 'post',
                'timestamp' => time(),
                'user_id' => !empty(Tygh::$app['session']['auth']['user_id']) ? Tygh::$app['session']['auth']['user_id'] : 0,
                'object_id' => $filter_id,
                'request' => json_encode($req_data)
            );
            fn_cp_megalog_ml_add_log($put_data);
        }
    }
}
/**
Hook for update product filter. Megalog
*/
function fn_cp_megalog_update_product_filter ($filter_data, $filter_id, $lang_code) {
    if (!empty($filter_data) && !empty($filter_id)) {
        $types = fn_get_schema('cp_ml', 'types');
        if (!empty($types) && !empty($types['product_filters']) && !empty($types['product_filters']['update'])) {
            $filter_data['filter_id'] = $filter_id;
            $put_data = array(
                'controller' => 'product_filters',
                'mode' => 'update',
                'method' => 'post',
                'timestamp' => time(),
                'user_id' => !empty(Tygh::$app['session']['auth']['user_id']) ? Tygh::$app['session']['auth']['user_id'] : 0,
                'object_id' => $filter_id,
                'request' => json_encode($filter_data)
            );
            fn_cp_megalog_ml_add_log($put_data);
        }
    }
}
/**
Hook for delete product feature. Megalog
*/
function fn_cp_megalog_delete_feature_post ($feature_id, $variant_ids) {
    if (!empty($feature_id)) {
        $types = fn_get_schema('cp_ml', 'types');
        if (!empty($types) && !empty($types['product_features']) && !empty($types['product_features']['delete'])) {
            $req_data = array(
                'feature_id' => $feature_id
            );
            $put_data = array(
                'controller' => 'product_features',
                'mode' => 'delete',
                'method' => 'post',
                'timestamp' => time(),
                'user_id' => !empty(Tygh::$app['session']['auth']['user_id']) ? Tygh::$app['session']['auth']['user_id'] : 0,
                'object_id' => $feature_id,
                'request' => json_encode($req_data)
            );
            fn_cp_megalog_ml_add_log($put_data);
        }
    }
}
/**
Hook for update product feature. Megalog
*/
function fn_cp_megalog_update_product_feature_post ($feature_data, $feature_id, $deleted_variants, $lang_code) {
    if (!empty($feature_data) && !empty($feature_id)) {
        $types = fn_get_schema('cp_ml', 'types');
        if (!empty($types) && !empty($types['product_features']) && !empty($types['product_features']['update'])) {
            $feature_data['feature_id'] = $feature_id;
            $put_data = array(
                'controller' => 'product_features',
                'mode' => 'update',
                'method' => 'post',
                'timestamp' => time(),
                'user_id' => !empty(Tygh::$app['session']['auth']['user_id']) ? Tygh::$app['session']['auth']['user_id'] : 0,
                'object_id' => $feature_id,
                'request' => json_encode($feature_data)
            );
            fn_cp_megalog_ml_add_log($put_data);
        }
    }
}
/**
Hook for delete discussion post. Megalog
*/
function fn_cp_megalog_discussion_delete_post_post ($post_id) {
    if (!empty($post_id)) {
        $types = fn_get_schema('cp_ml', 'types');
        if (!empty($types) && !empty($types['reviews']) && !empty($types['reviews']['delete'])) {
            $req_data = array(
                'post_id' => $post_id
            );
            $put_data = array(
                'controller' => 'reviews',
                'mode' => 'delete',
                'method' => 'post',
                'timestamp' => time(),
                'user_id' => !empty(Tygh::$app['session']['auth']['user_id']) ? Tygh::$app['session']['auth']['user_id'] : 0,
                'object_id' => $post_id,
                'request' => json_encode($req_data)
            );
            fn_cp_megalog_ml_add_log($put_data);
        }
    }
}
/**
Hook for delete banners. Megalog
*/
function fn_cp_megalog_delete_banners ($banner_id) {
    if (!empty($banner_id)) {
        $types = fn_get_schema('cp_ml', 'types');
        if (!empty($types) && !empty($types['banners']) && !empty($types['banners']['delete'])) {
            $req_data = array(
                'banner_id' => $banner_id
            );
            $put_data = array(
                'controller' => 'banners',
                'mode' => 'delete',
                'method' => 'post',
                'timestamp' => time(),
                'user_id' => !empty(Tygh::$app['session']['auth']['user_id']) ? Tygh::$app['session']['auth']['user_id'] : 0,
                'object_id' => $banner_id,
                'request' => json_encode($req_data)
            );
            fn_cp_megalog_ml_add_log($put_data);
        }
    }
}
/**
Hook for delete page. Megalog
*/
function fn_cp_megalog_delete_page ($v) {
    if (!empty($v)) {
        $types = fn_get_schema('cp_ml', 'types');
        if (!empty($types) && !empty($types['pages']) && !empty($types['pages']['delete'])) {
            $req_data = array(
                'page_id' => $v
            );
            $put_data = array(
                'controller' => 'pages',
                'mode' => 'delete',
                'method' => 'post',
                'timestamp' => time(),
                'user_id' => !empty(Tygh::$app['session']['auth']['user_id']) ? Tygh::$app['session']['auth']['user_id'] : 0,
                'object_id' => $v,
                'request' => json_encode($req_data)
            );
            fn_cp_megalog_ml_add_log($put_data);
        }
    }
}
/**
Hook for update page. Megalog
*/
function fn_cp_megalog_update_page_post ($page_data, $page_id, $lang_code, $create, $old_page_data) {
    if (!empty($page_data) && !empty($page_id)) {
        $mode = !empty($create) ? 'add' : 'update';
        $types = fn_get_schema('cp_ml', 'types');
        if (!empty($types) && !empty($types['pages']) && !empty($types['pages'][$mode])) {
            $page_data['page_id'] = $page_id;
            $put_data = array(
                'controller' => 'pages',
                'method' => 'post',
                'timestamp' => time(),
                'user_id' => !empty(Tygh::$app['session']['auth']['user_id']) ? Tygh::$app['session']['auth']['user_id'] : 0,
                'object_id' => $page_id,
                'request' => json_encode($page_data)
            );
            $put_data['mode'] = !empty($create) ? 'add' : 'update';
            fn_cp_megalog_ml_add_log($put_data);
        }
    }
}
/**
Hook for delete company. Megalog
*/
function fn_cp_megalog_delete_company ($company_id, $result) {
    if (!empty($result) && !empty($company_id)) {
        $types = fn_get_schema('cp_ml', 'types');
        if (!empty($types) && !empty($types['companies']) && !empty($types['companies']['delete'])) {
            $req_data = array(
                'company_id' => $company_id
            );
            $put_data = array(
                'controller' => 'companies',
                'mode' => 'delete',
                'method' => 'post',
                'timestamp' => time(),
                'user_id' => !empty(Tygh::$app['session']['auth']['user_id']) ? Tygh::$app['session']['auth']['user_id'] : 0,
                'object_id' => $company_id,
                'request' => json_encode($req_data)
            );
            fn_cp_megalog_ml_add_log($put_data);
        }
    }
}
/**
Hook for update company. Megalog
*/
function fn_cp_megalog_update_company ($company_data, $company_id, $lang_code, $action) {
    if (!empty($company_id) && !empty($company_data) && !empty($action) && $action == 'update') {
        $types = fn_get_schema('cp_ml', 'types');
        if (!empty($types) && !empty($types['companies']) && !empty($types['companies']['update'])) {
            $company_data['company_id'] = $company_id;
            $put_data = array(
                'controller' => 'companies',
                'mode' => 'update',
                'method' => 'post',
                'timestamp' => time(),
                'user_id' => !empty(Tygh::$app['session']['auth']['user_id']) ? Tygh::$app['session']['auth']['user_id'] : 0,
                'object_id' => $company_id,
                'request' => json_encode($company_data)
            );
            fn_cp_megalog_ml_add_log($put_data);
        }
    }
}
/**
Hook for update category. Megalog
*/
function fn_cp_megalog_update_category_post ($category_data, $category_id, $lang_code) {
    if (!empty($category_id) && !empty($category_data)) {
        $types = fn_get_schema('cp_ml', 'types');
        if (!empty($types) && !empty($types['categories']) && !empty($types['categories']['update'])) {
            $category_data['category_id'] = $category_id;
            $put_data = array(
                'controller' => 'categories',
                'mode' => 'update',
                'method' => 'post',
                'timestamp' => time(),
                'user_id' => !empty(Tygh::$app['session']['auth']['user_id']) ? Tygh::$app['session']['auth']['user_id'] : 0,
                'object_id' => $category_id,
                'request' => json_encode($category_data)
            );
            fn_cp_megalog_ml_add_log($put_data);
        }
    }
}
/**
Hook for delete shipments. Megalog
*/
function fn_cp_megalog_delete_shipments ($shipment_ids, $result) {
    if (!empty($result) && !empty($shipment_ids)) {
        $types = fn_get_schema('cp_ml', 'types');
        if (!empty($types) && !empty($types['shipments']) && !empty($types['shipments']['delete'])) {
            $req_data = array(
                'delete_ids' => is_array($shipment_ids) ? implode(',',$shipment_ids) : $shipment_ids
            );
            $put_data = array(
                'controller' => 'shipments',
                'mode' => 'delete',
                'method' => 'post',
                'timestamp' => time(),
                'user_id' => !empty(Tygh::$app['session']['auth']['user_id']) ? Tygh::$app['session']['auth']['user_id'] : 0,
                'object_id' => $req_data['delete_ids'],
                'request' => json_encode($req_data)
            );
            fn_cp_megalog_ml_add_log($put_data);
        }
    }
}
/**
Hook for create shipment. Megalog
*/
function fn_cp_megalog_create_shipment_post ($shipment_data, $order_info, $group_key, $all_products, $shipment_id) {
    if (!empty($shipment_id)) {
        $types = fn_get_schema('cp_ml', 'types');
        if (!empty($types) && !empty($types['shipments']) && !empty($types['shipments']['add'])) {
            $shipment_data['shipment_id'] = $shipment_id;
            $put_data = array(
                'controller' => 'shipments',
                'mode' => 'add',
                'method' => 'post',
                'timestamp' => time(),
                'user_id' => !empty(Tygh::$app['session']['auth']['user_id']) ? Tygh::$app['session']['auth']['user_id'] : 0,
                'object_id' => $shipment_id,
                'request' => json_encode($shipment_data)
            );
            fn_cp_megalog_ml_add_log($put_data);
        }
    }
}
/**
 * Add megalog data
 *
 * @param array $data data to save
 * @return boolean
 */
function fn_cp_megalog_ml_add_log ($data) {
    if (!empty($data)) {
        db_replace_into('cp_ml_megalog', $data);
    }
    return true;
}
/**
 * Get megalog data
 *
 * @param array $params params
 * @param int $items_per_page items per page
 * @param string $lang_code Language code
 * @return boolean
 */
function fn_cp_megalog_ml_get_logs($params = array(), $items_per_page = 0, $lang_code = CART_LANGUAGE) {
    $default_params = array (
        'page' => 1,
        'items_per_page' => $items_per_page,
    );
    $params = array_merge($default_params, $params);
    $fields = array (
        '?:cp_ml_megalog.*',
        '?:users.firstname',
        '?:users.lastname',
    );
    $sortings = array (
        'controller' => "?:cp_ml_megalog.controller",
        'mode' => "?:cp_ml_megalog.mode",
        'timestamp' => "?:cp_ml_megalog.timestamp",
        'name' => array('?:users.firstname', '?:users.lastname'),
    );
    $condition = $join = $group = '';
    
    if (!empty($params['controller'])) {
        $condition .= db_quote(" AND ?:cp_ml_megalog.controller = ?s", $params['controller']);
    }
    if (!empty($params['mode'])) {
        $condition .= db_quote(" AND ?:cp_ml_megalog.mode = ?s", $params['mode']);
    }
    if (!empty($params['object_id'])) {
        $condition .= db_quote(" AND (?:cp_ml_megalog.object_id = ?s OR ?:cp_ml_megalog.object_id LIKE ?l 
            OR ?:cp_ml_megalog.object_id LIKE ?l OR ?:cp_ml_megalog.object_id LIKE ?l)", $params['object_id'], '%,' . $params['object_id'] . ',%', $params['object_id'] . ',%', '%,' . $params['object_id']);
    }
    
    if (isset($params['name']) && fn_string_not_empty($params['name'])) {
        $arr = fn_explode(' ', $params['name']);
        foreach ($arr as $k => $v) {
            if (!fn_string_not_empty($v)) {
                unset($arr[$k]);
            }
        }
        $like_expression = ' AND (';
        $search_string = '%' . trim($params['name']) . '%';

        if (sizeof($arr) == 2) {
            $like_expression .= db_quote('?:users.firstname LIKE ?l', '%' . array_shift($arr) . '%');
            $like_expression .= db_quote(' OR ?:users.lastname LIKE ?l', '%' . array_shift($arr) . '%');
        } else {
            $like_expression .= db_quote('?:users.firstname LIKE ?l', $search_string);
            $like_expression .= db_quote(' OR ?:users.lastname LIKE ?l', $search_string);
        }
        $like_expression .= ')';
        $condition .= $like_expression;
    }
    
    $join .= db_quote(" LEFT JOIN ?:users ON ?:users.user_id = ?:cp_ml_megalog.user_id");
    $sorting = db_sort($params, $sortings, 'timestamp', 'desc');
    
    $limit = '';
    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:cp_ml_megalog $join WHERE 1 $condition $group");
        $limit = db_paginate($params['page'], $params['items_per_page']);
    }
   
    $logs = db_get_hash_array('SELECT ' . implode(', ', $fields) . " FROM ?:cp_ml_megalog $join WHERE 1 $condition $group $sorting $limit", 'log_id');
    if (!empty($logs)) {
        foreach($logs as $s_key => &$log_data) {
            if (!empty($log_data['request'])) {
                $log_data['parce_request'] = json_decode($log_data['request'], true);
                $log_data['parce_req'] = $log_data['request'];
            }
        }
    }
    return array($logs, $params);
}
// end MEGALOG
/**
 * Count products in categories for vendors
 *
 * @return boolean
 */
function fn_cp_megalog_count_vendor_categories_cron() {
    $all_vendors = db_get_array("SELECT ?:companies.company_id, ?:products_categories.category_id FROM ?:companies 
        LEFT JOIN ?:products ON ?:products.company_id = ?:companies.company_id 
        LEFT JOIN ?:products_categories ON ?:products_categories.product_id = ?:products.product_id 
        WHERE ?:companies.status = ?s GROUP BY ?:companies.company_id, ?:products_categories.category_id", 'A');
    if (!empty($all_vendors)) {
        foreach($all_vendors as $vend_data) {
            if (!empty($vend_data) && !empty($vend_data['category_id'])) {
                $total_in_cat = db_get_field("SELECT COUNT(?:products_categories.product_id) FROM ?:products 
                    LEFT JOIN ?:products_categories ON ?:products_categories.product_id = ?:products.product_id 
                    WHERE ?:products_categories.category_id = ?i AND ?:products.company_id = ?i", $vend_data['category_id'], $vend_data['company_id']);
                if (empty($total_in_cat)) {
                    $total_in_cat = 0;
                }
                $data = array(
                    'company_id' => $vend_data['company_id'],
                    'category_id' => $vend_data['category_id'],
                    'product_count' => $total_in_cat
                );
                db_replace_into('category_vendor_product_count', $data);
            }
        }
    }
    return true;
}
//cron
function fn_cp_megalog_cron_run_info()
{
    $admin_ind = Registry::get('config.admin_index');
    $__params = Registry::get('addons.cp_megalog');
    if (!empty($__params) && !empty($__params['cron_pass'])) {
        $cron_pass = $__params['cron_pass'];
    } else {
        $cron_pass = '';
    }
    $hint = '<b>' . __("cp_ml_use_this_for_clear_lgos") . ':</b><br /><code>php ' . Registry::get('config.dir.root') .'/' . $admin_ind . ' --dispatch=cronos.cron_clear_logs --cron_pass=' . $cron_pass . '</code>';
    //$hint .= '<br /><b>' . __("cp_ml_use_this_for_reculc_vend_counts") . ':</b><br /><code>php ' . Registry::get('config.dir.root') .'/' . $admin_ind . ' --dispatch=cronos.recalc_vendor_counts --cron_pass=' . $cron_pass . '</code>';
    
    return $hint;
}

function fn_cp_megalog_get_logs_controllers_modes()
{
    $controllers = db_get_fields("SELECT DISTINCT controller FROM ?:cp_ml_megalog");
    $modes = db_get_fields("SELECT DISTINCT mode FROM ?:cp_ml_megalog");
    
    return array($controllers, $modes);
}

function fn_cp_ml_clear_cron_logs($days)
{
    if (!empty($days)) {
        $now_time = time();
        db_query("DELETE FROM ?:cp_ml_megalog WHERE timestamp <= ?i", $now_time - $days*24*60*60);
    }
    return true;
}
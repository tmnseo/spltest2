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
use Tygh\Enum\OrderDataTypes;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

//HOOKS

function fn_cp_orders_list_details_change_order_status($status_to, $status_from, &$order_info, $force_notification, $order_statuses, $place_order) 
{
    if (!empty($order_info['order_id'])) {
        
        if (!empty($order_info['shipment_ids'])) {
            $shipment_id = current($order_info['shipment_ids']);
            $tracking_number = db_get_field("SELECT tracking_number FROM ?:shipments WHERE shipment_id = ?i", $shipment_id);
        }

        if (!empty($order_info['shipping']) && !empty($tracking_number)) {

            
            $shipping = current($order_info['shipping']);
            $module = !empty($shipping['module']) ? $shipping['module'] : '';
            $service_code = !empty($shipping['service_code']) ? $shipping['service_code'] : '';
            
            if (empty($module) || empty($service_code)) {
                return ;

            }elseif ($module == 'edost') {
                if (Registry::get('addons.rus_edost.status') == 'A') {
                    $edost_services = fn_get_schema('edost', 'services', 'php', true);
                    if (!empty($edost_services[$service_code]['cp_code'])) {
                        $shipping_link_code = $module . ' - ' . $edost_services[$service_code]['cp_code'];
                    }
                }
            }else {
                $shipping_link_code = $module;
            }
            
            if (!empty($shipping_link_code)) {
                
                $shipping_link = db_get_field("SELECT track_link FROM ?:cp_oc_service_link WHERE module = ?s", $shipping_link_code);

                if (!empty($shipping_link)) {
                    
                    $href = str_replace('[TRACKING_NUMBER]', $tracking_number, $shipping_link);
                    $order_info['cp_shipping_service_link'] = "<a href=". $href . " target='_blank'>" . $tracking_number ."</a>";
                }
            }
        }
    }
}

function fn_cp_orders_list_details_pre_get_orders(&$params, &$fields, $sortings, $get_totals, $lang_code)
{
    if (AREA == 'C') {
        $params['company_name'] = true;
        $fields[] = '?:orders.shipping_ids';
    }
}

function fn_cp_orders_list_details_get_orders_post($params, &$orders)
{
    if (AREA == 'C' && !empty($orders)) {
        list($links, $avail_services) = fn_cp_oc_get_service_links(array());
        if (!empty($links)) {
            $edost_services = array();
            if (Registry::get('addons.rus_edost.status') == 'A') {
                $edost_services = fn_get_schema('edost', 'services', 'php', true);
            }
            foreach($orders as &$ord_data) {
                if (!empty($ord_data['shipping_ids'])) {
                    $ships = explode(',', $ord_data['shipping_ids']);
                    $ship_id = reset($ships);
                    if (!empty($ship_id)) {
                        $service = db_get_row("SELECT ?:shipping_services.* FROM ?:shippings 
                            LEFT JOIN ?:shipping_services ON ?:shipping_services.service_id = ?:shippings.service_id
                            WHERE ?:shippings.shipping_id = ?i", $ship_id);
                        if (!empty($ord_data['tracking_number'])) {
                            if (!empty($service) && $service['module'] == 'edost' && !empty($service['code']) && !empty($edost_services[$service['code']]) 
                                && !empty($edost_services[$service['code']]['cp_code']) && !empty($links['edost - ' . $edost_services[$service['code']]['cp_code']])) {
                                $track_url = $links['edost - ' . $edost_services[$service['code']]['cp_code']]['track_link'];
                                $ord_data['cp_tracking_url'] = str_replace('[TRACKING_NUMBER]', $ord_data['tracking_number'], $track_url);
                                
                            } elseif (!empty($service) && !empty($service['module']) && !empty($links[$service['module']])) {
                                $ord_data['cp_tracking_url'] = str_replace('[TRACKING_NUMBER]', $ord_data['tracking_number'], $links[$service['module']]['track_link']);
                            }
                        } elseif (!empty($service) && !empty($service['code']) && $service['code'] == 'pickup') {
                            $additional_data = db_get_hash_single_array("SELECT type, data FROM ?:order_data WHERE order_id = ?i", array('type', 'data'), $ord_data['order_id']);
                            if (!empty($additional_data[OrderDataTypes::SHIPPING])) {
                                $ord_data['cp_pickup_store_data'] = unserialize($additional_data[OrderDataTypes::SHIPPING]);
                            }
                            $ord_data['cp_is_pick_up_order'] = true;
                        }
                    }
                }
            }
        }
    }
}

function fn_cp_orders_list_details_get_order_items_info_post(&$order, $v, $k)
{
    if (AREA == 'C' && !empty($v['product_id'])) {
        $get_manufact = Registry::get('cp_oc_is_get_details');
        if (!empty($get_manufact)) {
            $brand_id = Registry::get('addons.cp_orders_list_details.brand_feature_id');
            if (!empty($brand_id)) {
                $order['products'][$k]['cp_oc_manufacturer'] = db_get_field("SELECT ?:product_feature_variant_descriptions.variant FROM ?:product_features_values
                    LEFT JOIN ?:product_feature_variant_descriptions ON ?:product_feature_variant_descriptions.variant_id = ?:product_features_values.variant_id AND ?:product_feature_variant_descriptions.lang_code = ?s
                    WHERE ?:product_features_values.feature_id = ?i AND ?:product_features_values.product_id = ?i", CART_LANGUAGE, $brand_id, $v['product_id']
                );
            }
        }
    }
}

function fn_cp_orders_list_details_get_statuses($join, &$condition, $type, $status_to_select, $additional_statuses, $exclude_parent, $lang_code, $company_id, $order)
{
    if (AREA == 'C') {
        $is_cancels = Registry::get('cp_oc_get_canceling');
        if (!empty($is_cancels)) {
            $condition = db_quote(' AND ?:statuses.cp_oc_allow_cancel = ?s', 'Y');
        }
    }
}

function fn_cp_orders_list_details_update_status_pre($status, $status_data, $type, $lang_code, $can_continue)
{
    if (!empty($status_data) && !empty($status_data['status_id']) && $type == STATUSES_ORDER && isset($status_data['cp_oc_is_cancel']) && $status_data['cp_oc_is_cancel'] == 'Y') {
        $check_other_statuses = db_get_fields("SELECT status_id FROM ?:statuses WHERE cp_oc_is_cancel = ?s AND status_id != ?i", 'Y', $status_data['status_id']);
        if (!empty($check_other_statuses)) {
            db_query("UPDATE ?:statuses SET cp_oc_is_cancel = ?i WHERE status_id != ?i", 'N', $status_data['status_id']);
            fn_set_notification('W',  __('warning'), __('cp_oc_status_for_cancel_is') . ' - ' .$status_data['description']);
        }
    }
}
//FUNCTIONS

function fn_cp_oc_get_orders_statuses_for_cancel()
{
    $statuses = db_get_fields("SELECT status FROM ?:statuses WHERE type = ?s AND cp_oc_allow_cancel = ?s", STATUSES_ORDER, 'Y');
    return $statuses;
}

function fn_cp_oc_get_order_status_to_cancel()
{
    $statuses = db_get_field("SELECT status FROM ?:statuses WHERE type = ?s AND cp_oc_is_cancel = ?s", STATUSES_ORDER, 'Y');
    return $statuses;
}

function fn_cp_oc_cancel_order_by_customer ($params = array(), $auth) {
    if (!empty($params) && !empty($params['order_id'])) {
        $order_info = db_get_row("SELECT total, status, issuer_id, firstname, lastname, timestamp, is_parent_order, email, user_id FROM ?:orders WHERE order_id = ?i", $params['order_id']);
        if (!empty($order_info) && !empty($order_info['email'])) {
            $params['notify_department'] = $params['notify_user'] = $params['notify_vendor'] = 'Y';
            $go_next = false;
            $old_status = $order_info['status'];
            if (!empty($order_info['status'])) {
                $avail_statuses = fn_cp_oc_get_orders_statuses_for_cancel();
                if (in_array($order_info['status'], $avail_statuses)) {
                    $go_next = true;
                }
            }
            $cart = Tygh::$app['session']['cart'];
            $user_email = '';
            if (!empty($auth) && !empty($auth['user_id'])) {
                $user_email = db_get_field("SELECT email FROM ?:users WHERE user_id = ?i", $auth['user_id']);
            } elseif (!empty($cart) && !empty($cart['user_data']) && !empty($cart['user_data']['email'])) {
                $user_email = $cart['user_data']['email'];
            }
            if (!empty($go_next) && $order_info['email'] == $user_email) {

                $params['status'] = empty($params['status']) ? fn_cp_oc_get_order_status_to_cancel() : $params['status'];
                
                if (fn_change_order_status($params['order_id'], $params['status'], '', fn_get_notification_rules($params))) {
                    $order_info = fn_get_order_short_info($params['order_id']);
                    $new_status = $order_info['status'];
                    if ($params['status'] != $new_status) {
                        fn_set_notification('W', __('warning'), __('status_changed'));
                    } else {
                        fn_set_notification('N', __('notice'), __('status_changed'));
                    }
                    db_query("UPDATE ?:orders SET cp_oc_cust_cancel = ?s WHERE order_id = ?i", 'Y', $params['order_id']);
//             //add user_id to cancelation action
//                     if (Registry::get('addons.cp_megalog.status') == 'A') {
//                     }
//             //
                } else {
                    fn_set_notification('E', __('error'), __('error_status_not_changed'));
                }
            }
        }
    }
    return true;
}

function fn_cp_oc_delete_service_link($module)
{
    if (!empty($module)) {
        db_query("DELETE FROM ?:cp_oc_service_link WHERE module = ?s", $module);
    }
    return true;
}

function fn_cp_oc_update_service_links($data)
{
    if (!empty($data)) {
        foreach($data as $serv_data) {
            db_replace_into('cp_oc_service_link', $serv_data);
        }
    }
    return true;
}

function fn_cp_oc_get_service_links($params)
{
    $links = array();
    $avail_services = db_get_fields("SELECT DISTINCT module FROM ?:shipping_services WHERE module != ?s", 'edost');
    if (Registry::get('addons.rus_edost.status') == 'A') {
        $edost_services = fn_get_schema('edost', 'services', 'php', true);
        if (!empty($edost_services)) {
            foreach($edost_services as $key => $s_data) {
                if (!empty($s_data['cp_code']) && !in_array('edost - ' . $s_data['cp_code'], $avail_services)) {
                    $avail_services[] = 'edost - ' . $s_data['cp_code'];
                }
            }
        }
    }
    $links = db_get_hash_array("SELECT * FROM ?:cp_oc_service_link WHERE module IN (?a)", 'module', $avail_services);
    
    return array($links, $avail_services);
}
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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_cp_confirm_order_replace_text($text)
{
    $text = str_replace('"', "'", $text);
    $text = str_replace("\r", "", $text);
    $text = str_replace("\n", "", $text);
    $text = preg_replace("/((?<=>)|(?<=--)|(?<=.))[\s\n\r\t]+((?=--)|(?=<))/U", '', $text);

    return trim($text);
} 

function fn_settings_variants_addons_cp_confirm_order_order_status()
{
    $statuses_list  = array(__('do_not_change'));
    $order_statuses = fn_get_statuses();

    if (!empty($order_statuses)) {
        foreach ($order_statuses as $k => $v) {
            $statuses_list[$v['status']] = $v['description'];
        }
    }

    return $statuses_list;
}

/***********************************[hooks]***********************************/
function fn_cp_confirm_order_pre_get_orders($params, &$fields, $sortings, $get_totals, $lang_code)
{
    $fields[] = "?:orders.cp_confirm_status";
    

    
}
/*gMelnikov modifs*/
function fn_cp_confirm_order_get_orders($params, &$fields, &$sortings, $condition, &$join, &$group)
{
    if (AREA == 'C') {
        $fields[] = "?:shipments.tracking_number";
        $sortings[] = "tracking_number => ?:shipments.tracking_number";
        $join .= " LEFT JOIN ?:shipment_items ON ?:shipment_items.order_id = ?:orders.order_id";
        $join .= " LEFT JOIN ?:shipments ON ?:shipments.shipment_id = ?:shipment_items.shipment_id";
        $group = " GROUP BY ?:orders.order_id ";
    }
}
/*gMelnikov modifs*/
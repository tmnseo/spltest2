<?php

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function separationNameShippingMethod ($text) {
    if(preg_match_all("/.*\(([^)]*)\)/", $text, $result))
    {
        foreach($result[1] as $span_text)

        echo preg_replace("/\([^)]+\)/","",$text);

        echo '<span>' . $span_text . '</span>';

    } else {
        return $text;
    }
}
function fn_cp_add_note_to_elements(&$elements)
{
    foreach ($elements as $key => &$element) {
        $element['note'] = db_get_field("SELECT note FROM ?:form_descriptions WHERE object_id = ?i AND lang_code = ?s", $key, CART_LANGUAGE);
    }
}
function fn_cp_spl_theme_get_products($params, $fields, $sortings, &$condition, $join, $sorting, $group_by, $lang_code, $having)
{
    if (!empty($params['block_data']['type']) && $params['block_data']['type'] == 'products' && Registry::get('runtime.controller') == 'products' && Registry::get('runtime.mode') == 'view') {
        $product_id = !empty($_REQUEST['product_id']) ? $_REQUEST['product_id'] : null; // very bad style | gMelnikov
        if (!empty($product_id)) {
            $condition .= db_quote(" AND products.product_id <> ?i", $product_id);
        } 
    }
}
function fn_cp_spl_theme_gather_additional_products_data_post($product_ids, $params, &$products, $auth, $lang_code)
{   
    if (!empty($params['is_cp_product_block'])) {
        $products['exist_images'] = false;
        foreach ($products as $_pdata) {
            if (!empty($_pdata['main_pair'])) {
                $products['exist_images'] = true;
                break;
            }
        }
    }
}
function fn_cp_spl_theme_get_static_data($params, &$fields, $condition, $sorting, $lang_code)
{
    $fields[] = 'sd.cp_open_in_new_window';
}
function fn_cp_spl_theme_pre_place_order(&$cart, $allow, $product_groups)
{
    //$cart['cp_recipient_data'] = Tygh::$app['session']['auth']['cp_recipient_data'];
}
function fn_cp_spl_theme_create_order_details($order_id, $cart, &$order_details, $extra)
{   
    /*if (!empty($cart['cp_recipient_data'])) {
        $order_extra = unserialize($order_details['extra']);
        $order_extra['cp_recipient_data'] = $cart['cp_recipient_data'];
        $order_details['extra'] = serialize($order_extra);   
    }*/
}
function fn_cp_spl_theme_get_orders($params, &$fields, $sortings, $condition, &$join, $group)
{   
    $fields[] = '?:order_details.extra';
    $join .= " LEFT JOIN ?:order_details ON ?:orders.order_id = ?:order_details.order_id ";
}
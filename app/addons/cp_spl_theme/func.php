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

function fn_cp_spl_theme_cp_pre_communication_update_thread(&$thread_data, $params, $object_id, $auth)
{
    if (!empty($params['thread']['warehouse_id'])) {
        $thread_data['warehouse_id'] = $params['thread']['warehouse_id'];
    }
}

function fn_cp_spl_theme_get_products(&$params, $fields, $sortings, &$condition, &$join, $sorting, $group_by, $lang_code, $having)
{   
    if (Registry::get('runtime.controller') == 'companies' && Registry::get('runtime.mode') == 'products') {
        $params['load_products_extra_data'] = false;
    }
    
    $cp_wh_am_join = "?:warehouses_products_amount as cp_wh_am ON cp_wh_am.product_id = products.product_id";

    if (!empty($params['block_data']['type']) && $params['block_data']['type'] == 'products' && Registry::get('runtime.controller') == 'products' && Registry::get('runtime.mode') == 'view') {
        $product_id = !empty($_REQUEST['product_id']) ? $_REQUEST['product_id'] : null; // very bad style | gMelnikov
        if (!empty($product_id)) {
            $condition .= db_quote(" AND products.product_id <> ?i", $product_id);
        } 
    }
    if (empty($params['cp_np_type']) && empty($is_details_page) && stripos($join, $cp_wh_am_join) === false) {
        $join .= db_quote(" LEFT JOIN ?:warehouses_products_amount as cp_wh_am ON cp_wh_am.product_id = products.product_id");
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
    $cart['cp_recipient_data'] = !empty(Tygh::$app['session']['auth']['cp_recipient_data']) ? Tygh::$app['session']['auth']['cp_recipient_data'] : '';
}
function fn_cp_spl_theme_get_product_feature_variants($fields, $join, &$condition, $group_by, $sorting, $lang_code, $limit, &$params) {
    
    if (!empty($params['cp_only_show'])) {
        $condition .= db_quote(" AND cp_show_on_brands = ?s", 'Y');
        $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:product_feature_variants $join WHERE 1 $condition"); // hook is located below determining the number of items 
    }
}
function fn_cp_spl_theme_create_order_details($order_id, $cart, &$order_details, $extra)
{   
    if (!empty($cart['cp_recipient_data'])) {
        $order_extra = unserialize($order_details['extra']);
        $order_extra['cp_recipient_data'] = $cart['cp_recipient_data'];
        $order_details['extra'] = serialize($order_extra);   
    }
}
function fn_cp_spl_theme_get_products_before_select(&$params,
        $join,
        $condition,
        $u_condition,
        $inventory_join_cond,
        $sortings,
        $total,
        $items_per_page,
        $lang_code,
        $having)
{
    if (Registry::get('runtime.controller') == 'companies' && Registry::get('runtime.mode') == 'products') {
       $params['cp_np_type'] = 'B'; 
    }
    
}
function fn_cp_get_all_variants($feature_id, $params)
{   
    $variants = array();
    $variant_params = array(
        'feature_id' => $feature_id,
        'get_images' => true,
        'page' => !empty($params['page']) ? $params['page'] : 1,
        'items_per_page' => !empty($params['items_per_page']) ? $params['items_per_page'] : Registry::get('settings.Appearance.elements_per_page'),
        'cp_only_show' => 'Y'
    );

    list($cp_variants, $cp_variants_search) = fn_get_product_feature_variants($variant_params, Registry::get('settings.Appearance.elements_per_page'), DESCR_SL);
    if (!empty($cp_variants)) {
        foreach ($cp_variants as $variant) {
            $variants[fn_substr($variant['variant'], 0, 1)][] = $variant;
        }
    }

    return array ($variants, $cp_variants_search);
}
function fn_cp_get_user_storefront_id($user_id)
{
    $storefront_id = db_get_field("SELECT storefront_id FROM ?:users WHERE user_id = ?i", $user_id);

    return $storefront_id;
}


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
use Tygh\Http;
use Tygh\Addons\CpMatrixDestinations\Edost\Edost;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

/*HOOKS*/
function fn_cp_edost_improvement_template_document_order_context_init(&$document, &$order) 
{
    if (!empty($order->data['order_id'])) {
        $order->data['cp_edost_size_data'] = fn_cp_edost_improvement_get_edost_data_for_order($order->data['order_id']);
    }
}
function fn_cp_edost_improvement_cp_edost_get_request_data_post(&$post, $url, $shipping_info)
{
    $cart_products = Tygh::$app['session']['cart']['products'];
    if (!empty($cart_products)) {
        $_pdata = current($cart_products);

        if (!empty($_pdata['extra']['warehouse_data'])) {
            $warehouse_data = unserialize($_pdata['extra']['warehouse_data']);

            if (!empty($warehouse_data['city'])) {
                $warehouse_cities_ids = fn_rus_cities_get_city_ids(mb_strtolower($warehouse_data['city']), "", "");
                if (!empty($warehouse_cities_ids)) {
                    $edost_city_code = db_get_field("SELECT edost_code FROM ?:rus_edost_cities_link WHERE city_id = ?i", current($warehouse_cities_ids));
                    $post['from_city'] = !empty($edost_city_code) ? $edost_city_code : $warehouse_data['city'];
                }   
            }
        }
    }
}
function fn_cp_edost_improvement_rus_cities_find_cities($params, $lang_code, $items_per_page, $search, $fields, &$join, $condition)
{   
    //$join[] = db_quote("INNER JOIN ?:rus_edost_cities_link AS el ON el.city_id = rc.city_id");
}
/*HOOKS*/
function fn_cp_get_shipping_cost($data)
{   
    $url = 'http://www.edost.ru/edost_calc_kln.php';

    $order = fn_get_order_info($data['order_id']);
    
    /* There should only be one delivery */
    $current_shipping = !empty($order['shipping']) ? current($order['shipping']) : [];
    
    $current_shipping_price_value = !empty($current_shipping['rate']) ? $current_shipping['rate'] : 0;

    if (!empty($current_shipping)) {

        if (!empty($order['product_groups'])) {
            $current_product_group = current($order['product_groups']);
        }else {
            return 0 ;
        }
        
        $weight_data = fn_expand_weight($current_product_group['package_info']['W']);
        $location = !empty($current_product_group['package_info']['location']) ? $current_product_group['package_info']['location'] : [];

        if (empty($location)) {
            return 0;
        }

        $current_warehouse_data = !empty($order['warehouse_data_points']) ? current($order['warehouse_data_points']) : [];
        if (!empty($current_warehouse_data)) {

            $warehouse_city = !empty($current_warehouse_data['city']) ? $current_warehouse_data['city'] : '';

            if (empty($warehouse_city)) {
                return 0 ;
            }

            $warehouse_cities_ids = fn_rus_cities_get_city_ids(mb_strtolower($warehouse_city), "", "");

            if (!empty($warehouse_cities_ids)) {
                $edost_city_code = db_get_field("SELECT edost_code FROM ?:rus_edost_cities_link WHERE city_id = ?i", current($warehouse_cities_ids));
                $from_city = !empty($edost_city_code) ? $edost_city_code : $warehouse_data['city'];
            }else {
                return 0;
            }  
  
        }

        $request_data = array (
            'id' => $current_shipping['service_params']['store_id'],
            'p' => $current_shipping['service_params']['server_password'],
            'to_city' => fn_cp_get_edost_city_for_request($location),
            'zip' => !empty($location['zipcode']) ? $location['zipcode'] : '',
            'from_city' => !empty($from_city) ? $from_city : ''
        );

        $request_data['weight'] = $weight_data['plain'] * Registry::get('settings.General.weight_symbol_grams') / 1000;
        $request_data['strah'] = $current_product_group['package_info']['C'];


        $request_data['ln'] = !empty($data['length']) ? (float) $data['length'] : 0;
        $request_data['wd'] = !empty($data['width'])  ? (float) $data['width']  : 0;
        $request_data['hg'] = !empty($data['height']) ? (float) $data['height'] : 0;

        
        $key = md5(serialize($request_data));
        $edost_data = fn_get_session_data($key);
        
        if (empty($edost_data)) {
            $response = Http::post($url, $request_data, array('timeout' => 5));
            fn_set_session_data($key, $response);
        } else {
            $response = $edost_data;
        }

        if (!empty($response)) {
            $new_shipping_price_value = fn_cp_parse_edost_response($response, $current_shipping);

            return array($new_shipping_price_value, $current_shipping_price_value);
        }
    }

    return 0;
}

function fn_cp_get_edost_city_for_request($destination)
{
    $state = $country = '';

    foreach ($destination as $destination_id => $value) {
        $destination[$destination_id] = strtolower($value);
    }

    if (!empty($destination['state'])) {
        $state = $destination['state'];
    }

    if (!empty($destination['country'])) {
        $country = $destination['country'];
    }

    $cities_ids = fn_rus_cities_get_city_ids($destination['city'], $state, $country);
    if (empty($cities_ids)) {
        return '';
    }

    $cities = fn_rus_edost_get_codes($cities_ids);

    if (count($cities) == 1) {
        $result = reset($cities);

    } elseif (count($cities) < 1) {
        if (AREA != 'C') {
            fn_set_notification('E', __('notice'), __('shippings.edost.admin_city_not_served'));
        }

        return '';

    } else {
        if (AREA != 'C') {
            fn_set_notification('E', __('notice'), __('shippings.edost.admin_city_select_error'));
        } else {
            fn_set_notification('E', __('notice'), __('shippings.edost.city_select_error'));
        }

        return '';
    }

    return $result;
}

function fn_cp_parse_edost_response($response, $current_shipping)
{   
    $new_shipping_price_value = 0;
        
    if (!empty($current_shipping['service_code'])) {

       $return = Edost::_getRates($response); 
    }

    if (!empty($return)) {
        foreach ($return as $service_code => $service_data) {
            if($service_code == $current_shipping['service_code']) {
                $new_shipping_price_value = !empty($service_data['price']) ? $service_data['price'] : 0;
            }
        }
    }

    return $new_shipping_price_value;
}
function fn_cp_change_shipping_cost($order_id, $shipping_cost)
{   
    $cart = Tygh::$app['session']['cart'];

    fn_clear_cart($cart, true);
    $customer_auth = fn_fill_auth(array(), array(), false, 'C');

    $cart_status = md5(serialize($cart));
    fn_form_cart($order_id, $cart, $customer_auth, false);

    fn_store_shipping_rates($order_id, $cart, $customer_auth);

    $params = $_REQUEST;
    $params['cart_products'] = !empty($cart['products']) ? $cart['products'] : [];
    if (!empty($params['cart_products'])) {
        foreach ($params['cart_products'] as $key => &$_pdata) {
            $_pdata['stored_price'] = 'Y';
        }
    }

    if (!empty($cart['product_groups']) && isset($shipping_cost)) {
        foreach ($cart['product_groups'] as $group_key => &$group) {
            if (!empty($group['chosen_shippings'])) {
                foreach ($group['chosen_shippings'] as $shipping_key => $shipping) {
                    $params['stored_shipping'][$group_key][$shipping_key] = 'Y';
                    $params['stored_shipping_cost'][$group_key][$shipping_key] = $shipping_cost;
                    $group['shippings'][$shipping['shipping_id']]['rate'] = $shipping_cost;
                }
            }
        }
    }
    fn_update_cart_by_data($cart, $params, $customer_auth);
    $cart['order_id'] = $order_id;
      

    $force_notification = fn_get_notification_rules($params);
      
      
    list($order_id, $action) = fn_cp_place_order_manually($cart, $_REQUEST, $customer_auth, 'save', $auth['user_id'], $force_notification);
      
    return $order_id;
}

function fn_cp_edost_improvement_add_log($order_id, $description)
{
    $req_data = array(
        'order_id' => $order_id,
        'label' => 'cp_edost_improvement.change_shipping_cost',
        'description' => $description,
        'notice' => ''
    );
    $put_data = array(
        'controller' => 'orders',
        'mode' => '',
        'method' => 'post',
        'timestamp' => time(),
        'user_id' => !empty(Tygh::$app['session']['auth']['user_id']) ? Tygh::$app['session']['auth']['user_id'] : 0,
        'object_id' => $order_id,
        'request' => json_encode($req_data)
    );
    fn_cp_megalog_ml_add_log($put_data);
}
function fn_cp_edost_improvement_save_edost_data($edost_data)
{
    if (!empty($edost_data['order_id'])) {
        $order_data = [
            'order_id' => $edost_data['order_id'],
            'type' => 'E', // edost
            'data' => serialize($edost_data)
        ];

        db_query("REPLACE INTO ?:order_data ?e", $order_data);
    }
}
function fn_cp_edost_improvement_get_edost_data_for_order($order_id)
{
    $data = db_get_field("SELECT data FROM ?:order_data WHERE order_id = ?i AND type = ?s", $order_id, 'E');

    return !empty($data) ? unserialize($data) : [];
}
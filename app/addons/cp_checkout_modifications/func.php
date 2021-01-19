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
use Tygh\Addons\CpCheckoutModifications\Documents\Order\CpUser;
use Tygh\Enum\ProfileFieldSections;
use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }
/*HOOKS*/
function fn_cp_checkout_modifications_checkout_place_order_before_check_amount_in_stock(&$cart, $auth, $params, $cart_id, $product, $_is_edp)
{   
    if (!empty($cart['products']) && !empty($params['cp_current_checkout_order_number'])) {
        $cart['cp_current_checkout_order_number'] = $params['cp_current_checkout_order_number'];
    }
}
function fn_cp_checkout_modifications_place_order($order_id, $action, $order_status, $cart, $auth)
{   
    $session = &Tygh::$app['session'];
    $cp_is_place_all_orders = Tygh::$app['session']['is_place_all_orders'];

    if (!empty($order_id) && !empty($cart['cp_current_checkout_order_number']) && $cp_is_place_all_orders) {
        $session['cp_completed_orders'][$order_id] = $cart['cp_current_checkout_order_number'];
    }
}
function fn_cp_checkout_modifications_order_placement_routines($order_id, $force_notification, $order_info, $_error, &$redirect_url, $allow_external_redirect)
{   
    $cart_service = Tygh::$app['addons.cp_direct_payments.cart.service'];
    $carts = &$cart_service->getCarts();

    if (Tygh::$app['session']['is_place_all_orders'] && !empty($carts) && count($carts) > 1 && !empty($order_id)) {
        $redirect_url = "checkout.place_all_orders&completed_order=".$order_id;
    }elseif (!empty($order_id)) {
        $redirect_url = "orders.search&completed_order=".$order_id; 
    }
}
function fn_cp_checkout_modifications_shippings_get_shippings_list_conditions($group, $shippings, &$fields, $join, $condition, $order_by)
{
    $fields[] = '?:shippings.cp_is_door_delivery';
}
function fn_cp_checkout_modifications_template_document_order_context_init(&$document, &$order) {

	$shipping_ids = $order->data['shipping_ids'];
	$order->data['cp_is_delivery_to_TC'] = db_get_field("SELECT `cp_is_delivery_to_TC` FROM ?:shippings WHERE shipping_id = ?i", $shipping_ids);
}
function fn_cp_checkout_modifications_update_profile($action, $user_data, $current_user_data)
{
    if ($action == 'add') {
        if (AREA != 'A') {
            if (Registry::get('settings.General.approve_user_profiles') != 'Y') {
                
                /** @var \Tygh\Notifications\EventDispatcher $event_dispatcher */
                $event_dispatcher = Tygh::$app['event.dispatcher'];

                // Notify administrator about new profile
                $event_dispatcher->dispatch('profile.added', ['user_data' => $user_data]);
            }
        }
    }
}
/*HOOKS*/
function fn_cp_checkout_modifications_add_custom_profile_fields($context)
{
   $user = new CpUser($context);

   $cp_profile_fields = array(
      'legal_address' => $user->legal_address,
      's_office' => $user->office);
   
   return $cp_profile_fields;
}
function fn_cp_checkout_modifications_get_exclude_fields($profile_fields, $only_additions = false)
{
    $exclude_fields = [];
    
    $section = ProfileFieldSections::CONTACT_INFORMATION;
    
    if (empty($profile_fields) || empty($profile_fields[$section])) {
        return $exclude_fields;
    }
    
    $avaialbale_fields = [
        'activity_field',
        'company_phone',
        'company_email',
        'edo_availability',
    ];
    
    foreach ($profile_fields[$section] as $key => $field) {
        if (!empty($only_additions) && !in_array($field['field_name'], $avaialbale_fields)) {
            $exclude_fields[] = $field['field_name'];
        }
        elseif (empty($only_additions) && in_array($field['field_name'], $avaialbale_fields)) {
            $exclude_fields[] = $field['field_name'];
        }
    }
    
    return !empty($exclude_fields) ? $exclude_fields : [];
}

function fn_cp_checkout_modifications_get_exclude_fields_by_section($profile_fields, $profile_section = '')
{
    $exclude_fields = [];
    
    $section = ProfileFieldSections::CONTACT_INFORMATION;
    
    if (empty($profile_fields) || empty($profile_fields[$section])) {
        return $exclude_fields;
    }
    
    foreach ($profile_fields[$section] as $key => $field) {
        if ($field['cp_profile_section'] != $profile_section) {
            $exclude_fields[] = $field['field_name'];
        }
    }
    
    return !empty($exclude_fields) ? $exclude_fields : [];
}
function fn_cp_warehouse_selection(&$shipping_data, $warehouse_id)
{   
    foreach ($shipping_data['data']['stores'] as $store_id => $store_data) {
        if ($store_id != $warehouse_id) {
            unset($shipping_data['data']['stores'][$store_id]);
        }
    } 
}
function fn_cp_view_order_complete_notification($order_id)
{
    $compeleted_order_data = fn_get_order_info($order_id);
        
    if (!empty($compeleted_order_data)) {
        $first_product = current($compeleted_order_data['products']);
        $warehouse_data = !empty($first_product['extra']['warehouse_data']) ? unserialize($first_product['extra']['warehouse_data']) : null;
        $warehouse_address = !empty($warehouse_data['pickup_address']) ? $warehouse_data['pickup_address'] : null;

        Tygh::$app['view']->assign('order_info',$compeleted_order_data);
        Tygh::$app['view']->assign('cp_popup_order_completed', true);
        Tygh::$app['view']->assign('warehouse_address',$warehouse_address);
        $msg = Tygh::$app['view']->fetch('addons/cp_checkout_modifications/blocks/checkout/components/completed_order_popup.tpl');
        fn_set_notification('I', __('order_completed'), $msg, 'I');
    }
}
function fn_cp_check_permission($order_id) 
{   
    $auth = &Tygh::$app['session']['auth'];

    if (!empty($auth['user_id'])) {
        $check = db_get_field("SELECT order_id FROM ?:orders WHERE order_id = ?i AND user_id = ?i", $order_id, $auth['user_id']);
        if (!empty($check)) {
            return true;
        }else {
            return false;
        }
    }
}
<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

defined('BOOTSTRAP') or die('Access denied');

use Tygh\Addons\CpDirectPayments\Cart\Service;
use Tygh\BlockManager\Layout;
use Tygh\BlockManager\Location;
use Tygh\Enum\Addons\CpDirectPayments\OrderDataTypes;
use Tygh\Enum\VendorPayoutApprovalStatuses;
use Tygh\Enum\VendorPayoutTypes;
use Tygh\Registry;
use Tygh\SmartyEngine\Core;
use Tygh\Tools\Url;
use Tygh\VendorPayouts;


/**
 * Hook handler: sets company condition when storing cart info.
 */
function fn_cp_direct_payments_user_session_products_condition($params, &$conditions)
{
    $check = Registry::get('no_delete_products_from_cart',true);
    $check =false;
    if($check){
        $conditions['user_id'] = db_quote('user_id = ?i', -1);
    }

    else {
        if ($vendor_id = Registry::get('runtime.direct_payments.cart.vendor_id')) {
            $conditions['company_id'] = db_quote('company_id = ?i', $vendor_id);
        }
    }

    $cart_service = Tygh::$app['addons.cp_direct_payments.cart.service'];
    $vendor_id = $cart_service->getCurrentVendorId();
    $warehouse_id = fn_cp_direct_helper_separate_warehouse_id($vendor_id);

    if(empty($warehouse_id)) {
        $warehouse_id = Registry::get('cp_add_warehouse_id_to_get_cart');
    }

    if($warehouse_id){
        $conditions['warehouse_id'] = db_quote('warehouse_id = ?i', $warehouse_id);
    }
}


function fn_cp_direct_payments_extract_cart(&$cart, $user_id, $type, $user_type){

    $cart_service = Tygh::$app['addons.cp_direct_payments.cart.service'];
    $vendor_id = $cart_service->getCurrentVendorId();
    if(empty($vendor_id)  or empty($cart['products'])){
        return false;
    }

    $splitted_ids_company_warehouse = explode("_",$vendor_id);
    if(is_array($splitted_ids_company_warehouse) && count($splitted_ids_company_warehouse) ==2){
        $vendor_id = $splitted_ids_company_warehouse[0];
        $ware_house_id = $splitted_ids_company_warehouse[1];
        foreach ($cart['products'] as $kk =>$item){
            if(!empty($item['extra']['warehouse_id']) && $item['extra']['warehouse_id'] != $ware_house_id){
               // unset($cart['products'][$kk]);
            }
        }
    }

}

function fn_cp_direct_payments_save_cart_content_post(    $cart, $user_id, $type, $user_type){

    $check = Registry::get('no_delete_products_from_cart',true);

    $check = true;
    if($check){
        return false;
    }


    /*
    $cart_service = Tygh::$app['addons.cp_direct_payments.cart.service'];
    $carts = & $cart_service->getCarts();

    if (!empty($user_id)) {
        $condition = fn_user_session_products_condition(array(
            'user_id' => $user_id,
            'type' => $type,
            'user_type' => $user_type,
            'get_session_user_id' => false,
        ));
        db_query('DELETE FROM ?:user_session_products WHERE 1=1 AND ?p', $condition);
        Registry::set('no_delete_products_from_cart',true);
        foreach ($carts as $vendor_id => &$cart) {
            fn_save_cart_content($cart, $user_id);
        }
        Registry::set('no_delete_products_from_cart',false);
    }
    */
}

/*
 * we need to save warehouse ID
 */
function fn_cp_direct_payments_place_order($order_id, $action, $order_status, $cart, $auth){

    if($order_id){
        $cart_service = Tygh::$app['addons.cp_direct_payments.cart.service'];
        $current_vendor_id =$cart_service->getCurrentVendorId();
        $warehouse_id =0;

        if(!empty($current_vendor_id)){
            $warehouse_id = fn_cp_direct_helper_separate_warehouse_id($current_vendor_id);
        }

        db_query("UPDATE ?:orders SET cp_warehouse_id =?i WHERE order_id =?i",$warehouse_id,$order_id);
    }
}

function fn_cp_direct_payments_calculate_cart_post(&$cart, $auth, $calculate_shipping, $calculate_taxes, $options_style, $apply_cart_promotions, $cart_products, $product_groups){


   if(!empty($cart['products'])){

       foreach ($cart['products'] as $key =>$product){

           $product['extra']['warehouse_data'] = array();

           if(!empty($product['extra']['warehouse_id'])){

               list($locations,) =  fn_get_store_locations(['store_location_id' => array($product['extra']['warehouse_id'])], 0, CART_LANGUAGE);



               if(!empty($locations)){
                 $location  = current($locations);

                   if(!empty($location)){
                       $location =  serialize($location);

                       $cart['products'][$key]['extra']['warehouse_data'] = $location;
                   }
               }
           }
       }
   }
}


function fn_cp_direct_helper_separate_warehouse_id($vendor_id){
    $splitted_ids_company_warehouse = explode("_",$vendor_id);


    $warehouse_id=0;
    if(is_array($splitted_ids_company_warehouse) && count($splitted_ids_company_warehouse) ==2) {

        $warehouse_id = $splitted_ids_company_warehouse[1];
    }
    else {


    }

    return $warehouse_id;
}



function fn_cp_direct_helper_separate_ids($vendor_id){
    $splitted_ids_company_warehouse = explode("_",$vendor_id);


    if(is_array($splitted_ids_company_warehouse) && count($splitted_ids_company_warehouse) ==2) {

        $vendor_id = $splitted_ids_company_warehouse[0];
    }
    else {

        $vendor_id = (int)$vendor_id;
    }

    return $vendor_id;
}


function fn_cp_direct_payments_install()
{
    db_query('UPDATE ?:vendor_payouts SET payment_company_id = company_id WHERE order_id <> 0');
}

/**
 * Removes direct_payments layout pages.
 */
function fn_cp_direct_payments_uninstall()
{
    $layouts = Layout::instance()->getList();

    foreach ($layouts as $layout_data) {

        $location_manager = Location::instance($layout_data['layout_id']);

        $location_data = $location_manager->getList([
            'dispatch' => Url::buildUrn(['separate_checkout', 'cart']),
        ]);
        if (!$location_data) {
            continue;
        }
        $location_data = reset($location_data);

        $location_manager->remove($location_data['location_id']);
    }
}

/**
 * Hook handlers: modifies checkout payment methods fetching.
 */
function fn_cp_direct_payments_prepare_checkout_payment_methods_before_get_payments(
    $cart,
    $auth,
    $lang_code,
    $get_payment_groups,
    $payment_methods,
    &$get_payments_params
)
{
    if (isset($auth['user_type'])
        && $auth['user_type'] === 'V'
    ) {
        $vendor_id = 0;
    } else {
        $vendor_id = isset($cart['vendor_id']) ? $cart['vendor_id'] : null;
    }

    $get_payments_params['company_id'] = $vendor_id;
}

/**
 * Hook handlers: modifies checkout payment methods fetching.
 */
function fn_cp_direct_payments_prepare_checkout_payment_methods_after_get_payments(
    $cart,
    $auth,
    $lang_code,
    $get_payment_groups,
    &$payment_methods,
    $get_payments_params,
    $cache_key
)
{
    if (empty($payment_methods[$cache_key])) {
        $get_payments_params['company_id'] = 0;
        $payment_methods[$cache_key] = fn_get_payments($get_payments_params);
    }
}

/**
 * Provides mini cart data for 'Cart content' block.
 *
 * @return array Cart content
 */
function fn_cp_direct_payments_get_mini_cart()
{
    /** @var \Tygh\Web\Session $session */
    $session = Tygh::$app['session'];

    $cart = array(
        'amount'           => 0,
        'display_subtotal' => 0,
        'products'         => array(),
        'vendor_ids'       => array(),
        'vendor_id'        => 0,
    );

    if ($session->isStarted()) {
        /** @var \Tygh\Addons\CpDirectPayments\Cart\Service $cart_service */
        $cart_service = Tygh::$app['addons.cp_direct_payments.cart.service'];

        foreach ($cart_service->getCarts() as $vendor_id => $vendor_cart) {
            if (fn_cart_is_empty($vendor_cart)) {
                continue;
            }




            if(!empty($vendor_cart['taxes'])){

                foreach ($vendor_cart['taxes'] as $k_t =>$taxe_data){

                    if(!isset($taxe_data['tax_subtotal']) or !isset($taxe_data['rate_type'])){
                        continue;
                    }
                    if(!isset($cart['taxes'][$k_t]['tax_subtotal'])){
                        $cart['taxes'][$k_t]['tax_subtotal'] =0;
                    }

                    $cart['taxes'][$k_t]['tax_subtotal'] =$cart['taxes'][$k_t]['tax_subtotal'] + $taxe_data['tax_subtotal'];

                    $cart['taxes'][$k_t]['rate_type'] =$taxe_data['rate_type'];

                    $cart['taxes'][$k_t]['rate_value'] =$taxe_data['rate_value'];

                }
            }



            if (isset($vendor_cart['amount'])) {
                $cart['amount'] += $vendor_cart['amount'];
            }

            if (isset($vendor_cart['display_subtotal'])) {
                $cart['display_subtotal'] += $vendor_cart['display_subtotal'];
            }

            if (isset($vendor_cart['products'])) {
                $cart['products'] += $vendor_cart['products'];
            }

            $cart['vendor_ids'][] = $vendor_id;
        }
    }

    //lemuria cart-powert special save session to display summ

    Tygh::$app['session']['cp_global_cart_values'] = $cart;

    return $cart;
}

/**
 * Stores current vendor_id in runtime.
 *
 * @param int $id Vendor ID
 */
function fn_cp_direct_payments_set_runtime_vendor($id)
{
    /** @var \Tygh\Addons\CpDirectPayments\Cart\Service $cart_service */
    $cart_service = Tygh::$app['addons.cp_direct_payments.cart.service'];

    $cart_service->setRuntimeVendorId($id);
}

/**
 * Checks whether payment is owned by a vendor.
 *
 * @param int       $vendor_id Vendor ID
 * @param array|int $payment   Payment data or payment ID
 *
 * @return bool
 */
function fn_cp_direct_payments_check_payment_owner($vendor_id, $payment)
{
    if ($vendor_id === null) {
        $vendor_id = Registry::get('runtime.company_id');
    }

    if (!$vendor_id || !$payment) {
        return true;
    }

    if (is_numeric($payment)) {
        $payment = fn_get_payment_method_data($payment, DESCR_SL);
    }

    return $payment['company_id'] == $vendor_id;
}

/**
 * Checks whether promotion is owned by a vendor.
 *
 * @param int       $vendor_id Vendor ID
 * @param array|int $promotion Promotion data or payment ID
 *
 * @return bool
 */
function fn_cp_direct_payments_check_promotion_owner($vendor_id, $promotion)
{
    if ($vendor_id === null) {
        $vendor_id = Registry::get('runtime.company_id');
    }

    if (!$vendor_id || !$promotion) {
        return true;
    }

    if (is_numeric($promotion)) {
        $promotion = fn_get_promotion_data($promotion);
    }

    return $promotion['company_id'] == $vendor_id;
}

/**
 * Hook handler: replaces 'checkout' controller with the 'separate_checkout' one.
 */
function fn_cp_direct_payments_get_route_runtime(
    $req,
    $area,
    $result,
    $is_allowed_url,
    &$controller,
    $mode,
    $action,
    $dispatch_extra,
    $current_url_params,
    $current_url
)
{
    $rewrite_modes = [
        'add'                 => true,
        'update'              => true,
        'shipping_estimation' => true,
        'cart'                => true,
    ];

    if ($controller == 'checkout' && !empty($rewrite_modes[$mode])) {
        $controller = 'separate_checkout';
        $_REQUEST['dispatch'] = 'separate_' . $_REQUEST['dispatch'];
    }
}

/**
 * Hook handler: adds company filtering for payments.
 */
function fn_cp_direct_payments_get_payments(&$params, $fields, $join, $order, &$condition, $having)
{
    if (AREA === 'A' && !isset($params['company_id'])) {
        $params['company_id'] = (int) Registry::get('runtime.company_id');
    }

    if (isset($params['company_id'])) {
        $condition[] = db_quote('?:payments.company_id = ?i', $params['company_id']);
    }
}

/**
 * Hook handler: adds company filtering for promotions.
 */
function fn_cp_direct_payments_get_promotions(&$params, $fields, $sortings, &$condition, $join, $group, $lang_code)
{
    if (AREA === 'A') {
        $params['company_id'] = (int) Registry::get('runtime.company_id');
    }

    if ($vendor_id = Registry::get('runtime.cp_direct_payments.cart.vendor_id')) {
        $params['company_id'] = $vendor_id;
    }

    if (isset($params['company_id'])) {
        $condition .= db_quote(' AND ?:promotions.company_id = ?i', $params['company_id']);
    }
}

/**
 * Hook handler: properly populates cart info on login.
 */
function fn_cp_direct_payments_user_init($auth, $user_info, $first_init)
{
    //lemuria hard hook RESET
    if(AREA == "A"){
        return true;
    }

    /** @var \Tygh\Addons\CpDirectPayments\Cart\Service $cart_service */
    $cart_service = Tygh::$app['addons.cp_direct_payments.cart.service'];

    if (!$first_init) {
        return;
    }

    $user_type = empty($auth['user_id'])
        ? 'U'
        : 'R';
    $current_user_id = fn_get_session_data('cu_id');
    $user_id = empty($auth['user_id'])
        ? $current_user_id
        : $auth['user_id'];

    //lemuria
    //Registry::set('cp_delete_wrong_warehouse_date_products',true);

    $cart_service->load($user_id, 'C', $user_type);


    //зачем сохранять ?
    /*
    if (!empty($user_id)) {
        $condition = fn_user_session_products_condition(array(
            'user_id' => $user_id,
            'type' => "C",
            'user_type' => $user_type,
            'get_session_user_id' => false,
        ));
        db_query('DELETE FROM ?:user_session_products WHERE 1=1 AND ?p', $condition);
        Registry::set('no_delete_products_from_cart', true);
    }
    */

   // $cart_service->save($user_id, 'C', $user_type);

    $user_data = fn_get_user_info($user_id);
    $cart_service->setUserData($user_data);
}

/**
 * Hook handler: properly populates cart info on login.
 */
function fn_cp_direct_payments_init_user_session_data(&$sess_data, $user_id)
{
    /** @var \Tygh\Addons\CpDirectPayments\Cart\Service $cart_service */
    $cart_service = Tygh::$app['addons.cp_direct_payments.cart.service'];
    $cccart =  Tygh::$app['session']['cart'];
    $cart_service->load($user_id, 'C');

    if (AREA == 'C') {
        $cart_service->save($user_id);
    }

    $user_data = fn_get_user_info($user_id);
    $cart_service->setUserData($user_data);

    $sess_data['product_notifications']['email'] = !empty($user_data['email'])
        ? $user_data['email']
        : '';
}

/**
 * Hook handler: sets company condition when extracting cart info.
 */
function fn_cp_direct_payments_pre_extract_cart($cart, &$condition, $item_types)
{
    if (isset($cart['vendor_id'])) {
       // $condition .= db_quote(' AND company_id = ?i', $cart['vendor_id']);
    }

    $warehouse_id =       Registry::get('cp_add_warehouse_id_to_get_cart');
    if($warehouse_id){
        $condition .= db_quote(' AND warehouse_id = ?i', $warehouse_id);
    }

}

/**
 * Hook handler: sets company condition when storing cart info.
 */
function fn_cp_direct_payments_save_cart_content_pre($cart, $user_id, $type, $user_type)
{
    if (isset($cart['vendor_id'])) {
        fn_cp_direct_payments_set_runtime_vendor($cart['vendor_id']);
    }
}

/**
 * Hook handler: sets company ID when storing cart info.
 */
function fn_cp_direct_payments_save_cart_content_before_save($cart, $user_id, $type, $user_type, &$product_data)
{


    if (isset($cart['vendor_id'])) {

        //lemuria cart-power 17.12.2020
        $splitted_ids_company_warehouse = explode("_",$cart['vendor_id']);
        if(is_array($splitted_ids_company_warehouse) && count($splitted_ids_company_warehouse) ==2) {
            $cart['vendor_id'] = $splitted_ids_company_warehouse[0];
        }

        $product_data['company_id'] = $cart['vendor_id'];
    }


    $cart_service = Tygh::$app['addons.cp_direct_payments.cart.service'];
    $vendor_id = $cart_service->getCurrentVendorId();
    $warehouse_id = fn_cp_direct_helper_separate_warehouse_id($vendor_id);
    if($warehouse_id){
        $product_data['warehouse_id'] = $warehouse_id;

    }

    //вобщем тут все очень странно. из за того что модуль cs-cart был в бета версии то в нем изначально было много багов
    //с корзиной.
    //данный хук нужен чтобы товары из сессии нормально (с правильным вархауз айди) записывались в базу
    //и потом оттуда доставались
    //этот хук не может работать нормально когда происходит авторизация с товаром в корзине и есть еще брошенная корзина
    //поэтому данное условие будет срабатывать как переопределение финальное компани и вархаус айди.

    if(!empty($product_data) && !empty($product_data['extra'])){
        $extra = unserialize($product_data['extra']);
        if(!empty($extra['extra']['warehouse_id'])){
            $product_data['company_id'] = $extra['company_id'];
        }

        if(!empty($extra['extra']['warehouse_id'])){
            $product_data['warehouse_id'] = $extra['extra']['warehouse_id'];
        }

        //нужно удалить запись навсякий самим. так как фунция удаления срабаьывает не всегда точно
        if(!empty($user_id)) {
            db_query("DELETE FROM ?:user_session_products 
WHERE user_id =?i and product_id =?i and company_id =?i and warehouse_id = ?i", $user_id, $product_data['product_id'], $product_data['company_id'], $product_data['warehouse_id']);
        }
    }
}


/**
 * Hook handler: resets promotions cache when switching vendors on cart calculation.
 */
function fn_cp_direct_payments_promotion_apply_before_get_promotions(
    $zone,
    $data,
    $auth,
    $cart_products,
    &$promotions,
    $applied_promotions
)
{
    static $cache = array();

    if (!empty($data['company_id'])) {
        $company_id = $data['company_id'];
        /** @var \Tygh\Addons\CpDirectPayments\Cart\Service $cart_service */
        $cart_service = Tygh::$app['addons.cp_direct_payments.cart.service'];
        $cart_service->setRuntimeVendorId($company_id);
    } else {
        $company_id = Registry::get('runtime.cp_direct_payments.cart.vendor_id');
    }

    foreach ($promotions as $zone => $zone_promotions) {
        foreach ($zone_promotions as $promotion_id => $promotion) {
            $cache[$promotion['company_id']][$zone][$promotion_id] = $promotion;
        }
    }

    if (isset($cache[$company_id][$zone])) {
        $promotions[$zone] = $cache[$company_id][$zone];
    } else {
        unset($promotions[$zone]);
    }
}

/**
 * Hook handler: creates vendor payout for the paid order.
 */
function fn_cp_direct_payments_change_order_status(
    $status_to,
    $status_from,
    $order_info,
    $force_notification,
    $order_statuses,
    $place_order
) {
    if ($order_statuses[$status_to]['params']['inventory'] === 'I'
        || empty($order_info['company_id'])
        || !empty($order_info['is_commission_payout_requested'])
    ) {
        return;
    }

    $payouts_manager = VendorPayouts::instance(array('vendor' => $order_info['company_id']));

    $order_payout = $payouts_manager->getSimple(array(
        'order_id'    => $order_info['order_id'],
        'payout_type' => VendorPayoutTypes::ORDER_PLACED,
    ));
    if (!$order_payout) {
        return;
    }

    $order_payout = reset($order_payout);

    if (!isset($order_payout['commission_amount'])) {
        $order_payout['commission_amount'] = 0;
    }

    $payouts = array();
    $is_vendor_payment = fn_cp_direct_payments_check_payment_owner($order_info['company_id'], $order_info['payment_id']);

    if ($is_vendor_payment) {
        $payouts[] = array(
            'payout_type'     => VendorPayoutTypes::WITHDRAWAL,
            'payout_amount'   => $order_payout['order_amount'],
            'comments'        => '',
            'company_id'      => $order_info['company_id'],
            'order_id'        => $order_info['order_id'],
            'approval_status' => VendorPayoutApprovalStatuses::COMPLETED,
        );
    }

    foreach ($payouts as $payout_params) {
        $payouts_manager->update($payout_params);
    }

    // mark payout as requested
    db_replace_into('order_data', array(
        'order_id' => $order_info['order_id'],
        'type'     => OrderDataTypes::PAYOUT_REQUEST,
        'data'     => serialize(true),
    ));
}

/**
 * Checks wheter the Vendor plans add-on is installed.
 *
 * @return bool
 */
function fn_cp_direct_payments_is_vendor_plans_addon_installed()
{
    static $has_vendor_plans;
    if ($has_vendor_plans === null) {
        $has_vendor_plans = Registry::ifGet('addons.vendor_plans', null) !== null;
    }

    return $has_vendor_plans;
}

/**
 * Hook handler: sets company ID when creating/updating payment.
 */
function fn_cp_direct_payments_update_payment_pre(
    &$payment_data,
    $payment_id,
    $lang_code,
    $certificate_file,
    $certificates_dir
)
{
    $company_id = (int) Registry::get('runtime.company_id');
    if (!$company_id && isset($payment_data['company_id'])) {
        $company_id = (int) ($payment_data['company_id']);
    }

    $payment_data['company_id'] = $company_id;
}

/**
 * Hook handler: sets company ID when creating/updating shipping.
 */
function fn_cp_direct_payments_update_shipping(&$shipping_data, $shipping_id, $lang_code)
{
    if (!$shipping_id || !empty($shipping_data['company_id'])) {
        $shipping_data['company_id'] = (int) Registry::get('runtime.company_id');
    }
}

/**
 * Hook handler: prevents administrator from seeing/editing vendor shipping methods.
 */
function fn_cp_direct_payments_get_available_shippings($company_id, $fields, $join, &$condition)
{
    if (!$company_id) {
        $condition = db_quote('a.company_id = ?i', 0);
    }
}

/**
 * Hook handler: sets order payout request status.
 *
 * @param array $order           Order info
 * @param array $additional_data Additional order data
 */
function fn_cp_direct_payments_get_order_info(&$order, &$additional_data)
{
    if (!empty($additional_data[OrderDataTypes::PAYOUT_REQUEST])) {
        $order['is_commission_payout_requested'] = unserialize($additional_data[OrderDataTypes::PAYOUT_REQUEST]);
    }
}

/**
 * Hook handler: sets company ID when creating/updating promotion.
 */
function fn_cp_direct_payments_update_promotion_pre(&$data, $promotion_id, $lang_code)
{
    $data['company_id'] = (int) Registry::get('runtime.company_id');
}

/**
 * Hook handler: manipulates with surcharge value for payout calculation
 */
function fn_cp_direct_payments_vendor_plans_calculate_commission_for_payout_before($order_info, $company_data, $payout_data, $total, $shipping_cost, $surcharge_from_total, &$surcharge_to_commission, $commission)
{
    /**
     * Since all payments now belong to vendor, we need:
     * 1. To leave "$surcharge_from_total" as is, to be subtracted from order total, because we do not want to give away some part of money that vendor may have to pay to payment service
     * 2. To set "$surcharge_to_commission" to zero, because we do not want the payment surcharge be included to payout
     */
    $surcharge_to_commission = 0;
}

/**
 * Hook handler: adds payment company ID for the order payout.
 */
function fn_cp_direct_payments_vendor_payouts_update($instance, &$data, $payout_id, $action)
{
    if (!empty($data['order_id'])) {
        $order_info = fn_get_order_info($data['order_id']);
        if (isset($order_info['payment_method']['company_id'])) {
            $data['payment_company_id'] = $order_info['payment_method']['company_id'];
        } else {
            $data['payment_company_id'] = 0;
        }
    }
}

/**
 * Hook handler: Saves cart when loggin user out.
 */
function fn_cp_direct_payments_user_logout_before_save_cart($auth, &$save_cart)
{
    $save_cart = false;

    /** @var \Tygh\Addons\CpDirectPayments\Cart\Service $cart_service */
    $cart_service = Tygh::$app['addons.cp_direct_payments.cart.service'];
    $cart_service->save($auth['user_id']);

}

/**
 * Hook handler: Clears cart when loggin user out.
 */
function fn_cp_direct_payments_user_logout_before_clear_cart($auth, &$clear_cart)
{
    $clear_cart = false;

    /** @var \Tygh\Addons\CpDirectPayments\Cart\Service $cart_service */
    $cart_service = Tygh::$app['addons.cp_direct_payments.cart.service'];
    $cart_service->clear(false, true);
}

/**
 * Populates data that is used in checkout templates.
 *
 * @param \Tygh\Addons\CpDirectPayments\Cart\Service $cart_service
 * @param \Tygh\SmartyEngine\Core                  $view
 * @param array                                    $auth
 *
 * @internal
 */
function fn_cp_direct_payments_bootstrap_checkout_data(Service $cart_service, Core $view, array $auth)
{
    $active_carts = $vendor_ids = $vendors = [];

    $carts = &$cart_service->getCarts();
    foreach ($carts as $vendor_id => &$vendor_cart) {
        if (!fn_cart_is_empty($vendor_cart)) {
            $active_carts[$vendor_id] = $vendor_cart;
            if ($vendor_id) {
                $vendor_ids[$vendor_id] = $vendor_id;
            }
        }
    }

    if (!empty($vendor_ids)) {
        list($vendors) = fn_get_companies(['company_id' => $vendor_ids], $auth);
        $vendors = fn_array_value_to_key($vendors, 'company_id');
    }

    $view->assign([
        'cart_is_separate_checkout' => true,
        'carts'                     => $active_carts,
        'vendors'                   => $vendors,
    ]);
}

/**
 * Hook handler: updates user data in all carts when updating user data on checkout.
 */
function fn_cp_direct_payments_checkout_update_user_data_post($cart, $auth, $user_data, $ship_to_another, $user_id)
{
    /** @var \Tygh\Addons\CpDirectPayments\Cart\Service $cart_service */
    $cart_service = Tygh::$app['addons.cp_direct_payments.cart.service'];

    $cart_service->setUserData($cart['user_data']);
}

/**
 * Hook handler: get available payment methods for vendor
 */
function fn_cp_direct_payments_prepare_repay_data($payment_id, $order_info, $auth, &$payment_methods)
{
    /** @var \Tygh\Addons\CpDirectPayments\Cart\Service $cart_service */
    $cart_service = Tygh::$app['addons.cp_direct_payments.cart.service'];

    $vendor_id = null;
    if (!empty($_REQUEST['order_id'])) {
        $vendor_id = $cart_service->getVendorIdByOrderId($_REQUEST['order_id']);
    }

    $payment_methods = fn_get_payments([
        'usergroup_ids' => $auth['usergroup_ids'],
        'extend' => ['images'],
        'company_id' => $vendor_id
    ]);

    if (empty($payment_methods)) {
        $payment_methods = fn_get_payments([
            'usergroup_ids' => $auth['usergroup_ids'],
            'extend' => ['images'],
            'company_id' => 0
        ]);
    }
}


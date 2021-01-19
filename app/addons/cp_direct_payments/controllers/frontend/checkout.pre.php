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

/** @var \Tygh\Addons\CpDirectPayments\Cart\Service $cart_service */
$cart_service = Tygh::$app['addons.cp_direct_payments.cart.service'];


/*
 * We should find vendor with the biggest order total amount and redirect vendot to this
 */

if($mode =='place_all_orders') {

    /*gMelnikov modifs*/
    if (!empty($_REQUEST['completed_order']) && fn_cp_check_permission($_REQUEST['completed_order'])) {
        fn_cp_view_order_complete_notification($_REQUEST['completed_order']);
    }
    /*gMelnikov modifs*/
    
    
    $carts = &$cart_service->getCarts();
    $biggest_vendor_cart_total_by_warehouse = 0;
    $redirect_vendor_id  =0;

    foreach ($carts as $vendor_id => &$cart) {

        if (!isset($cart['vendor_id'])) {
            continue;
        }

        if (!isset($cart['total'])) {
            continue;
        }

        if($cart['total']  > $biggest_vendor_cart_total_by_warehouse){
            $biggest_vendor_cart_total_by_warehouse = $cart['total'];

            $redirect_vendor_id = $vendor_id;
        }
    }
    
    if($redirect_vendor_id){
        Tygh::$app['session']['is_place_all_orders'] = true;
    }
    
    return [CONTROLLER_STATUS_REDIRECT, 'checkout.checkout?vendor_id='.$redirect_vendor_id];

}


if($mode =='update_steps'){

    //$_REQUEST['vendor_id'] = $cart_service->getCurrentVendorId();

    if(!isset($_REQUEST['vendor_id'])) {
        $_REQUEST['vendor_id'] = $cart_service->getCurrentVendorId();
    }
}


if($mode =='checkout'){




    $carts = & $cart_service->getCarts();

    foreach ($carts as $key =>$cart) {
       if(empty($cart['products'])){
           unset($carts[$key]);
       }
    }

    if(!isset($_REQUEST['vendor_id'])) {
        $_REQUEST['vendor_id'] = $cart_service->getCurrentVendorId();
    }

}
$carts = & $cart_service->getCarts();




if (isset($_REQUEST['vendor_id'])) {


    $splitted_ids_company_warehouse = explode("_",$_REQUEST['vendor_id']);
    if(is_array($splitted_ids_company_warehouse) && count($splitted_ids_company_warehouse) ==2) {

        $vendor_id = $_REQUEST['vendor_id'];
    }
    else{
        $vendor_id = (int)$_REQUEST['vendor_id'];
    }
        $cart_service->setCurrentVendorId($vendor_id);
}

/**
 * Store current cart in the session to remove the need to override controllers from another add-ons
 */
Tygh::$app['session']['cart'] = $cart_service->getCart();

if($mode == 'checkout'){



    if (!empty($_REQUEST['vendor_id'])) {
        $cart_vendor_id = $_REQUEST['vendor_id'];
    }

    $vendor_carts = $cart_service->getCarts();
    
    if(empty( Tygh::$app['session']['cp_user_data_was_sync'][$cart_vendor_id]) 
        || Tygh::$app['session']['cp_user_data_was_sync'][$cart_vendor_id] != true 
        || (!empty($vendor_carts) && empty($vendor_carts[$cart_vendor_id]['user_data']))){

        $auth = Tygh::$app['session']['auth'];
        if(!empty($auth['user_id'])){


            $user_data = fn_get_user_info($auth['user_id']);
            $cart_service->setUserData($user_data);
            Tygh::$app['session']['cp_user_data_was_sync'][$cart_vendor_id] = true;
        }       
    }
}



if ($mode === 'clear') {

    $carts = & $cart_service->getCarts();
    $complete = false;
    $clear_all = false;

    $vendor_id = 0;
    if(!empty($_REQUEST['vendor_id'])){
        $vendor_id =$_REQUEST['vendor_id'];
    }


    foreach ($carts as $vendor_cart_key => &$cart) {
        if($vendor_id > 0 && $vendor_cart_key !=$vendor_id){
            continue;
        }
        fn_clear_cart($cart, $complete, $clear_all);
    }
    return [CONTROLLER_STATUS_REDIRECT, 'checkout.cart'];
}

return [CONTROLLER_STATUS_OK];

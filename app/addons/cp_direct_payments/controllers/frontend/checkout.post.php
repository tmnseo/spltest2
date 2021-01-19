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

/** @var \Tygh\SmartyEngine\Core $view */
$view = Tygh::$app['view'];

/** @var array $auth */

if ($mode === 'checkout') {
    $view->assign([
        'vendor_id' => $cart_service->getCurrentVendorId(),
    ]);
    
}

if($mode =='complete'){

    $is_customer_want_to_place_al_orders = false;

    if(isset(Tygh::$app['session']['is_place_all_orders'])){
        $is_customer_want_to_place_al_orders = Tygh::$app['session']['is_place_all_orders'];
    }

    $view = Tygh::$app['view'];

    $view->assign('is_customer_want_to_place_al_orders', $is_customer_want_to_place_al_orders);
}






fn_cp_direct_payments_bootstrap_checkout_data($cart_service, $view, $auth);

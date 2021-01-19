<?php

use Tygh\Registry;

/** @var string $mode */
if ($mode == 'new') {
    $order_id = $_REQUEST['order_id'];
    $order = fn_get_order_info($order_id);

    $products = [];

    $user_id = $_SESSION['auth']['user_id'];

    if ($user_id != $order['user_id']) {
        $view = Registry::get('view')->assign('error', 'Ошибка заказа');
        return;
    }

    foreach ($order['products'] as $product) {
        $st_product = new stdClass();
        $st_product->id = $product['item_id'];
        $st_product->name = $product['product'];
        $st_product->price = $product['subtotal'];
        $st_product->amout = $product['amount'];
        $products[] = $st_product;
    }


    $view = Registry::get('view')->assign(
        'all_data',
        [
            'products'       => $products,
            'order_id'       => $order_id,
            'order_sum'      => $order['total'], // todo возможнно subtotal
            'products_count' => count($products),
            'seller_name'    => $order['shipping'][0]['group_name'], // todo возможнно из другого места следует брать
            'shipping_price' => $order['display_shipping_cost'],

        ]
    );
}
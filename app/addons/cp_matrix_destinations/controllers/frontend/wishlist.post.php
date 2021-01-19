<?php

use Tygh\Addons\CpMatrixDestinations\ServiceProvider;

if ($mode == 'view') {

    if(empty(Tygh::$app['session']['cp_user_has_defined_city'])){
        return true;
    }

    $user_city_id = Tygh::$app['session']['cp_user_has_defined_city'];

    $products = Tygh::$app['view']->getTemplateVars('products');

    $matrix_model = ServiceProvider::getMatrix();

    //[extra] => Array
    // (
    // [warehouse_id]

    foreach ($products as $key => $product) {

        if(empty($product['extra']['warehouse_id'])){
            continue;
        }
        $products[$key]['extra_warehouse_data'][$product['extra']['warehouse_id']]['time_average'] = $matrix_model->findDeliveryForWarehouse($product,$user_city_id);
    }


    $products = $matrix_model->recalculateDeliveryDate($products,CART_LANGUAGE);

    Tygh::$app['view']->assign('products', $products);

}


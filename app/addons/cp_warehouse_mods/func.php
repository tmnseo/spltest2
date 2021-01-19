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

use Tygh\Addons\Warehouses\Manager;
use Tygh\Addons\Warehouses\ProductWarehouse;
use Tygh\Addons\Warehouses\ServiceProvider;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_cp_warehouse_mods_reorder_product($order_info, $cart, $auth, $product, $amount, &$price, $zero_price_action, $k){
    
    if (!empty($product['extra']['warehouse_id'])) {
        $price = fn_cp_warehouse_products_prices_get_product_price($product['product_id'], $product['extra']['warehouse_id']);
    }

}

function fn_cp_warehouse_mods_get_products($params, $fields, $sortings, &$condition, &$join, $sorting, $group_by, $lang_code, $having){


    $need_to_add_warehouse_condition = Registry::get('cp_warehouse_stock_condition');

    if($need_to_add_warehouse_condition && empty($params['cp_np_type'])){
        $join.= db_quote( " INNER JOIN ?:warehouses_products_amount as warehouse_cp ON warehouse_cp.product_id = products.product_id");
        $condition.= db_quote(' AND warehouse_cp.amount > 0');
    }


    

}


function fn_cp_warehouse_mods_get_products_before_select( $params,
                                                          $join,
                                                          $condition,
                                                          $u_condition,
                                                          $inventory_join_cond,
                                                          $sortings,
                                                          $total,
                                                          $items_per_page,
                                                          $lang_code,
                                                          $having){


    if(AREA =="C"){

        Registry::set('cp_warehouse_stock_condition',true);
        Registry::set('settings.General.show_out_of_stock_products',"Y");
    }
}


function fn_cp_warehouse_mods_get_order_info (&$order, $additional_data){


    $warehouse_data = [];

    if(empty($order['products'])){
        return true;
    }

    foreach ($order['products'] as $order_produt){
        if(!empty($order_produt['extra']['warehouse_data'])){
            $warehouse_data_unserialize = unserialize($order_produt['extra']['warehouse_data']);
            $warehouse_data[$warehouse_data_unserialize['store_location_id']] = $warehouse_data_unserialize;
        }
    }
    
    $order['warehouse_data_points'] =$warehouse_data;
}

function fn_cp_warehouses_get_availability_summary($product_id, $destination_id, $lang_code = CART_LANGUAGE)
{


    $summary = [
        'in_stock_stores_count'   => null,
        'available_stores_count'  => null,
        'warn_about_delay'        => false,
        'shipping_delay'          => null,
        'show_stock_availability' => false,
        'product_id'              => $product_id,
        'grouped_stores'          => null,
    ];

    /** @var Tygh\Addons\CpWarehouses\Manager $stock_manager */
    $stock_manager = Tygh::$app['addons.cpwarehouses.manager'];
    $product_stock = $stock_manager->getProductWarehousesStock($product_id);
    if (!$product_stock->hasStockSplitByWarehouses()) {
        return $summary;
    }

    // stores that are shown in the customer's destination



    $stores = $product_stock->getWarehousesForPickupInDestination($destination_id);


    $store_ids = array_map(function(ProductWarehouse $store) {
        return $store->getWarehouseId();
    }, $stores);
    list($locations,) = fn_get_store_locations(['store_location_id' => $store_ids], 0, $lang_code);

    // amount of stores where the product is available right now
    $in_stock_stores_count = 0;
    // amount of stores where the product can be purchased
    $available_stores_count = 0;
    // whether customer must be warned about shipping delay in his destination
    $warn_about_delay = false;
    // shipping delay to show to customer
    $shipping_delay = null;
    // whether stock availability block must be shown
    $show_stock_availability = false;
    // stores where the product can be picked up
    $grouped_stores = [];
    $city_ids = [];
    foreach ($stores as $store) {
        // shipping delay details
        $store_warn_about_delay = false;
        $store_shipping_delay = null;
        $store_is_available = $store->getAmount() > 0;
        $store_destination_id = $store->getMainDestinationId();
        foreach ($product_stock->getWarehousesThatShipToStore($store) as $fallback) {
            $is_fallback_prioritized = $fallback->getPosition($store_destination_id) < $store->getPosition($store_destination_id);

            if ($is_fallback_prioritized && $fallback->isWarnAboutDelay($store_destination_id)) {
                $warn_about_delay = true;
                $shipping_delay = $fallback->getShippingDelay($store_destination_id);
            }

            if (!$store_is_available) {
                if ($fallback->isWarnAboutDelay($store_destination_id)) {
                    $store_warn_about_delay = true;
                }
                if ($fallback->getShippingDelay($store_destination_id)) {
                    $store_shipping_delay = $fallback->getShippingDelay($store_destination_id);
                }
            }

            $store_is_available = true;
            break;
        }

        $store_id = $store->getWarehouseId();
        $location_data = $locations[$store_id];
        $store_city = $location_data['city'];
        if (!isset($city_ids[$location_data['city']])) {
            $city_ids[$store_city] = count($city_ids);
            $grouped_stores[$city_ids[$store_city]] = [
                'name'  => $store_city,
                'items' => [],
            ];
        }

        $grouped_stores[$city_ids[$store_city]]['items'][$store_id] = [
            'store_location_id' => $store_id,
            'name'              => $location_data['name'],
            'description'       => $location_data['description'],
            'latitude'          => $location_data['latitude'],
            'longitude'         => $location_data['longitude'],
            'pickup_address'    => $location_data['pickup_address'],
            'pickup_time'       => $location_data['pickup_time'],
            'pickup_phone'      => $location_data['pickup_phone'],
            'amount'            => $store->getAmount(),
            'is_available'      => $store_is_available,
            'shipping_delay'    => $store_shipping_delay,
        ];

        if ($store->getAmount() > 0) {
            $in_stock_stores_count++;
        }
        if ($store_is_available) {
            $available_stores_count += (int) $store_is_available;
        }
        $warn_about_delay = $warn_about_delay || $store_warn_about_delay;
        $shipping_delay = $shipping_delay ?: $store_shipping_delay;
        $show_stock_availability = $show_stock_availability || $store_is_available;
    }

    if (!$stores) {
        foreach ($product_stock->getWarehousesForShippingInDestination($destination_id) as $fallback) {
            if ($fallback->getAmount() > 0) {

                if ($fallback->isWarnAboutDelay($destination_id)) {
                    $warn_about_delay = true;
                    $shipping_delay = $fallback->getShippingDelay($destination_id);
                }

                break;
            }
        }
    }

    $summary['in_stock_stores_count'] = $in_stock_stores_count;
    $summary['available_stores_count'] = $available_stores_count;
    $summary['warn_about_delay'] = $warn_about_delay;
    $summary['shipping_delay'] = $shipping_delay;
    $summary['show_stock_availability'] = $show_stock_availability;
    $summary['grouped_stores'] = $grouped_stores;

    return $summary;
}



function fn_cp_warehouses_blocks_get_availability_in_stores()
{
    $params = array_merge([
        'product_id' => null,
    ], $_REQUEST);

    if (!$params['product_id']) {
        return [];
    }

    /** @var \Tygh\Location\Manager $manager */
    $location_manager = Tygh::$app['location'];
    $destination_id = $location_manager->getDestinationId();
    $availability = fn_cp_warehouses_get_availability_summary($params['product_id'], $destination_id);



    if (!$availability) {
        return $availability;
    }

    return $availability['grouped_stores'];
}










function fn_cp_warehouse_mods_generate_cart_id(&$_cid, &$extra, $only_selectable){
    if(!empty($extra['warehouse_id'])){
        $_cid[] = $extra['warehouse_id'];
    }
}


function fn_cp_warehouse_mods_get_product_data_post (&$product_data, $auth, $preview, $lang_code){



    if (empty($product_data['product_id'])) {
        return;
    }

    /** @var Tygh\Addons\Warehouses\Manager $manager */
    $manager = Tygh::$app['addons.warehouses.manager'];
    /** @var Tygh\Addons\Warehouses\ProductStock $product_stock */
    $product_stock = $manager->getProductWarehousesStock($product_data['product_id']);

    if (!$product_stock->hasStockSplitByWarehouses()) {
        return;
    }

    if (AREA == 'C') {
        /** @var \Tygh\Location\Manager $manager */

        $product_data['amount'] = $product_stock->getAmount();
    }

}
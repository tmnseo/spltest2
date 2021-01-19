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

if (!defined('BOOTSTRAP')) { die('Access denied'); }
use Tygh\Addons\CpWarehouses\ServiceProvider;

Tygh::$app->register(new ServiceProvider());

fn_register_hooks(
    'reorder_product',
    'get_products',
    'get_products_before_select',
    'get_order_info',
    'get_product_data_post',
    'generate_cart_id'

);
/*
fn_register_hooks(
    'update_product_post',
    'get_product_data_post',
    'update_product_amount',
    'update_product_amount_pre',
    'get_store_locations_before_select',
    'check_amount_in_stock_before_check',
    'gather_additional_products_data_pre',
    'gather_additional_products_data_post',
    'get_store_locations_for_shipping_before_select',
    'delete_destinations_post',
    'store_locator_delete_store_location_post',
    'store_locator_get_store_location_post',
    'store_locator_update_store_location_before_update'
);
*/
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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

fn_register_hooks(
    'change_order_status',
    'delete_order',
//     'delete_shipping',
//     'delete_category_after',
//     'delete_shipments',
//     'delete_company',
//     'delete_page',
//     'delete_banners',
//     'delete_feature_post',
//     'delete_product_filter_post',
//     'discussion_delete_post_post',
    'place_order'
//     'update_shipping_post',
//     'update_company',
//     'update_category_post',
//     'update_page_post',
//     'update_product_feature_post',
//     'update_product_filter',
//     'update_user_pre',
//     'create_shipment_post',
);
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

$schema = array(
    'cp_megalog' => array(
        'clear_logs' => array(
            'label' => true,
        )
    ),
    'orders' => array(
        'delete' => array(
            'label' => true,
            'prefix' => __('order'),
        ), 
        /*'update_details' => array(
            'object_link' => 'orders.details?order_id='
        ),*/
        'update_status' => array(
            'prefix' => __('order'),
            'label' => true,
            'label_langvar' => true,
            'object_link' => 'orders.details?order_id='
        ),
    ),
    'checkout' => array(
        'place_order' => array(
            'prefix' => __('order'),
            'label' => true,
            'object_link' => 'orders.details?order_id='
        ),
    ),
    /*
    'shipments' => array(
        'add' => array(
            'prefix' => __('shipment'),
            'object_link' => 'shipments.details?shipment_id='
        ),
        'delete' => array(
            'prefix' => __('shipments'),
            'parced_object_ids' => 'delete_ids'
        ),
    ),    
    'categories' => array(
        'update' => array(
            'prefix' => __('category'),
            'object_link' => 'categories.update?category_id='
        ),
        'delete' => array(
            'prefix' => __('category'),
        ),
    ),
    'shippings' => array(
        'update' => array(
            'prefix' => __('shipping'),
            'label' => true,
            'object_link' => 'shippings.update?shipping_id='
        ),
        'delete' => array(
            'prefix' => __('shipping'),
        ),
    ),
    'companies' => array(
        'update' => array(
            'prefix' => __('vendor'),
            'object_link' => 'companies.update?company_id='
        ),
        'update_status' => array(
            'prefix' => __('vendor'),
            'label' => true,
            'object_link' => 'companies.update?company_id='
        ),
        'delete' => array(
            'prefix' => __('vendor'),
        ),
    ),
    'pages' => array(
        'update' => array(
            'prefix' => __("page") . '/' . __("blog"),
            'object_link' => 'pages.update?page_id='
        ),
        'delete' => array(
            'prefix' => __("page") . '/' . __("blog"),
        ),
    ),
    'banners' => array(
        'update' => array(
            'prefix' => __('banner'),
            'object_link' => 'banners.update?banner_id='
        ),
        'delete' => array(
            'prefix' => __('banner'),
        ),
    ),
    'reviews' => array(
        'approve' => array(
            'prefix' => __('discussion_title_product'),
            'object_link' => 'discussion_manager.manage?post_id='
        ),
        'disapprove' => array(
            'prefix' => __('discussion_title_product'),
            'object_link' => 'discussion_manager.manage?post_id='
        ),
        'delete' => array(
            'prefix' => __('discussion_title_product'),
        ),
    ),
    'product_features' => array(
        'update' => array(
            'prefix' => __('feature'),
            'object_link' => 'product_features.manage?feature_id='
        ),
        'delete' => array(
            'prefix' => __('feature'),
        ),
    ),
    'product_filters' => array(
        'update' => array(
            'prefix' => __('filter'),
            'object_link' => 'product_filters.manage?filter_id='
        ),
        'delete' => array(
            'prefix' => __('filter'),
        ),
    ),
    'tools' => array(
        'update_status' => array(
            'label' => true,
        ),
    ),
    'profiles' => array(
        'update' => array(
            'prefix' => __('user'),
            'object_link' => 'profiles.update?user_id='
        ),
        'delete' => array(
            'prefix' => __('user'),
        ),
    ),
    */
);
return $schema;

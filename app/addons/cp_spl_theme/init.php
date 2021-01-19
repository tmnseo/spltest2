<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

fn_register_hooks(
    'get_products',
    'get_static_data',
    'create_order_details',
    'pre_place_order',
    'gather_additional_products_data_post',
    'get_product_feature_variants',
    'get_products_before_select',
    'cp_pre_communication_update_thread'
);
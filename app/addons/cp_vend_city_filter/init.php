<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

fn_register_hooks(
    'get_product_filter_fields',
    'generate_filter_field_params',
    'get_products',
    'get_filters_products_count_post'
);
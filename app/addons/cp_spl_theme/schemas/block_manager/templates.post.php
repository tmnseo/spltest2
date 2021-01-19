<?php

$schema['addons/cp_spl_theme/blocks/our_brands_flex.tpl'] = array (
    'settings' => array(
        'item_quantity' =>  array (
            'type' => 'input',
            'default_value' => 5
        ),
        'thumbnail_width_flex' =>  array (
            'type' => 'input',
            'default_value' => 183
        ),
        'thumbnail_height_flex' =>  array (
            'type' => 'input',
            'default_value' => 93
        ),
        'filter_id' =>  array (
            'type' => 'input'
        ),
    ),
);
$schema['blocks/products/products_scroller.tpl']['bulk_modifier']['fn_gather_additional_products_data']['params'] = array (
    'is_cp_product_block' => true,
    'get_icon' => true,
    'get_detailed' => true,
    'get_options' => true,
);
return $schema;

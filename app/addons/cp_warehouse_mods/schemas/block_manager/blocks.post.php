<?php

defined('BOOTSTRAP') or die('Access denied');



if (isset($_REQUEST['warehouse_id'])) {
    unset($schema['availability_in_stores']);
}else {
    $schema['availability_in_stores'] = [
        'show_on_locations' => ['product_tabs', 'products.view'],
        'templates'         => 'addons/warehouses/blocks/availability_in_stores.tpl',
        'content'           => [
            'items' => [
                'type'     => 'function',
                'function' => [
                    /** @see \fn_warehouses_blocks_get_availability_in_stores */
                    'fn_cp_warehouses_blocks_get_availability_in_stores',
                ],
            ],
        ],
        'cache'             => [
            'request_handlers'  => ['product_id'],
            'update_handlers'   => [
                'products',
                'store_location_destination_links',
                'store_location_shipping_delays',
                'warehouses_products_amount',
                'store_locations',
            ],
            'callable_handlers' => [
                'customer_location_hash' => [
                    /** @see \fn_warehouses_blocks_get_customer_location_hash */
                    'fn_warehouses_blocks_get_customer_location_hash',
                ],
            ],
        ],
    ];
}

return $schema;

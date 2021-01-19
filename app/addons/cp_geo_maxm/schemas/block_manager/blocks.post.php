<?php


$schema['cp_geo_maxm_customer_location'] = [
    'templates' => 'addons/cp_geo_maxm/blocks/customer_location.tpl',
    'wrappers' => 'blocks/wrappers',
    'content' => [
        'location' => [
            'type' => 'function',
            'function' => ['fn_cp_geo_maxm_get_customer_stored_geolocation'],
        ],
        'location_detected' => [
            'type' => 'function',
            'function' => ['fn_cp_geo_maxm_is_customer_location_detected'],
        ],


        'autocomplete_data' => [
            'type' => 'function',
            'function' => ['fn_cp_geo_maxm_define_autocomplete'],
        ],
    ],
    'cache' => false
];

return $schema;
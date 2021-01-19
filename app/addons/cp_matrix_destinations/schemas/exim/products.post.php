<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }


include_once __DIR__ . '/products.functions.php';


$schema['post_processing']['cp_matrix_destinations'] = [
    'function'    => 'fn_cp_matrix_destinations_exim_post_processing',
    'args'        => ['$primary_object_ids', '$import_data', '$processed_data', '$final_import_notification'],
    'import_only' => true,
];

return $schema;
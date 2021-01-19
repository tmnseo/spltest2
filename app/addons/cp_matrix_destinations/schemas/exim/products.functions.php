<?php

use Tygh\Registry;
use Tygh\Addons\CpMatrixDestinations\ServiceProvider;
use Tygh\Application;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_cp_matrix_destinations_exim_post_processing($primary_object_ids, $import_data, $processed_data, &$final_import_notification){

    $needed_store_ids = db_get_fields("SELECT warehouse_id  FROM ?:warehouses_products_amount WHERE city_id =?i",0);

    foreach ($needed_store_ids as $needed_store_id){
        $matrix_model = ServiceProvider::getMatrix();
        $matrix_model->updateWarehouseAmountsetCityId($needed_store_id);
    }
}
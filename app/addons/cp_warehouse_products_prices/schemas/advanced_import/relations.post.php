<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

$schema['products']['cp_warehouses_price'] = [
    'description'        => 'cp_warehouse_products_prices.warehouse_price',
    'items_function'     => 'fn_warehouses_exim_get_list',
    'aggregate_field'    => 'Advanced Import: Warehouse price',
    'aggregate_function' => 'fn_cp_warehouses_exim_aggregate_prices',
];

return $schema;
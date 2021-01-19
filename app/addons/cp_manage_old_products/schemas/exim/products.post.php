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

if (!defined('BOOTSTRAP')) { die('Access denied'); }


include_once __DIR__ . '/products.functions.php';

$schema['pre_processing']['cp_manage_old_products'] = [
    'function'    => 'fn_cp_manage_old_products_exim_pre_processing',
    'args'        => ['@preset'],
    'import_only' => true,
];
$schema['import_process_data']['cp_manage_old_products'] = [
    'function'    => 'fn_cp_manage_old_products_exim_process',
    'args'        => ['$primary_object_id'],
    'import_only' => true,
];
$schema['post_processing']['cp_manage_old_products'] = [
    'function'    => 'fn_cp_manage_old_products_exim_post_processing',
    'args'        => ['$primary_object_ids', '@preset'],
    'import_only' => true,
];

return $schema;
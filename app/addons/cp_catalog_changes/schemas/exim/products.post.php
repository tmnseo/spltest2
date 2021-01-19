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

defined('BOOTSTRAP') or die('Access denied');

use Tygh\Registry;

/** @var array $schema */

require_once Registry::get('config.dir.addons') . 'cp_catalog_changes/schemas/exim/products.functions.php';

if (fn_allowed_for('MULTIVENDOR')) {
    
    $schema['import_get_primary_object_id']['cp_catalog_changes_get_product_code'] = [
        'function'    => 'fn_cp_catalog_changes_get_exim_product_code',
        'args'        => ['$alt_keys', '$object', '$skip_get_primary_object_id'],
        'import_only' => true,
    ];
    
}

return $schema;

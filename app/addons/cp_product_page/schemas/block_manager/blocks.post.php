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

use Tygh\Registry;

$schema['product_filters']['content']['items']['fillings']['manually']['params']['request']['cp_np_prod_id'] = '%PRODUCT_ID%';
$schema['product_filters']['cache']['request_handlers'][] = 'cp_np_prod_id';
if (Registry::get('addons.warehouses.status') == 'A') {
    $schema['product_filters']['cache']['update_handlers'][] = 'warehouses_products_amount';
}
if (Registry::get('addons.cp_warehouse_products_prices.status') == 'A') {
    $schema['product_filters']['cache']['update_handlers'][] = 'cp_warehouse_products_prices';
}
$schema['cp_np_most_blocks'] = array (
    'content' => array (
        'items' => array (
            'remove_indent' => true,
            'hide_label' => true,
            'type' => 'enum',
            'object' => 'products',
            'items_function' => 'fn_cp_np_get_mosts_products',
            'fillings' => array (
                'cp_np_from_current' => array (
                    'params' => array (
                        'request' => array(
                            'product_id' => '%PRODUCT_ID%',
                            'features_hash' => '%FEATURES_HASH%',
                            'cp_cur_wh_id' => '%WAREHOUSE_ID%'
                        ),
                    )
                ),
            ),
        ),
    ),
    'templates' => 'addons/cp_product_page/blocks/product_most_blocks.tpl',
    'wrappers' => 'blocks/wrappers'
);

return $schema;
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

$schema['product_extended'] = array(
    'class' => '\Tygh\Template\Document\Variables\GenericVariable',
    'data' => function (\Tygh\Template\Snippet\Table\ItemContext $context) {
        $product = $context->getItem();
        $manufacturer_code_id = Registry::get('addons.cp_change_inv.manufacturer_code_id');
        $manufacturer_id = Registry::get('addons.cp_change_inv.manufacturer_id');
        $product_extended['manufacturer_code'] = db_get_field("SELECT vd.variant FROM ?:product_features_values as v LEFT JOIN ?:product_feature_variant_descriptions as vd ON v.variant_id = vd.variant_id WHERE v.product_id = ?i AND v.feature_id = ?i AND vd.lang_code = ?s", $product['product_id'], $manufacturer_code_id, CART_LANGUAGE);
        $product_extended['manufacturer'] = db_get_field("SELECT vd.variant FROM ?:product_features_values as v LEFT JOIN ?:product_feature_variant_descriptions as vd ON v.variant_id = vd.variant_id WHERE v.product_id = ?i AND v.feature_id = ?i AND vd.lang_code = ?s", $product['product_id'], $manufacturer_id, CART_LANGUAGE);
        $product_extended['product_number'] = $product['cp_product_number'];
        
        $formatter = Tygh::$app['formatter'];
        $product_extended['cp_price'] = $formatter->asPrice($product['cp_price']);
        
        return $product_extended;
    },
    'arguments' => array('#context', '#config', '@formatter'),
    'attributes' => array(
        'product_number',
        //'brand',
        'manufacturer_code',
        'manufacturer',
        'cp_price'
    ),
    'alias' => 'cp_p',
);

return $schema;
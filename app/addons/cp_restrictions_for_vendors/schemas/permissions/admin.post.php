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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

$schema['categories'] = array(
    'modes' => array(
        'manage' => array(
            'permissions' => 'cp_categories_view',
        )
    ),
);
$schema['product_features'] = array(
    'modes' => array(
        'manage' => array(
            'permissions' => 'cp_features_manage',
        ),
        'update' => array(
            'permissions' => 'cp_features_manage',
        ),
        'apply' => array(
            'permissions' => 'cp_features_manage',
        ),
        'delete' => array(
            'permissions' => 'cp_features_manage',
        ),
    ),
);
$schema['product_filters'] = array(
    'modes' => array(
        'manage' => array(
            'permissions' => 'cp_filters_manage',
        ),
        'update' => array(
            'permissions' => 'cp_filters_manage',
        ),
        'apply' => array(
            'permissions' => 'cp_filters_manage',
        ),
        'delete' => array(
            'permissions' => 'cp_filters_manage',
        ),
    ),
);
$schema['products'] = array(
    'modes' => array(
        'manage' => array(
            'permissions' => 'cp_products_view',
        ),
        'm_update' => array(
            'permissions' => 'cp_products_manage',
        ),
        'global_update' => array(
            'permissions' => 'cp_products_manage',
        ),
        'update' => array(
            'permissions' => array(
                'GET' => 'cp_products_view',
                'POST' => 'cp_products_manage'
            ),
        ),
        'add' => array(
            'permissions' => 'cp_products_manage',
        ),
        'm_add' => array(
            'permissions' => 'cp_products_manage',
        ),
        'p_subscr' => array(
            'permissions' => 'cp_products_manage',
        ),
        'export_found' => array(
            'permissions' => 'cp_products_manage',
        ),
        'apply' => array(
            'permissions' => 'cp_products_manage',
        ),
        'delete' => array(
            'permissions' => 'cp_products_manage',
        ),
        'clone' => array(
            'permissions' => 'cp_products_manage',
        ),
    ),
);


if (Registry::get('addons.product_variations.status') == 'A') {
    $schema['product_variations'] = array(
        'permissions' => 'cp_products_manage',
        'modes' => array(
            'manage' => array(
                'permissions' => 'cp_products_view',
            )
        )
    );
}
$schema['attachments'] = array (
    'permissions' => array ('GET' => true, 'POST' => true),
        'modes' => array (
            'delete' => array (
                'permissions' => true
            )
        ),
    );
$schema['tools']['modes']['update_status']['param_permissions']['table']['attachments'] = true;
return $schema;
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

$addon_settings = Registry::get('addons.cp_restrictions_for_vendors');

if (empty($addon_settings['restrict_logs']) || $addon_settings['restrict_logs'] == 'Y') {
    $schema['controllers']['logs'] = array(
        'modes' => array(
            'clean' => array(
                'permissions' => array(
                    'POST' => false,
                    'GET' => false, 
                ), 
            ),
            'manage' => array(
                'permissions' => array(
                    'POST' => true,
                    'GET' => true, 
                ), 
            ), 
        )
    );
}

if (empty($addon_settings['restrict_files']) || $addon_settings['restrict_files'] == 'Y') {
    $schema['controllers']['file_editor'] = array(
        'permissions' => array(
            'GET' => false,
            'POST' => false
        )
    );
}

if (empty($addon_settings['restrict_exim']) || $addon_settings['restrict_exim'] == 'Y') {
    $schema['controllers']['exim'] = array(
        'permissions' => array(
            'GET' => false,
            'POST' => false
        )
    );
}
if (empty($addon_settings['restrict_blog_and_pages']) || $addon_settings['restrict_blog_and_pages'] == 'Y') {
    $schema['controllers']['pages'] = array(
        'permissions' => array(
            'GET' => false,
            'POST' => false
        )
    );
}
if (empty($addon_settings['restrict_product_options']) || $addon_settings['restrict_product_options'] == 'Y') {
    $schema['controllers']['product_options'] = array(
        'permissions' => array(
            'GET' => false,
            'POST' => false
        )
    );
}
if (empty($addon_settings['restrict_product_features']) || $addon_settings['restrict_product_features'] == 'Y') {
    $schema['controllers']['product_features'] = array(
        'permissions' => array(
            'GET' => false,
            'POST' => false
        )
    );
    if (Registry::get('runtime.controller') == 'product_features' && (Registry::get('runtime.mode') == 'get_variants_list' || Registry::get('runtime.mode') == 'get_features_list')) {
        $schema['controllers']['product_features'] = array(
            'modes' => array(
                'get_features_list' => array(
                    'permissions' => true,
                ),
                'get_feature_variants_list' => array(
                    'permissions' => true,
                ) ,
                'get_variants_list' => array(
                    'permissions' => true,
                ) ,
                'get_variants' => array(
                    'permissions' => array(
                        'GET' => true,
                        'POST' => false,
                    ) ,
                ) ,
            )
        );
  
    }
}
if (empty($addon_settings['restrict_product_filters']) || $addon_settings['restrict_product_filters'] == 'Y') {
    $schema['controllers']['product_filters'] = array(
        'permissions' => array(
            'GET' => false,
            'POST' => false
        )
    );
}
$schema['controllers']['shipments'] = array(
    'permissions' => array(
        'GET' => false,
        'POST' => false
    )
);
$schema['controllers']['call_requests'] = array(
    'permissions' => array(
        'GET' => false,
        'POST' => false
    )
);
$schema['controllers']['currencies']['modes']['manage'] = array(
    'permissions' => array(
        'GET' => false,
        'POST' => false
    )
);
/*$schema['controllers']['profiles']['modes']['update'] = array(
    'permissions' => array(
        'GET' => false,
        'POST' => false
    )
);*/
return $schema;
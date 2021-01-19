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

if (Registry::get('runtime.company_id')) {
    $cur_company_id = Registry::get('runtime.company_id');

    $schema['central']['customers']['items']['vendor_administrators'] = array();
    $schema['central']['cp_vp_storefront'] = array(
        'items' => array(
            'cp_vendor' => array(
                'href' => 'companies.update?company_id=' . $cur_company_id,
                'position' => 100,
            ),
        ),
        'position' => 700,
    );
    $schema['central']['vendors']['items']['vendors'] = array();
    $schema['central']['vendors']['items']['vendor_administrators'] = array(
        'href' => 'profiles.manage?user_type=V',
        'alt' => 'profiles.update?user_type=V',
        'position' => 250,
    );
    if (Registry::get('addons.store_locator.status') == 'A') {
        $schema['top']['administration']['items']['store_locator'] = array();
        
        $schema['central']['cp_vp_storefront']['items']['store_locator'] = array(
            'attrs' => [
                'class' => 'is-addon'
            ],
            'href' => 'store_locator.manage',
            'position' => 400
        );
    }
} else {
    $schema['central']['vendors']['items']['cp_vp_waranty_cat'] = array(
        'href' => 'cp_waranty_cat.manage',
        'position' => 800,
        'attrs' => array(
            'class' => 'is-addon'
        ),
    );
}
return $schema;

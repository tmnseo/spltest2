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

if (Registry::get('addons.cp_vendor_panel.status') == 'A' && Registry::get('runtime.company_id')) {
    
    $schema['central']['cp_vp_storefront']['items']['store_locator']['subitems'] = array(
        'store_locator' => array(
            'href' => 'store_locator.manage',
            'position' => 110
        ),
        'cp_premoderation_warehouses' => array(
            'href' => 'cp_warehouses_premoderation.manage',
            'position' => 110
        ),
        'cp_disapprove_warehouses' => array(
            'href' => 'cp_warehouses_premoderation.disapprove_manage',
            'position' => 110
        ),
    );
}else {

    $schema['top']['administration']['items']['store_locator']['subitems'] = array(
        'store_locator' => array(
            'href' => 'store_locator.manage',
            'position' => 110
        ),
        'cp_premoderation_warehouses' => array(
            'href' => 'cp_warehouses_premoderation.manage',
            'position' => 110
        ),
        'cp_disapprove_warehouses' => array(
            'href' => 'cp_warehouses_premoderation.disapprove_manage',
            'position' => 110
        ),
    );
}

return $schema;
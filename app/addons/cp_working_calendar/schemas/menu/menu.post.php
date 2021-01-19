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

$company_id = Registry::get('runtime.company_id');

if (!empty($company_id)) {
    $prefix = "?company_id=" . $company_id;
}else {
    $prefix = "?main_calendar=1";
}

$menu = array(
    'position' => 700,
    'attrs' => array(
        'class'=>'is-addon'
    ),
    'subitems' => array(
        'cp_working_calendar.manage' => array(
            'href' => 'cp_working_calendar.manage',
            'position' => 110
        ),
        'cp_working_calendar.update' => array(
            'href' => 'cp_working_calendar.update' . $prefix,
            'position' => 120
        ),
    ),
    'href' => 'cp_working_calendar.update' . $prefix,
);

if (Registry::get('addons.cp_vendor_panel.status') == 'A' && !empty($company_id)) {
    
    $schema['central']['cp_vp_storefront']['items']['cp_working_calendar'] = $menu;

}else {

    $schema['central']['website']['items']['cp_working_calendar'] = $menu;
}

return $schema;
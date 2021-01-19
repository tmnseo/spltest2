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

/*HOOKS*/
function fn_cp_restrictions_for_vendors_dispatch_before_display()
{
    if (Tygh::$app['session']['auth']['user_type'] == 'V' && Registry::get('runtime.controller') == 'categories' && Registry::get('runtime.mode') == 'update') {
        $tabs = Registry::get('navigation.tabs');
        if (!empty($tabs)) {
            foreach ($tabs as $tab_id => $tab_data) {
                if ($tab_id !== 'detailed') {
                    unset($tabs[$tab_id]);
                }
            }
            Registry::set('navigation.tabs', $tabs);
        }
    }
}
/*HOOKS*/
function fn_cp_unset_restriction_privileges(&$group_data)
{
  $addon_settings = Registry::get('addons.cp_restrictions_for_vendors');

    foreach ($group_data as $privilede_id => $privilege_data) {
        if (!empty($privilede_id) && in_array($privilede_id, CP_RESTRICTION_MODES) && (empty($addon_settings['restrict_'.$privilede_id]) || $addon_settings['restrict_'.$privilede_id] == 'Y')) {
            unset($group_data[$privilede_id]);
        }
    }
}

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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    return;
}

if ($mode == 'update') {

    $usergroup = Tygh::$app['view']->getTemplateVars('usergroup');

    if (empty($usergroup['type']) || $usergroup['type'] != 'V') {
        return ;
    }

    $grouped_privileges = Tygh::$app['view']->getTemplateVars('grouped_privileges');
    
    foreach ($grouped_privileges as $group_id => &$group_data) {
        fn_cp_unset_restriction_privileges($group_data);

        if (empty($grouped_privileges[$group_id])) {
            unset($grouped_privileges[$group_id]);
        }
    }
    
    Tygh::$app['view']->assign('grouped_privileges', $grouped_privileges);
}
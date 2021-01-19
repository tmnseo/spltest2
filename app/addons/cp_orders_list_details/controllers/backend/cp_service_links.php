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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode == 'add') {
        fn_cp_oc_update_service_links(array($_REQUEST['service_data']));
    }
    if ($mode == 'update') {
        fn_cp_oc_update_service_links($_REQUEST['services']);
    }
    return array(CONTROLLER_STATUS_OK, 'cp_service_links.manage');
}
if ($mode == 'manage') {
    
    $params = $_REQUEST;
    list($links, $avail_services) = fn_cp_oc_get_service_links($params);
    
    Tygh::$app['view']->assign('links', $links);
    Tygh::$app['view']->assign('avail_services', $avail_services);
    
} elseif ($mode == 'delete') {
    if (!empty($_REQUEST['module'])) {
        fn_cp_oc_delete_service_link($_REQUEST['module']);
    }
    return array(CONTROLLER_STATUS_REDIRECT, "cp_service_links.manage");
}

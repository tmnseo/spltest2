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
    
        fn_trusted_vars('category_data');
        $suffix = '';
        if ($mode == 'm_update') {
            if (!empty($_REQUEST['categories'])) {
                foreach($_REQUEST['categories'] as $category_id => $category_data) {
                    if (!empty($category_id)) {
                        fn_cp_vp_update_warranty_cat($category_data, $category_id, DESCR_SL);
                    }
                }
            }
            $suffix = '.manage';
        }
        if ($mode == 'm_delete') {
            if (!empty($_REQUEST['category_ids'])) {
                fn_cp_vp_delete_warranty_category($_REQUEST['category_ids']);
            }
            $suffix = '.manage';
        }
        if ($mode == 'update') {
            $suffix = '.manage';
             if (!empty($_REQUEST['category_data'])) {
                $category_id = fn_cp_vp_update_warranty_cat($_REQUEST['category_data'], $_REQUEST['category_id'], DESCR_SL);
            }
        }
        return array(CONTROLLER_STATUS_OK, "cp_waranty_cat$suffix");
}
if ($mode == 'manage' || $mode == 'picker') {
    
    $params = $_REQUEST;
    list($categories, $search) = fn_cp_vp_get_warranty_categories($params, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);
    
    Registry::get('view')->assign('search', $search);
    Registry::get('view')->assign('categories', $categories);
    
} elseif ($mode == 'delete') {
    if (!empty($_REQUEST['category_id'])) {
        fn_cp_vp_delete_warranty_category(array($_REQUEST['category_id']));
    }
    return array(CONTROLLER_STATUS_REDIRECT, "cp_waranty_cat.manage");
    
}
if ($mode == 'picker') {
    Registry::get('view')->display('addons/cp_vendor_panel/pickers/categories/picker_contents.tpl');
    exit;
}





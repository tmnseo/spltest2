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
    if ($mode == 'add_warranty_cats') {
        
        if (!empty($_REQUEST['company_id']) && !empty($_REQUEST['warranty_data']) && !empty($_REQUEST['warranty_data']['variant_id']) && !empty($_REQUEST['warranty_data']['categories'])) {
            fn_cp_vp_add_vendor_warranties($_REQUEST['company_id'], $_REQUEST['warranty_data']['variant_id'], $_REQUEST['warranty_data']['categories']);
        }
    }
    if ($mode == 'update_war_brands') {
        if (!empty($_REQUEST['company_id']) && !empty($_REQUEST['warranty_data'])) {
            fn_cp_vp_update_vendor_warranties($_REQUEST['company_id'], $_REQUEST['warranty_data']);
        }
    }
    if ($mode == 'cp_warranty_delete') {
        if (!empty($_REQUEST['company_id']) && !empty($_REQUEST['category_id'])) {
            $cur_company_id = Registry::get('runtime.company_id');
            if (empty($cur_company_id) || (!empty($cur_company_id) && $cur_company_id == $_REQUEST['company_id'])) {
                fn_cp_vp_delete_vendor_warranty($_REQUEST['company_id'], $_REQUEST['category_id']);
                return array(CONTROLLER_STATUS_REDIRECT, "companies.update?company_id=" . $_REQUEST['company_id'] . '&selected_section=cp_vp_brands_for_work');
            }
        }
    }
    if ($mode == 'cp_warranty_delete_brand') {
        if (!empty($_REQUEST['company_id']) && !empty($_REQUEST['variant_id'])) {
            $cur_company_id = Registry::get('runtime.company_id');
            if (empty($cur_company_id) || (!empty($cur_company_id) && $cur_company_id == $_REQUEST['company_id'])) {
                fn_cp_vp_delete_vendor_warranty_brand($_REQUEST['company_id'], $_REQUEST['variant_id']);
                return array(CONTROLLER_STATUS_REDIRECT, "companies.update?company_id=" . $_REQUEST['company_id'] . '&selected_section=cp_vp_brands_for_work');
            }
        }
    }
    return;
}

if ($mode == 'update') {
    if (!empty($_REQUEST['company_id'])) {
        Registry::set('navigation.tabs.cp_vp_certificates', array (
            'title' => __('cp_vp_certificates'),
            'js' => true
        ));
        $certificates = fn_get_attachments('vendor_cert', $_REQUEST['company_id'], 'M', DESCR_SL);
        Tygh::$app['view']->assign('cp_vp_certificates', $certificates);
        
        $brand_feature = Registry::get('addons.cp_vendor_panel.feature_brand');
        if (!empty($brand_feature)) {
            Registry::set('navigation.tabs.cp_vp_brands_for_work', array (
                'title' => __('cp_vp_brands_for_work_warranty'),
                'js' => true
            ));
            Tygh::$app['view']->assign('cp_brand_feature', $brand_feature);
        }
        $params = $_REQUEST;
        if (!isset($params['items_per_page'])) {
            $params['items_per_page'] = Registry::get('settings.Appearance.admin_elements_per_page');
        }
        $vend_warranties = fn_cp_vp_get_vendor_warranties($_REQUEST['company_id'], $params, DESCR_SL);
        Tygh::$app['view']->assign('cp_vp_warranties', $vend_warranties);
    }
}
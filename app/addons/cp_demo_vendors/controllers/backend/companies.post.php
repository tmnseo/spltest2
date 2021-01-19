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

    if ($mode == 'update_demo_status') {

        $params = $_REQUEST;
        /* 
            $params['storefront_id'] - company_id
            $params['status'] - demo_status
        */
        if (!empty($params['storefront_id']) && !empty($params['status'])) {

            $result = db_query("UPDATE ?:companies SET cp_is_demo = ?s WHERE company_id = ?i", $params['status'], $params['storefront_id']);
            if ($result){
                $admin_ids = db_get_fields("SELECT user_id FROM ?:users WHERE company_id = ?i",$params['storefront_id']);
                $usergroup_id = Registry::get('addons.cp_demo_vendors.demo_usergroup_id');
                $venodr_usergroup_id = Registry::get('addons.cp_demo_vendors.vendor_usergroup_id');
                
                foreach ($admin_ids as $user_id) {
                    if ($params['status'] == 'Y') {
                        fn_change_usergroup_status('A', $user_id, $usergroup_id);
                    }elseif ($params['status'] == 'N'){
                        fn_change_usergroup_status('F', $user_id, $usergroup_id);
                        fn_change_usergroup_status('A', $user_id, $venodr_usergroup_id);
                    }
                }
            }
                
            
        }
        if (defined('AJAX_REQUEST')) {
            Tygh::$app['ajax']->assign('result', $result);

            return [CONTROLLER_STATUS_OK, urldecode($params['return_url'])];
        }
    }
    return ;
}


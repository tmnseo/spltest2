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
function fn_cp_front_additional_info_dispatch_assign_template($controller, $mode, $area, $controllers_cascade)
{
    if ($area == 'C' && $controller == '_no_page') {
        Tygh::$app['view']->assign('layout_data',Registry::get('runtime.layout'));
    }
}
function fn_cp_front_additional_info_get_product_data_post(&$product_data, $auth, $preview, $lang_code) 
{
    if (AREA == 'C' && !empty($product_data)) {
        $feature_warranty_id = Registry::get('addons.cp_front_additional_info.feature_warranty_id');
        if (!empty($feature_warranty_id)) {
            $product_data['cp_warranty_period'] = db_get_field("SELECT value FROM ?:product_features_values WHERE product_id = ?i AND feature_id = ?i", $product_data['product_id'], $feature_warranty_id);
        }
    }
}
function fn_cp_front_additional_info_cp_auth_routines_post($status, $user_data, $user_login, $password, $salt, $request)
{
    
    if (
        AREA == 'C' 
        && !empty($password) 
        && !empty($salt)
        && !empty($user_data['password'])
        && fn_generate_salted_password($password, $salt) == $user_data['password']
        && !empty($user_data['user_type']) 
        && $user_data['user_type'] == 'V' 
        && !empty($user_data['user_id']) 
        && !empty($request['cp_area']) 
        && $request['cp_area'] == 'A') {

        $user_token = md5(uniqid(""));
        $start_time = time();
        $data = array(
           'user_id' => $user_data['user_id'],
           'user_token' => $user_token,
           'start_time' => $start_time
        );
        db_query("INSERT INTO ?:cp_storefront_redirect_tokens ?e",$data);

        fn_redirect(CP_VENDOR_LOGIN_FILE_PATH."&user_id=".$user_data['user_id']."&cp_token=".$user_token);
    }
}
/*HOOKS*/


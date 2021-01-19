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

    return;
}

if ($mode == 'cp_checkout_login_form') {

    if (defined('AJAX_REQUEST') && empty($auth['user_id'])) {

        fn_set_notification('W', __('warning'), __('cp_spl_theme.chekout_login_notification'));
        
        Tygh::$app['view']->assign([
            'return_url'   => isset($_REQUEST['return_url']) ? $_REQUEST['return_url'] : null,
            'redirect_url' => isset($_REQUEST['redirect_url']) ? $_REQUEST['redirect_url'] : null,
            'title'        => __('authorize_before_order'),
            'is_checkout'  => true,
        ]);

        

        Tygh::$app['view']->display('addons/cp_spl_theme/blocks/checkout_login_popup.tpl');
        exit;
    }

    fn_set_notification('W', __('warning'), __('authorize_before_order'));

    return [CONTROLLER_STATUS_REDIRECT, 'auth.login_form'];
}elseif ($mode == 'add_recipient') {

    if (defined("AJAX_REQUEST")) {
        Tygh::$app['view']->display('addons/cp_spl_theme/components/another_recipient_form_popup.tpl');
        exit;
    }

}elseif ($mode == 'checkout') {
    

    if (isset($_REQUEST['delete_recipient']) && isset(Tygh::$app['session']['auth']['cp_recipient_data'])) {
        unset(Tygh::$app['session']['auth']['cp_recipient_data']);
    }

    if (!empty($_REQUEST['recipient_data'])) {

        Tygh::$app['session']['auth']['cp_recipient_data'] = $_REQUEST['recipient_data'];
        Tygh::$app['view']->assign('recipient_data',$_REQUEST['recipient_data']);

    } elseif (!empty(Tygh::$app['session']['auth']['cp_recipient_data'])) {
        Tygh::$app['view']->assign('recipient_data',Tygh::$app['session']['auth']['cp_recipient_data']);
    }
}

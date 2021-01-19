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

if ($mode == 'cp_wishlist_login_form') {

    if (defined('AJAX_REQUEST') && empty($auth['user_id'])) {

        fn_set_notification('W', __('warning'), __('cp_spl_theme.wishlist_login_notification'));
        
        Tygh::$app['view']->assign([
            'return_url'   => isset($_REQUEST['return_url']) ? $_REQUEST['return_url'] : null,
            'redirect_url' => isset($_REQUEST['redirect_url']) ? $_REQUEST['redirect_url'] : null,
            'is_wishlist'  => true,
        ]);

        

        Tygh::$app['view']->display('addons/cp_spl_theme/blocks/checkout_login_popup.tpl');
        exit;
    }

    fn_set_notification('W', __('warning'), __('authorize_before_order'));

    return [CONTROLLER_STATUS_REDIRECT, 'auth.login_form'];
}

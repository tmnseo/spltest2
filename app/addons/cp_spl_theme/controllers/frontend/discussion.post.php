<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

use Tygh\Enum\Addons\Discussion\DiscussionObjectTypes;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    return;
}

if ($mode == 'cp_discussion_login_form') {

    if (defined('AJAX_REQUEST') && empty($auth['user_id'])) {

        fn_set_notification('W', __('warning'), __('cp_spl_theme.discussion_login_notification'));

        Tygh::$app['view']->assign([
            'return_url'   => isset($_REQUEST['return_url']) ? $_REQUEST['return_url'] : null,
            'redirect_url' => isset($_REQUEST['redirect_url']) ? $_REQUEST['redirect_url'] : null,
            'is_discussion'  => true,
        ]);

        Tygh::$app['view']->display('addons/cp_spl_theme/blocks/checkout_login_popup.tpl');
        exit;
    }

    fn_set_notification('W', __('warning'), __('authorize_before_order'));

    return [CONTROLLER_STATUS_REDIRECT, 'auth.login_form'];
}

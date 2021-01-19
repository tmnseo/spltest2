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
    if ($mode == 'send_form') {

        $suffix = '';

        if (fn_send_form($_REQUEST['page_id'], empty($_REQUEST['form_values']) ? array() : $_REQUEST['form_values'])) {
            $href = 'pages.view?page_id='.$_REQUEST['page_id'].'&sent=Y';
            
            $vendor_reg_page_id = Registry::get('addons.cp_spl_theme.id_page_profiles_add');
            
            if (!empty($vendor_reg_page_id) && $_REQUEST['page_id'] == $vendor_reg_page_id) {

                $href = "?success_registration=Y";
            }
            
        }

        return !empty($_REQUEST['cp_prev_url']) ? array(CONTROLLER_STATUS_REDIRECT, $_REQUEST['cp_prev_url']) : array(CONTROLLER_STATUS_REDIRECT,  $href);
    }
    return;
    
}

if ($mode == 'view') {
    
    if (!empty($_REQUEST['cp_prev_url'])) {
        Tygh::$app['view']->assign('cp_prev_url',$_REQUEST['cp_prev_url']);
    }
}
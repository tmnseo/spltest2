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
use Tygh\Addons\CpStatusesRules\ServiceProvider;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'update_status') {

        if (Registry::get('runtime.company_id')
            && !empty($_REQUEST['id'])
            && !isset($_REQUEST['cp_checked_shipping_size']) 
            && !empty($_REQUEST['status']) 
            && $_REQUEST['status'] == ServiceProvider::statusCompleted()) {
            
            exit;

        }elseif (!empty($_REQUEST['edost_data'])) {
            
            fn_cp_edost_improvement_save_edost_data($_REQUEST['edost_data']);
        }
    }
}

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
use Tygh\Addons\CpStatusesRules\OrderStatuses\OrderStatuses;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    return;
}

if ($mode == 'details') {

    Tygh::$app['view']->assign('cp_allowed_change_shipping', OrderStatuses::getStatusesForChangeShippingCost());
    Tygh::$app['view']->assign('cp_before_completed_statuses', OrderStatuses::getStatusesBeforeCompleted());
    Tygh::$app['view']->assign('cp_allowed_change_planned_time_issuing_order', OrderStatuses::getStatusesForChangePlannedTimeIssuingOrder());
    
}elseif ($mode == 'manage') {
    Tygh::$app['view']->assign('cp_before_completed_statuses', OrderStatuses::getStatusesBeforeCompleted());
}
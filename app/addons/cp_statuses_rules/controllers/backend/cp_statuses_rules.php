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
use Tygh\Addons\CpStatusesRules\Order\StatusesRulesOrderLogger;
use Tygh\Addons\CpStatusesRules\Order\Order;
use Tygh\Addons\CpStatusesRules\Service;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode == 'go') {

        $cron_pass = ServiceProvider::cronPass();
        return [CONTROLLER_STATUS_REDIRECT, 'cp_statuses_rules.check_statuses&cron_pass='.$cron_pass.'&is_test=Y'];
    }elseif ($mode == 'update_timestamp') {

        if (!empty($_REQUEST['new_timestamp']) && !empty($_REQUEST['order_id'])) {
            Service::updateStatusTimestampForTesting($_REQUEST['new_timestamp'], $_REQUEST['order_id']);
        }

        $url = !empty($_REQUEST['current_url']) ? $_REQUEST['current_url'] : 'cp_statuses_rules.testing_mode';
        return [CONTROLLER_STATUS_REDIRECT, $url];
    }

}

if ($mode == 'check_statuses') {

    $cron_pass = ServiceProvider::cronPass();
    
    if (!empty($_REQUEST['cron_pass']) && $cron_pass == $_REQUEST['cron_pass']) {

        $order_logger = ServiceProvider::getStatusesRulesOrderLogger();

        $orders_for_work = $order_logger->getOrdersForNotifications('M', time());
        
        if (!empty($orders_for_work)) {
            foreach ($orders_for_work as $order_data) {
                if ($order_logger->isTimeToChangeStatus($order_data)){
                    $order_logger->changeStatus($order_data);
                } 
            }
        }
    }

    if (!empty($_REQUEST['is_test']) && $_REQUEST['is_test'] == 'Y') {
        return [CONTROLLER_STATUS_REDIRECT, 'cp_statuses_rules.testing_mode'];  
    }

    exit;

}elseif($mode == 'check_unpaid_orders') {

    $cron_pass = ServiceProvider::cronPass();
    
    if (!empty($_REQUEST['cron_pass']) && $cron_pass == $_REQUEST['cron_pass']) {

        $order_logger = ServiceProvider::getStatusesRulesOrderLogger();

        $orders_for_work = $order_logger->getUnpaidOrdersForNotifications();

        $event_dispatcher = Tygh::$app['event.dispatcher'];
        
        foreach ($orders_for_work as $order_data) {

            if (!empty($order_data['order_id'])) {

                $request_data['order_id'] = $order_data['order_id'];
                $request_data['email'] = Order::getUserEmail($order_data['order_id']);

                $event_dispatcher->dispatch('cp_additional_email_templates.order_unpaid', [
                    'request_data' => $request_data
                ]);
            }
        }       
    }

    exit;

}elseif ($mode == 'testing_mode') {

    $params = $_REQUEST;
    $params['log_type'] = 'M';
    list($log_data, $search) = StatusesRulesOrderLogger::getOrdersForTemplate($params, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);
    
    Tygh::$app['view']->assign('log_data', $log_data);
    Tygh::$app['view']->assign('search', $search);
}


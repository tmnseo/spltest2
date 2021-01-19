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
use Tygh\Addons\CpZohoNotifications\ServiceProvider;
use Tygh\Addons\CpZohoNotifications\Notifications\NotificationsManager;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == 'time_to_check') {

    $cron_pass = Registry::get('addons.cp_zoho_notifications.cron_pass');
    
    if (!empty($_REQUEST['cron_pass']) && $cron_pass == $_REQUEST['cron_pass']) {

        $order_logger = ServiceProvider::getOrderLogger();

        $orders_for_notifications = $order_logger->getOrdersForNotifications();
        
        if (!empty($orders_for_notifications)) {
            foreach ($orders_for_notifications as $notification_data) {
                
                if ($order_logger->isTimeToSend($notification_data)) {
                    
                    $zoho = new Zoho();
                    $contact = $zoho->createContact("ADMIN", Registry::get('settings.Company.company_site_administrator'));

                    if ($contact->id !== false) {
                        $notifications_manager = new NotificationsManager($notification_data);
                        $desc = $notifications_manager->getNotificationDesc();
                        $subj = $notifications_manager->getNotificationSubj();

                        $zoho->newNotification($contact->id, $desc, $subj);
                    }
                }
            }
        }
    }
    exit;
}

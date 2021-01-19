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


namespace Tygh\Addons\CpZohoNotifications\OrderLogger;

use Tygh\Addons\CpZohoNotifications\ServiceProvider;
use Tygh\Addons\CpZohoNotifications\Service;
use Tygh\Addons\CpStatusesRules\Service as StatusesService; 

class OrderLogger
{
    protected $monitored_statuses = [];
    protected $log_data = [];
    const MIN_MONITORED_TIME = SECONDS_IN_DAY;
    const DAYS_BEFORE_SEND = 2;
    const DAYS_BEFORE_SEND_PICKUP = 5;

    function __construct()
    {
        $this->monitored_statuses = array(
            ServiceProvider::statusProcessed(),
            ServiceProvider::statusConfirmed(),
            ServiceProvider::statusPaid(),
            ServiceProvider::statusPaidAfterCancellation(),
            ServiceProvider::statusCompleted(),
            ServiceProvider::statusReceived()
        );    
    }
    public function isMonitoredOrderStatus($order_status)
    {
        return in_array($order_status, $this->monitored_statuses) ? true : false;
    }
    public function createLogData($order_id, $order_status, $log_type = 'Z')
    {   
        $this->log_data = array(
            'order_id' => $order_id,
            'status' => $order_status,
            'update_timestamp' => time(),
            'type' => $log_type
        );
    }
    public function updateLog() 
    {
        db_query("REPLACE INTO ?:cp_order_statuses_log ?e", $this->log_data);
    }
    public function removeLogById($order_id, $log_type = 'Z')
    {
        db_query("DELETE FROM ?:cp_order_statuses_log WHERE order_id = ?i AND type = ?s", $order_id, $log_type);
    }
    public function getOrdersForNotifications($log_type = 'Z', $timestamp = 0)
    {   
        $min_timestamp = !empty($timestamp) ? $timestamp : time() - self::MIN_MONITORED_TIME;
        
        $log_data = db_get_array("SELECT ?:cp_order_statuses_log.*, ?:orders.company_id FROM ?:cp_order_statuses_log LEFT JOIN ?:orders ON ?:orders.order_id = ?:cp_order_statuses_log.order_id WHERE ?:cp_order_statuses_log.update_timestamp < ?i AND ?:cp_order_statuses_log.status IN (?a) AND ?:cp_order_statuses_log.type = ?s", $min_timestamp, $this->monitored_statuses, $log_type);

        return !empty($log_data) ? $log_data : [];
    }
    public function isTimeToSend($notification_data)
    {   
        switch ($notification_data['status']) {
            case ServiceProvider::statusProcessed() :
                $waiting_time = $notification_data['update_timestamp'] + SECONDS_IN_DAY;          
                break;
            case ServiceProvider::statusCompleted() :
                
                $order_info = fn_get_order_info($notification_data['order_id']);

                if (!empty($order_info['shipping'])) {

                    if (Service::isPickupShipping($order_info['shipping'])) {

                        $waiting_time = $notification_data['update_timestamp'] + (SECONDS_IN_DAY * Service::getDaysWithWeekends(self::DAYS_BEFORE_SEND_PICKUP, $notification_data['update_timestamp'], $notification_data['company_id']));
                    }else {
                        $waiting_time = $notification_data['update_timestamp'] + (SECONDS_IN_DAY * Service::getDaysWithWeekends(self::DAYS_BEFORE_SEND, $notification_data['update_timestamp'], $notification_data['company_id']));
                    }    
                }             
                break;
            case ServiceProvider::statusReceived():
                $waiting_time = $notification_data['update_timestamp'] + (SECONDS_IN_DAY * Service::getDaysWithWeekends(1, $notification_data['update_timestamp'], $notification_data['company_id']));
                break;
            default:
                $waiting_time = $notification_data['update_timestamp'] + (SECONDS_IN_DAY * Service::getDaysWithWeekends(self::DAYS_BEFORE_SEND, $notification_data['update_timestamp'], $notification_data['company_id']));
                break;
        }

        return  StatusesService::isTodayTime($waiting_time);
        //return  $waiting_time < time() ? true : false;
    }
}
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


namespace Tygh\Addons\CpStatusesRules\Order;

use Tygh\Addons\CpZohoNotifications\OrderLogger\OrderLogger;
use Tygh\Addons\CpZohoNotifications\Service as ZohoService;
use Tygh\Addons\CpStatusesRules\ServiceProvider;
use Tygh\Addons\CpStatusesRules\Service;
use Tygh\Addons\CpWorkingCalendar\Analizator\DayAnalizator;

class StatusesRulesOrderLogger extends OrderLogger
{
    protected $monitored_statuses = [];
    const PLACED_DAYS_WAITING = 2;
    const CONFIRMED_DAYS_WAITING = 2;
    const HOURS_BEFORE_SENT = 13;

    function __construct()
    {
        $this->monitored_statuses = array(
            ServiceProvider::statusPlaced(),
            ServiceProvider::statusConfirmed(),
            ServiceProvider::statusRefund(), // 3 (customer and vendor x 2)
            ServiceProvider::statusVendorReturn(),
            ServiceProvider::statusReceived()
        );
    }

    public function isTimeToChangeStatus($order_data)
    {   
        
        switch ($order_data['status']) {
            case ServiceProvider::statusPlaced():
                $waiting_time = $order_data['update_timestamp'] + (SECONDS_IN_DAY * ZohoService::getDaysWithWeekends(self::PLACED_DAYS_WAITING, $order_data['update_timestamp'], $order_data['company_id']));
                break;
            case ServiceProvider::statusConfirmed():
                $waiting_time = $order_data['update_timestamp'] + (SECONDS_IN_DAY * ZohoService::getDaysWithWeekends(self::CONFIRMED_DAYS_WAITING, $order_data['update_timestamp'], $order_data['company_id']));
                break;
            case ServiceProvider::statusRefund():
                $waiting_time = time() + 100; // it is not known how long to change the status
                break;
            case ServiceProvider::statusVendorReturn():
                $waiting_time = time() + 100; // it is not known how long to change the status
                break;
            case ServiceProvider::statusReceived():
                 $waiting_time = time() + 100; // it is not known how long to change the status
                break;
        }

        return  Service::isTodayTime($waiting_time);
    }

    public function changeStatus($order_data)
    {   
        if (!empty($order_data['order_id'])) {

            switch ($order_data['status']) {
                case ServiceProvider::statusPlaced():
                    fn_change_order_status($order_data['order_id'], ServiceProvider::statusCancel());
                    break;
                case ServiceProvider::statusConfirmed():
                    fn_change_order_status($order_data['order_id'], ServiceProvider::statusCancel());
                    break;
                case ServiceProvider::statusRefund():
                    fn_change_order_status($order_data['order_id'], ServiceProvider::statusCancelWithRefund());
                    break;
                case ServiceProvider::statusVendorReturn():
                    fn_change_order_status($order_data['order_id'], ServiceProvider::statusCancelWithRefund());
                    break;
                case ServiceProvider::statusReceived():
                    fn_change_order_status($order_data['order_id'], ServiceProvider::statusFinished());
                    break;
            }
        }
    }

    public function getUnpaidOrdersForNotifications()
    {
        //$checked_day = date('Ymd',time());
        //$is_working_day = fn_cp_get_working_day($checked_day);

        /* new functionality with calendars */
        $min_timestamp = time() - (self::HOURS_BEFORE_SENT * SECONDS_IN_HOUR);
        
        $company_ids  = db_get_fields("SELECT company_id FROM ?:cp_order_statuses_log LEFT JOIN ?:orders ON ?:orders.order_id = ?:cp_order_statuses_log.order_id WHERE ?:cp_order_statuses_log.update_timestamp < ?i AND ?:cp_order_statuses_log.status = ?s AND ?:cp_order_statuses_log.type = ?s GROUP BY company_id", $min_timestamp, ServiceProvider::statusConfirmed(), 'M');

        if (!empty($company_ids)) {

            foreach ($company_ids as $company_id) {
                
                $analizator = new DayAnalizator($company_id, time());
                $is_working_day = $analizator->isWorkDay(false);                
                
                /* $is_working_day == 0 => $is_working_day == 1*/

                if ($is_working_day == 1) {
                    $checked_companies[] = $company_id;
                }
            }

        }
        if (!empty($checked_companies)) {
            $log_data = db_get_array("SELECT ?:cp_order_statuses_log.*, ?:orders.company_id FROM ?:cp_order_statuses_log LEFT JOIN ?:orders ON ?:orders.order_id = ?:cp_order_statuses_log.order_id WHERE ?:cp_order_statuses_log.update_timestamp < ?i AND ?:cp_order_statuses_log.status = ?s AND ?:cp_order_statuses_log.type = ?s AND company_id in (?n) GROUP BY company_id", $min_timestamp, ServiceProvider::statusConfirmed(), 'M', $checked_companies);
        }
        /* new functionality with calendars */
        
        return !empty($log_data) ? $log_data : [];
    }
    public static function getOrdersForTemplate($params, $items_per_page, $lang_code = CART_LANGUAGE)
    {   
        $default_params = array(
            'page' => 1,
            'items_per_page' => $items_per_page
        );

        $params = array_merge($default_params, $params);

        $params['log_type'] = !empty($params['log_type']) ? $params['log_type'] : 'Z';

        if (!empty($params['items_per_page'])) {

            $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:cp_order_statuses_log WHERE type = ?s", $params['log_type']);
            $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
        }

        $log_data = db_get_array("SELECT osl.*, sd.description FROM ?:cp_order_statuses_log as osl
            LEFT JOIN ?:statuses as s ON s.status = osl.status AND s.type = ?s
            LEFT JOIN ?:status_descriptions as sd ON sd.status_id = s.status_id AND sd.lang_code = ?s
            WHERE osl.type = ?s $limit", STATUSES_ORDER, $lang_code, $params['log_type']);
        
        return array($log_data, $params);
    }

}
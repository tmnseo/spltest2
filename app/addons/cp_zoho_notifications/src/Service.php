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

namespace Tygh\Addons\CpZohoNotifications;

use Tygh\Registry;
use Tygh\Addons\CpZohoNotifications\ServiceProvider;
use Tygh\Addons\CpWorkingCalendar\Analizator\DayAnalizator;


class Service
{   
    public static function getOrderStatusesForSettings()
    {
        $data = array(
        '' => ' -- '
        );

        foreach (fn_get_statuses(STATUSES_ORDER) as $status) {
            $data[$status['status']] = $status['description'];
        }

        return $data;
    }
    public static function getDaysWithWeekends($working_days_count, $start_timestamp, $company_id = 0)
    {   
        /* DON'T CHANGE IT'S WORK! */
        $added_days = 1;
        $i = 0;
        
        /* check if day of creating orders has been not working with checking worktime */
        $first_day_analizator = new DayAnalizator($company_id, $start_timestamp);
        $first_working_day = $first_day_analizator->isWorkDay();
        if ($first_working_day != 1) {
            $working_days_count ++;
        }
       
        while ($added_days <= $working_days_count) {

            //$checked_day = date('Ymd',$start_timestamp + ($i * SECONDS_IN_DAY));

            $time = $start_timestamp + ($i * SECONDS_IN_DAY);
            
            $analizator = new DayAnalizator($company_id, $time);
            $is_working_day = $analizator->isWorkDay(false);

            //$is_working_day = fn_cp_get_working_day($checked_day);
            
            if ($is_working_day == 1) {
                $added_days++;
            }
            if ($is_working_day == 100) {
                $i = 0;
                break;
            }
            $i++;  
        }
        
        return $i;
    }
    public static function isPickupShipping($shippings)
    {
        foreach ($shippings as $shipping) {
            if (isset($shipping['service_code']) && $shipping['service_code'] == 'pickup') {

               return true;

            } elseif (!empty($shipping['service_code'])) {

               return false;
            }   
        }
        return false;
    }
    public static function getVendorByOrderId($order_id)
    {
        $company_id = db_get_field("SELECT company_id FROM ?:orders WHERE order_id = ?i", $order_id);
        
        return !empty($company_id) ? fn_get_company_name($company_id) : "";
    }
    public static function getUserByOrderId($order_id)
    {   
        $user_name = "";

        $user_id = db_get_field("SELECT user_id FROM ?:orders WHERE order_id = ?i", $order_id);
        if (!empty($user_id)) {
            $user_name = db_get_field("SELECT CONCAT(firstname, ' ', lastname) as user_name FROM ?:users WHERE user_id = ?i",$user_id);
        }
        return $user_name;
    }
    public static function getPaymentNumberByOrderId($order_id)
    {
        $payment_number = db_get_field("SELECT cp_payment_order_number FROM ?:orders WHERE order_id = ?i", $order_id);

        return !empty($payment_number) ? $payment_number : null;
    }
    public static function getReceivedTimeByOrderId($order_id)
    {
        $time = db_get_field("SELECT update_timestamp FROM ?:cp_order_statuses_log WHERE status = ?s AND order_id = ?i AND type = ?s",ServiceProvider::statusReceived(), $order_id, 'Z');

        return !empty($time) ? fn_timestamp_to_date($time) : '';
    }
}
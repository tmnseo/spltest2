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

namespace Tygh\Addons\CpStatusesRules;

use Tygh\Registry;
use Tygh;
use Tygh\Addons\CpZohoNotifications\Service as ZohoService;


class Service
{   
    public static function isPickupShipping($order_id)
    {
        $order_info = fn_get_order_info($order_id);
        
        if (!empty($order_info['shipping'])) {

            return ZohoService::isPickupShipping($order_info['shipping']);
        }

    }
    public static function updateStatusTimestampForTesting($new_timestamp, $order_id)
    {
        db_query("UPDATE ?:cp_order_statuses_log SET update_timestamp = ?i WHERE order_id = ?i AND type = ?s", $new_timestamp, $order_id, 'M');
    }

    public static function isTodayTime($time, $today_time = 0)
    {
        $today_time = empty($today_time) ? time() : $today_time;

        /* Tygh\Addons\CpWorkingCalendar\Analizator\DayAnalizator has menthod getCheckingDayStartTime but it's private */
        $formatter = Tygh::$app['formatter'];

        $current_day_date = $formatter->asDatetime($today_time, "%d/%m/%y");
        $date_format = date_create_from_format("d/m/y", $current_day_date);
        $start_checked_day_time = strtotime(date_format($date_format, 'm/d/y'));
        
        if (($time >= $start_checked_day_time && $time < ($start_checked_day_time + SECONDS_IN_DAY)) || $time < $start_checked_day_time) {
            return true;
        }else {
            return false;
        }
    }
}